<?php

//Returns the content of the modal to an Ajax request which then puts the content in the correct places
//of the modal so that it is displayed correctly

switch ($_POST['name']){
    case("Getting Started"):
        gettingStarted();
        break;
    case("Edit"):
        editModal();
        break;
    case("Delete"):
        confirmDelete();
        break;
    case("Exit"):
        confirmExit();
        break;
    case("Add"):
        addModal();
        break;
    case("View"):
        viewModal();
        break;
    case("Settings"):
        settingsModal();
        break;
}

//Responds to the AJAX call with the getting started modal content
function gettingStarted(){
    include "security.php";

    session_start();

    $header = "Getting Started";

    $body = '
    <h3>Welcome to '.h($_SESSION['household']).'!</h3>
    <p style="margin-top:20px;">Here are a few things you can do:</p>
    <ul>
        <li>Click the pencil icon or the chore itself to <strong>edit it</strong></li>
        <li>Click on a chore to <strong>view the chore details</strong></li>
        <li>Click on the plus icon in the menu to <strong>add new chores</strong></li>
        <li><strong>Exit your household</strong> by going to the exit icon in the menu</li>
        <li><strong>Change any login details</strong> by going to the settings icon</li>
    </ul>
    <p">Now go ahead and <strong>edit the example chores</strong>, <strong>add a chore</strong> or <strong>view the walkthrough!</strong></p>
    ';

    $footer = "
    <button style='background-color:#0404e0;' onclick='closeModal();viewWalkthrough();'>View Walkthrough</button>
    <button style='background-color:#00A92A;' onclick='openModal(".'"Add"'.");'>Create a Chore</button>
    <button style='background-color:#6D757D;' onclick='closeModal()'>Close</button>";

    echo json_encode(array("header"=>$header, "body"=> $body, "footer"=> $footer));exit();
}

