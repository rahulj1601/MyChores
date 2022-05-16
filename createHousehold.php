<?php

    //If the user has chosen to create a household this will process it and update the database

    session_start();
    
    include "security.php";
    redirectToLogin();

    include "database.php";
    $data = new Database();

    $username = $_SESSION["username"];
    $userID = $_SESSION["id"];
    $passcode = password_hash($_POST["passcode"], PASSWORD_DEFAULT);
    $householdName = $_POST["name"];

    //update the session variable for the household name
    $_SESSION["household"] = $householdName;

    //Update the households table information with new household and household password
    $stmt = $data->prepare("INSERT INTO households VALUES(NULL, :householdName, :passcode)");
    $stmt->bindValue(':householdName', $householdName, SQLITE3_TEXT);
    $stmt->bindValue(':passcode', $passcode, SQLITE3_TEXT);
    $stmt->execute();

    //add the household name to the user information
    $stmt = $data->prepare("UPDATE users SET household=:householdName WHERE id=:userID");
    $stmt->bindValue(':householdName', $householdName, SQLITE3_TEXT);
    $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
    $stmt->execute();

    header('location:index.php');

?>