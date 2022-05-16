<?php

  //If the user is not logged in they will be redirected to the login page
  include "database.php";
  include "security.php";
  include 'usefulFunctions.php';
  redirectToLogin();

?>

<!DOCTYPE html>
<?php include 'header.php'?>
    
  <div class="container-fluid">

    <?php
      $data = new Database();

      $username = $_SESSION["username"];
      $userID = $_SESSION["id"];
      $household = $_SESSION["household"];
      
      //if the user doesn't belong to a household
      if ($household == null){
        include 'nohousehold.php';
      }

      //if the user belongs to a household
      else {
        include 'household.php';
      }
    ?>

  </div>

<?php include 'footer.php' ?>

</div>
