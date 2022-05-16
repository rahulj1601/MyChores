<script src="javascript/household.js"></script>
<script src="javascript/validateModalForms.js"></script>

<div class="grey_container">

    <?php
      //Activate the getting started modal if there are no household chores
      //Updating the database with example chores for a new user
      $stmt = $data->prepare("SELECT COUNT(choreName) FROM chores WHERE householdName=:household");
      $stmt->bindValue(':household', $household, SQLITE3_TEXT);
      $choreCount = $stmt->execute()->fetchArray();
      if ($choreCount["COUNT(choreName)"] == 0){
        $due = date("Y-m-d\TH:i", strtotime('+24 hours'));
        $notification = date("Y-m-d\TH:i", strtotime('+20 hours'));
        $status = 0;
        for ($i=1; $i<=5; $i++){
          $stmt = $data->prepare("INSERT INTO chores VALUES (NULL, 'Example Chore $i', 'This is example chore number $i', 7, :status, :due, :notification, :username, :household)");
          $stmt->bindValue(':household', $household, SQLITE3_TEXT);
          $stmt->bindValue(':username', $username, SQLITE3_TEXT);
          $stmt->bindValue(':due', $due, SQLITE3_TEXT);
          $stmt->bindValue(':notification', $notification, SQLITE3_TEXT);
          $stmt->bindValue(':status', $status, SQLITE3_INTEGER);
          $stmt->execute();
          $status += 25;
          $due = date('Y-m-d\TH:i', strtotime(date("Y-m-d\TH:i"). ' + '.rand(1,30).' days'));
        }
        echo "<script>openModal('Getting Started');</script>";
      }
    ?>

    <!-- List Group Section Displaying Top 5/10 (depending on num of household users) Chores Ordered by Due Date -->

    <div class="row" style="gap:30px;">

      <div class="col-md-4" style="padding:0px;" id="nextChores">
      
        <div class="list-group" style="border-radius:0px;">
        
          <?php
            //If there are more than 3 users in the household the number of chores displayed on the left
            //changes from top 5 chores to top 10 chores
            $stmt = $data->prepare("SELECT COUNT(username) FROM users WHERE household=:householdName");
            $stmt->bindValue(':householdName', $household, SQLITE3_TEXT);
            $userCount = $stmt->execute()->fetchArray()['COUNT(username)'];
            if ($userCount <= 3){$limit = 5;}
            else{$limit = 10;}

            $stmt = $data->prepare("SELECT * FROM chores WHERE householdName=:householdName AND choreStatus!=100 ORDER BY date(deadlineDate) ASC LIMIT :limit");
            $stmt->bindValue(':householdName', $household, SQLITE3_TEXT);
            $stmt->bindValue(':limit', $limit, SQLITE3_TEXT);
            $allHouseholdChores = $stmt->execute();

            //Generating each list group item using the data taken from the database
            $html="";
            while ($chore = $allHouseholdChores->fetchArray()){
              $currentDate = date_create(date("Y-m-d\TH:i"));
              $deadlineDate = date_create($chore["deadlineDate"]);

              if ($currentDate < $deadlineDate){
                $datediff = date_diff($currentDate, $deadlineDate)->format('%a');
              }
              else{
                $datediff = -1;
              }
              
              $html.="<a class='list-group-item list-group-item-action' onclick='openModal(".'"Edit",'.$chore['id'].")'>
                      <div class='d-flex justify-content-between'>";

              $html.="<h5 class='mb-1'>".h($chore["choreName"])."</h5>";

              $html.="<small class='text-muted'>";

              if ($datediff < 0){$html.="<span style='color:red;'>Overdue</span>";}
              else if ($datediff == 0){$html.= "<span class='timer'>".date_diff(new DateTime("now"), $deadlineDate)->format('%h:%i:%s')."</span>";}
              else if ($datediff == 1){$html.="1 Day Left";}
              else{$html.=$datediff." Days Left";}

              $html.="</small></div>";

              $html.= "<p class='mb-1'><div class='progress'>
                      <div class='progress-bar progress-bar-striped progress-bar-animated bg-success' role='progressbar' aria-valuenow=".$chore["choreStatus"]." aria-valuemin='0' aria-valuemax='100' style='width: ".$chore["choreStatus"]."%'>"
                      .progressBar($chore['choreStatus']).
                      "</div></p>";

              $html.="<small style='color:#64a874;'>".h($chore["choreUser"])."</small>";

              if ($datediff < 0){
                $html.="<button id='skipChore' onclick='updateStatus(".'"Skip",'.$chore["id"].")'>
                          <svg xmlns='http://www.w3.org/2000/svg' class='bi bi-reply' viewBox='0 0 16 16'>
                            <title>Skip Overdue Chore</title>  
                            <path d='M6.598 5.013a.144.144 0 0 1 .202.134V6.3a.5.5 0 0 0 .5.5c.667 0 2.013.005 3.3.822.984.624 1.99 1.76 2.595 3.876-1.02-.983-2.185-1.516-3.205-1.799a8.74 8.74 0 0 0-1.921-.306 7.404 7.404 0 0 0-.798.008h-.013l-.005.001h-.001L7.3 9.9l-.05-.498a.5.5 0 0 0-.45.498v1.153c0 .108-.11.176-.202.134L2.614 8.254a.503.503 0 0 0-.042-.028.147.147 0 0 1 0-.252.499.499 0 0 0 .042-.028l3.984-2.933zM7.8 10.386c.068 0 .143.003.223.006.434.02 1.034.086 1.7.271 1.326.368 2.896 1.202 3.94 3.08a.5.5 0 0 0 .933-.305c-.464-3.71-1.886-5.662-3.46-6.66-1.245-.79-2.527-.942-3.336-.971v-.66a1.144 1.144 0 0 0-1.767-.96l-3.994 2.94a1.147 1.147 0 0 0 0 1.946l3.994 2.94a1.144 1.144 0 0 0 1.767-.96v-.667z'/>
                          </svg>
                        </button>";
              }

              $html.="</a>";

            }

            echo $html;
          ?>

        </div>

      </div>
      
      <!-- Second Column with carousel of current user chores and then section to view different chores -->

      <div class="col">

        <!-- Start of the code for the carousel itself -->

        <div id="carouselControls" class="carousel slide text-center" data-bs-interval="false"> 

          <div class="carousel-indicators" style="bottom:-12px;">

            <?php
            //Generating the carousel buttons at the bottom of the the carousel div
              $stmt = $data->prepare("SELECT COUNT(choreName) FROM chores WHERE choreUser=:username AND choreStatus!=100");
              $stmt->bindValue(':username', $username, SQLITE3_TEXT);
              $choreCount = $stmt->execute()->fetchArray();
              $html = '';
              if ($choreCount['COUNT(choreName)'] >=1 ){
                $html.='<button type="button" data-bs-target="#carouselControls" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>';
                for ($i=2; $i<=$choreCount['COUNT(choreName)']; $i++){
                  $html.= "<button type='button' data-bs-target='#carouselControls' data-bs-slide-to='".($i-1)."' aria-label='Slide ".$i."'></button>";
                }
              }
              echo $html;
            ?>

          </div>

          <div class="carousel-inner">

            <?php
            //extracts data from the database to form the main content of the carousel
              $stmt = $data->prepare("SELECT * FROM chores WHERE choreUser=:username AND choreStatus!=100 ORDER BY date(deadlineDate)");
              $stmt->bindValue(':username', $username, SQLITE3_TEXT);
              $myChores = $stmt->execute();
              $html="";

              while ( $chore = $myChores->fetchArray() ){
                $html.="<div class='carousel-item'>

                          <div id='carouselEditChore' onclick='openModal(".'"Edit",'.$chore['id'].")'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
                              <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/>
                            </svg>
                          </div>

                          <h3>".h($chore["choreName"])."</h3>";

                //implementing the dropdown where the user can update the status of one of their chores
                $html.="<div class='dropdown'>

                          <button class='settings'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='white' class='bi bi-three-dots' viewBox='0 0 16 16'>
                            <path d='M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z'/>
                            </svg>
                          </button>

                          <div class='dropdown_content'>
                            <div id='mainButtons'>
                              <button onclick='updateStatus(".'"Skip",'.$chore["id"].")'>Skip</button>
                              <button onclick='updateStatus(".'"Done",'.$chore["id"].")'>Mark as Done</button>
                              <button id='updateButton' onclick='statusButtons()'>< Update Status</button>
                            </div>

                            <div class='statusButtons'>
                              <button onclick='updateStatus(".'"Pending",'.$chore["id"].")'>Pending</button>
                              <button onclick='updateStatus(".'"Started",'.$chore["id"].")'>Started</button>
                              <button onclick='updateStatus(".'"In Progress",'.$chore["id"].")'>In Progress</button>
                              <button onclick='updateStatus(".'"Almost Done",'.$chore["id"].")'>Almost Done</button>
                            </div>

                          </div>

                        </div>";

                $html.="<p>".h($chore["choreDescription"])."</p>";
                
                $deadlineDate = date('Y-m-d', strtotime($chore["deadlineDate"]));
                $html.="<p>";
                if ((date("Y-m-d\TH:i")) >= $chore["deadlineDate"]){$html.="Overdue";}
                else if((date("Y-m-d")) == $deadlineDate) {$html.="Due Today";}
                else if(date("Y-m-d", strtotime('tomorrow')) == $deadlineDate){$html.="Due Tomorrow";}
                else{$html.=date('l, F jS Y \a\t g:ia', strtotime($chore["deadlineDate"]));}
                $html.="</p>";

                $html.="<div class='progress'>
                        <div class='progress-bar progress-bar-striped progress-bar-animated bg-warning' role='progressbar' aria-valuenow=".$chore["choreStatus"]." aria-valuemin='0' aria-valuemax='100' style='width: ".$chore["choreStatus"]."%'>"
                        .progressBar($chore['choreStatus']).
                        "</div></div>";
              }

              echo $html;
            ?>
            
          </div>
          
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselControls"  data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselControls"  data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>

        </div>

        <!-- The menu where all chores can be viewed by clicking on the corresponding user -->
        <!-- You can also view completed chores, and these can then be cleared from the database -->

        <div id="allChores">
          
          <nav id="vertical-menu">

            <button id='Donebutton' onclick='showChores("Done")'>Done</button>

            <?php
              $stmt = $data->prepare("SELECT username FROM users WHERE household=:household");
              $stmt->bindValue(':household', $household, SQLITE3_TEXT);
              $householdUsers = $stmt->execute();
              $html="";
              while ($householdUser = $householdUsers->fetchArray()){
                $html.="<button id='".h($householdUser['username'])."button' onclick='showChores(".'"'.h($householdUser['username']).'"'.")'>"
                        .h($householdUser['username']).
                        "</button>";
              }
              echo $html;
            ?>

          </nav>

          <?php
            $stmt = $data->prepare("SELECT * FROM chores WHERE choreStatus=100 AND householdName=:household ORDER BY choreName COLLATE NOCASE ASC");
            $stmt->bindValue(':household',$household,SQLITE3_TEXT);
            $completedChores = $stmt->execute();
            $html="<div class='choreGroup' id='Done' style='display:none'>";

            while ($doneChore = $completedChores->fetchArray()){
              $html.="<div class='singleChore' onclick='openModal(".'"View",'.$doneChore['id'].")'>
                      <span style='color:#495057;font-size:18px;'>".h($doneChore["choreName"])."</span>
                      <br>".h($doneChore["choreUser"])."
                      </div>";
            }

            $html.='<div class="scrollDownArrow">
                      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                      </svg>
                    </div>';

            $html.="</div>";

            $stmt = $data->prepare("SELECT username FROM users WHERE household=:household");
            $stmt->bindValue(':household', $household, SQLITE3_TEXT);
            $householdUsers = $stmt->execute();

            while ($householdUser = $householdUsers->fetchArray()){

              $html.="<div class='choreGroup' id='".h($householdUser["username"])."' style='display:none'>";
              $stmt = $data->prepare("SELECT * FROM chores WHERE choreUser=:username AND choreStatus!=100 ORDER BY date(deadlineDate)");
              $stmt->bindValue(':username', $householdUser["username"], SQLITE3_TEXT);
              $choreData = $stmt->execute();
              while ($userChore = $choreData->fetchArray()){
                $html.="<div class='singleChore' onclick='openModal(".'"Edit",'.$userChore['id'].")'>
                          <span style='color:#495057;font-size:18px;'>".h($userChore["choreName"])."</span>
                          <br>".h($userChore["choreDescription"])."<br>";
                
                switch ($userChore['choreStatus']){
                  case(0):
                    $html.="Pending";
                    break;
                  case(25):
                    $html.="Started";
                    break;
                  case(50):
                    $html.="In Progress";
                    break;
                  case(75):
                    $html.="Almost Done";
                    break;
                }
                          
                $html.="<div class='singleChoreDate'>Due ".date('d/m/Y', strtotime($userChore["deadlineDate"]))."</div>
                        </div>";
              }

              $html.='<div class="scrollDownArrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                      </div>';

              $html.="</div>";

            }

            echo $html;

          ?>

          <button onclick="location.href='clearDoneChores.php'" id='clearAllButton'>Clear</button>

        </div>

      </div>

    </div>

