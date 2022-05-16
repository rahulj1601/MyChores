<?php

//Validates various forms depending on which form has been filled out

switch ($_POST['function']){
    case("checkUsername"):
        checkUsername();
        break;
    case("checkLogin"):
        checkLogin();
        break;
    case("checkHousehold"):
        checkHousehold();
        break;
    case("checkHouseholdLogin"):
        checkHouseholdLogin();
        break;
    case("housePassword"):
        checkHouseDetails();
        break;
    case("userPassword"):
        checkUserDetails();
        break;
    case("userName"):
        isTakenUser();
        break;
    case("houseName"):
        isTakenHouse();
        break;
}

//This checks if a username is taken
function checkUsername(){
    $checkUsername = $_POST['name'];
    include "database.php";
    $data = new Database();
    $stmt = $data->prepare("SELECT COUNT(*) FROM users WHERE username=:checkUsername");
    $stmt->bindValue(':checkUsername', $checkUsername, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray();
    if ($user["COUNT(*)"] > 0){
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This checks if the user has entered the correct login details
function checkLogin(){
    $checkUsername = $_POST['username'];
    $checkPassword = $_POST['password'];

    include "database.php";
    $data = new Database();
    $stmt = $data->prepare("SELECT * FROM users WHERE username=:checkUsername");
    $stmt->bindValue(':checkUsername', $checkUsername, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray();

    if ( $user!=null && password_verify($checkPassword,$user["password"]) ) {
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This checks if a household name is taken
function checkHousehold(){
    $checkHousehold = $_POST['name'];

    include "database.php";
    $data = new Database();
    $stmt = $data->prepare("SELECT COUNT(*) FROM households WHERE householdName=:checkHousehold");
    $stmt->bindValue(':checkHousehold', $checkHousehold, SQLITE3_TEXT);
    $household = $stmt->execute()->fetchArray();
    
    if ($household["COUNT(*)"] > 0){
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This checks if the user has entered the correct household login details
function checkHouseholdLogin(){
    $checkName = $_POST['name'];
    $checkPasscode = $_POST['passcode'];

    include "database.php";
    $data = new Database();
    
    $stmt = $data->prepare("SELECT passcode FROM households WHERE householdName=:householdName");
    $stmt->bindValue(':householdName', $checkName, SQLITE3_TEXT);
    $household = $stmt->execute()->fetchArray();

    if ($household!=null && password_verify($checkPasscode,$household["passcode"])) {
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This checks if the user has entered the correct password for the household they are logged into
//This is used for the changing household login details modal
function checkHouseDetails(){
    session_start();

    $checkPassword = $_POST['password'];
    $houseName = $_SESSION["household"];

    include "database.php";
    $data = new Database();
    $stmt = $data->prepare("SELECT passcode FROM households WHERE householdName=:householdName");
    $stmt->bindValue(':householdName', $houseName, SQLITE3_TEXT);
    $householdData = $stmt->execute()->fetchArray();

    if ( $householdData!=null && password_verify( $checkPassword, $householdData["passcode"] ) ) {
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This checks if the user has entered the correct password for their account that they are logged into
//This is used for the changing user login details modal
function checkUserDetails(){
    session_start();

    $checkPassword = $_POST['password'];
    $username = $_SESSION["username"];

    include "database.php";
    $data = new Database();
    $stmt = $data->prepare("SELECT * FROM users WHERE username=:username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray();

    if ($user!=null && password_verify($checkPassword,$user["password"])) {
        echo true;exit();
    }
    else{
        echo false;exit();
    }
}

//This function checks if the name the user wishes to change to is taken
//They are able to keep their current name so this function returns false if they have entered
//their current username
function isTakenUser(){
    session_start();

    $checkUsername = $_POST['name'];
    $username = $_SESSION["username"];

    if ($checkUsername == $username){
        echo false;exit();
    }
    else {
        checkUsername();
    }
}

//This function will check if the household name has been taken
//This will exclude the current household name they have chosen so they can keep the current household name
function isTakenHouse(){
    session_start();

    $checkHousehold = $_POST['name'];
    $household = $_SESSION["household"];

    if ($checkHousehold == $household){
        echo false;exit();
    }
    else {
        checkHousehold();
    }
}

?>