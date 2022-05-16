<?php

//Updates any login settings the user has chosen to change for their own login account or household account

include "security.php";

session_start();

include "database.php";
$data = new Database();

if ($_POST["formType"] == "house"){
    $household = $_SESSION["household"];
    $newPassword = password_hash($_POST["NewPassword"], PASSWORD_DEFAULT);
    $newName = $_POST["NewName"];

    if ($newName != $household){
        //updade house name
        $stmt = $data->prepare("UPDATE households SET householdName=:newName WHERE householdName=:household");
        $stmt->bindValue(':newName', $newName, SQLITE3_TEXT);
        $stmt->bindValue(':household', $household, SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $data->prepare("UPDATE chores SET householdName=:newName WHERE householdName=:household");
        $stmt->bindValue(':newName', $newName, SQLITE3_TEXT);
        $stmt->bindValue(':household', $household, SQLITE3_TEXT);
        $stmt->execute();

        $stmt = $data->prepare("UPDATE users SET household=:newName WHERE household=:household");
        $stmt->bindValue(':newName', $newName, SQLITE3_TEXT);
        $stmt->bindValue(':household', $household, SQLITE3_TEXT);
        $stmt->execute();
    }

    $stmt = $data->prepare("UPDATE households SET passcode=:passcode WHERE householdName=:householdName");
    $stmt->bindValue(':passcode', $newPassword, SQLITE3_TEXT);
    $stmt->bindValue(':householdName', $newName, SQLITE3_TEXT);
    $stmt->execute();

    $_SESSION["household"] = h($newName);

}

else if ($_POST["formType"] == "user"){
    $username = $_SESSION["username"];
    $newPassword = password_hash($_POST["NewPassword"], PASSWORD_DEFAULT);
    $newName = $_POST["NewName"];

    if ($newName != $username){
        //updade username
        $stmt = $data->prepare("UPDATE users SET username=:newName WHERE username=:username");
        $stmt->bindValue(':newName', $newName, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();
        
        $stmt = $data->prepare("UPDATE chores SET choreUser=:newName WHERE choreUser=:username");
        $stmt->bindValue(':newName', $newName, SQLITE3_TEXT);
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->execute();
    }

    //update the password
    $stmt = $data->prepare("UPDATE users SET password=:password WHERE username=:username");
    $stmt->bindValue(':password', $newPassword, SQLITE3_TEXT);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->execute();

    $_SESSION["username"] = h($newName);

}

header("Location:index.php");

?>