</div>

<!-- Generating the corner menu with various options that can be clicked -->
<div class="sticky">
  <div id='cornerMenu'>
    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-list" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
    </svg>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-x" viewBox="0 0 16 16">
    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg>
  </div>

  <div id="menuItems">
    <div class="singleItem" style="background-color:#00A92A;" onclick="openModal('Add')">
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-plus" viewBox="0 0 16 16">
        <title>Add New Chore</title>
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
      </svg>
    </div>

    <div class="singleItem" onclick="openModal('Exit')">
      <svg style="margin:0px 3px 1px 0px;" xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="white" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
        <title>Exit Household</title>
        <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
        <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
      </svg>
    </div>
    
    <div class="singleItem" onclick="openModal('Getting Started')">
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-question" viewBox="0 0 16 16">
        <title>Need Help?</title>
        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
      </svg>
    </div>
    
    <div class="singleItem" onclick="openModal('Settings')">
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi bi-gear" viewBox="0 0 16 16">
        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
      </svg>
    </div>

  </div>
</div>

<!-- Empty Modal Shell (AJAX is used to fill its contents) -->
<div id="emptyModal" class="modalContainer" style="display: none;">
    <div class="modalContentWrapper">

      <div class="modalHeader">
        <h5></h5>
        <div id="closeModal" onclick="closeModal()">
          <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </div>
      </div>
      
      <div class="modalBody">
        
      </div>

      <div class="modalFooter">
        
      </div>
    </div>