function editModal(){
    //Returns the HTML to be included in the edit modal for a chore
    //This will pre-load data so they can be edited by the user if needed

    session_start();

    include "database.php";
    include "security.php";
    $data = new Database();

    $choreID = $_POST["choreid"];
    
    $stmt = $data->prepare("SELECT * FROM chores WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_INTEGER);
    $choreData = $stmt->execute()->fetchArray();

    $header = "Edit Chore";

    $body = '<form method="post" action="processnewchore.php" id="editChore" class="modalForm" onchange="validateForm('."'edit'".')" onsubmit="return validateForm('."'edit'".')" novalidate>';
    $body.='<input type="hidden" id="hiddenID" name="choreID" value='.$choreData['id'].'>
                <div class="input-group mb-3">
                    <input class="form-control" type="text" placeholder="Chore Name" name="choreName" value="'.h($choreData["choreName"]).'" id="editName">
                </div>
                <div class="input-group">
                    <textarea class="form-control" placeholder="Chore Description" name="choreDescription" id="editDescription">'.h($choreData["choreDescription"]).'</textarea>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Repeat Every</span>
                    <input type="number" class="form-control" value='.$choreData["choreFrequency"].' name="choreFrequency" id="editFrequency">
                    <span class="input-group-text" id="basic-addon2">Day(s)</span>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon3">Deadline Date and Time:</span>
                    <input id="editDeadline" value="'.$choreData["deadlineDate"].'" type="datetime-local" name="deadlineDate" class="form-control" aria-describedby="basic-addon3">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon4">Notification Date and Time:</span>
                    <input id="editNotification" value="'.$choreData["notificationDate"].'" type="datetime-local" name="notificationDate" class="form-control" aria-describedby="basic-addon4">
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="editUser">Select Chore User or Randomly Allocate:</label>
                    <select name="choreUser" id="editUser" class="form-select" aria-label="Default select example" id="editUser">
                    <option value="'.h($choreData['choreUser']).'" selected>'.h($choreData['choreUser']).'</option>';
                    $stmt = $data->prepare("SELECT * FROM users WHERE household=:household");
                    $stmt->bindValue(':household', $_SESSION['household'], SQLITE3_TEXT);
                    $householdUserList = $stmt->execute();
                    while ($user = $householdUserList->fetchArray()){
                        if ($user['username'] != $choreData['choreUser']){
                            $body.="<option value='".h($user['username'])."'>".h($user['username'])."</option>";
                        }
                    }
                    $body.= '<option value="random">Randomly Allocate Chore</option>
                    </select>
                </div>
            </form>
            <div class="error"><small></small></div>';
    
    $footer = '
    <button style="background-color:#dc3545;" type="button" onclick="openModal('."'Delete',".$choreData['id'].')">Delete</button>
    <button style="background-color:#00A92A;" type="submit" form="editChore">Save changes</button>
    ';

    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

//Returns the modal data for confirming deletion of a chore
function confirmDelete(){
    session_start();
    include "security.php";
    $username = h($_SESSION['username']);

    $header = "Delete Chore";

    $body = '
    <p>Are you sure you want to delete this chore?</p>
    <form method="post" action="choreStatus.php" id="exitHousehold">
        <input type="hidden" name="choreID" value='.$_POST['choreid'].'>
        <input type="hidden" name="choreStatus" value="Remove">
    </form>
    ';

    $footer = '
    <button type="submit" style="background-color:#00A92A;" form="exitHousehold">Yes</button>
    <button type="button" style="background-color:#6D757D;" onclick="closeModal()">No</button>
    ';

    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

//Returns the content required for the modal where a chore can be added
function addModal(){
    session_start();

    include "database.php";
    include "security.php";
    $data = new Database();

    $header = "Add a Chore";

    $body = '
        <form method="post" action="processnewchore.php" id="addChore" class="modalForm" onchange="validateForm('."'add'".')" onsubmit="return validateForm('."'add'".')" novalidate>
          
          <div class="input-group mb-3">
            <input class="form-control" type="text" name="choreName" placeholder="Chore Name" id="addName">
          </div>
          
          <div class="input-group">
            <textarea class="form-control" name="choreDescription" placeholder="Chore Description" id="addDescription"></textarea>
          </div>
          
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Repeat Every</span>
            <input class="form-control" type="number" name="choreFrequency" id="addFrequency">
            <span class="input-group-text" id="basic-addon2">Day(s)</span>
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon3">Deadline Date and Time:</span>
            <input id="addDeadline" type="datetime-local" name="deadlineDate" class="form-control" aria-describedby="basic-addon3">
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon4">Notification Date and Time:</span>
            <input id="addNotification" type="datetime-local" name="notificationDate" class="form-control" aria-describedby="basic-addon4">
          </div>
          
          <div class="input-group mb-3">
            <select name="choreUser" id="addUser" class="form-select" aria-label="Default select example">
              <option value="" disabled selected>Select Chore User or Random Allocation</option>
              <option value="random">Randomly Allocate Chore</option>';
                $stmt = $data->prepare("SELECT * FROM users WHERE household=:household");
                $stmt->bindValue(':household', $_SESSION['household'], SQLITE3_TEXT);
                $householdUserList = $stmt->execute();
                while ($user = $householdUserList->fetchArray()){
                  $body.="<option value='".h($user['username'])."'>".h($user['username'])."</option>";
                }
            $body.='</select>
          </div>
        </form>
        <div class="error"><small></small></div>';

    $footer = "
    <button style='background-color:#6D757D;' onclick='closeModal()'>Close</button>
    <button type='submit' style='background-color:#00A92A;' form='addChore'>Create Chore</button>";

    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

//Sends the modal for viewing information about a chore
//This will get data for the chore from the database
function viewModal(){
    session_start();

    include "security.php";
    include "database.php";
    include "usefulFunctions.php";
    $data = new Database();

    $choreID = $_POST["choreid"];
    
    $stmt = $data->prepare("SELECT * FROM chores WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_INTEGER);
    $choreData = $stmt->execute()->fetchArray();

    $header = "Chore Info";

    $body="<div style='background-color:#23993E;color:white;padding:10px;'>";
    $body.= "<div style='margin-bottom:20px;background-color:white;color:black;padding:10px;text-align:center;'><h3>".h($choreData["choreName"])."</h3>";
    $body.= "<p>".h($choreData["choreDescription"])."</p>";
    $body.= "<p class='mb-1'><div class='progress'>
            <div class='progress-bar progress-bar-striped progress-bar-animated bg-success' role='progressbar' aria-valuenow=".$choreData["choreStatus"]." aria-valuemin='0' aria-valuemax='100' style='width: ".$choreData["choreStatus"]."%'>"
            .progressBar($choreData['choreStatus']).
            "</div></p></div>";
    $body.= "<p>Repeats every ".$choreData["choreFrequency"]." days</p>";
    $body.= "<p>Deadline Date and Time: ".date('F jS Y \a\t g:ia', strtotime($choreData["deadlineDate"]))."</p>";
    $body.= "<p>Notification Date and Time: ".date('F jS Y \a\t g:ia', strtotime($choreData["notificationDate"]))."</p>";
    $body.= "<p>Selected Chore User: ".h($choreData["choreUser"])."</p>";
    $body.= "</div>";

    $footer = "<button style='background-color:#6D757D;' onclick='closeModal()'>Close</button>";

    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

//Sends the data for confirming that the user exits the household
function confirmExit(){
    session_start();
    include "security.php";
    
    $username = h($_SESSION['username']);
    $header = "Exit Household";
    $body = '
    <p>Are you sure you want to exit the household?</p>
    <form method="post" action="processHouseholdExit.php" id="exitHousehold">
        <input type="hidden" name="username" value='.$username.'>
    </form>
    ';
    $footer = '
    <button type="submit" style="background-color:#00A92A;" form="exitHousehold">Yes</button>
    <button type="button" style="background-color:#6D757D;" onclick="closeModal()">No</button>
    ';
    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

//The HTML for changing the users login information and household information
function settingsModal(){
    session_start();

    $header = 'Settings';

    $body = '
    <h6 style="text-align:center;">Change Household Name and Passcode</h6>
    <form method="post" action="updateSettings.php" id="houseForm" class="modalForm" onchange="validateSettings('."'house'".')" onsubmit="return validateSettings('."'house'".')" novalidate>
        <input type="hidden" name="formType" value="house">
        <div class="input-group mb-3">
            <input class="form-control" type="password" name="OldPassword" placeholder="Current Passcode" id="houseOldPassword" autocomplete="new-password">
        </div>

        <div id="houseDetails" style="display:none;">
            <div class="input-group mb-3">
                <input class="form-control" type="text" name="NewName" placeholder="New Household Name" id="houseNewName" value="'.$_SESSION['household'].'">
            </div>
            <div class="input-group mb-3">
                <input class="form-control" type="password" name="NewPassword" placeholder="New Password" id="houseNewPassword">
            </div>
            <div class="input-group mb-3">
                <input class="form-control" type="password" name="NewPasswordConfirm" placeholder="Confirm New Password" id="houseNewPasswordConfirm">
            </div>
            <button type="submit" class="btn" style="color:white;background-color:#00A92A;" form="houseForm">Update</button>
        </div>

    </form>

    <h6 style="margin-top:30px;text-align:center;">Change Username and Password</h6>
    <form method="post" action="updateSettings.php" id="userForm" class="modalForm" onchange="validateSettings('."'user'".')" onsubmit="return validateSettings('."'user'".')" novalidate>
        <input type="hidden" name="formType" value="user">
        <div class="input-group mb-3">
            <input class="form-control" type="password" name="OldPassword" placeholder="Current Password" id="userOldPassword" autocomplete="new-password">
        </div>

        <div id="userDetails" style="display:none;">
            <div class="input-group mb-3">
                <input class="form-control" type="text" name="NewName" placeholder="New Username" id="userNewName" value="'.$_SESSION['username'].'">
            </div>
            <div class="input-group mb-3">
                <input class="form-control" type="password" name="NewPassword" placeholder="New Password" id="userNewPassword">
            </div>
            <div class="input-group mb-3">
                <input class="form-control" type="password" name="NewPasswordConfirm" placeholder="Confirm New Password" id="userNewPasswordConfirm">
            </div>
            <button type="submit" class="btn" style="color:white;background-color:#00A92A;" form="userForm">Update</button>
        </div>
    </form>
    ';

    $footer = '<button type="button" style="background-color:#6D757D;" onclick="closeModal()">Close</button>';

    echo json_encode(array("header"=>$header, "body"=>$body, "footer"=>$footer));exit();
}

?>