</div>

<!-- Forms the walkthrough for the user to be able to learn what each part of the application shows and does -->
<div id='walkthrough'>
  <div style="position:absolute;top:5px;left:5px;font-size:10px;">Drag Me!</div>
  <div onclick="closeWalkthrough()" id="closeWalkthrough">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-x" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg>
  </div>
  <div id="walkthroughCarousel" class="carousel slide text-center" data-bs-interval="false">
    
    <div class="carousel-inner">
      <div class="carousel-item active" id="listGroup">
        <p>
        View the households top priority chores ordered by due date. 
        You can edit them by clicking on them and also skip overdue chores.
        You will also see a timer on the last 24 hours of a chore!
        </p>
      </div>
      <div class="carousel-item" id="carousel">
        <p>
          Look at your own chores ordered by due date.
          Edit them if needed by clicking the pencil icon and also update the 
          status if you have completed more of the chore to let everyone know 
          where your at.
        </p>
      </div>
      <div class="carousel-item" id="chores">
        <p>
        View all of the chores in the household and see exactly who is doing what. 
        Edit them if you need to. If there is a completed chore section click on it 
        to see all of the completed chores and who has completed what!
        </p>
      </div>
    </div>

    <button onclick="walkthroughDelay()" class="carousel-control-prev" type="button" data-bs-target="#walkthroughCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button onclick="walkthroughDelay()" class="carousel-control-next" type="button" data-bs-target="#walkthroughCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>