<?php

    //Allows a user to enter the household if they have entered the correct details

    session_start();

    include "security.php";
    redirectToLogin();

    include "database.php";
    $data = new Database();

    $householdName = $_POST["name"];
    $passcode = $_POST["passcode"];
    $userID = $_SESSION["id"];

    $stmt = $data->prepare("SELECT passcode FROM households WHERE householdName=:householdName");
    $stmt->bindValue(':householdName', $householdName, SQLITE3_TEXT);
    $household = $stmt->execute()->fetchArray();

    if ($household!=null && password_verify($passcode,$household["passcode"])) {
        $_SESSION['household'] = $householdName;
        $stmt = $data->prepare("UPDATE users SET household=:householdName WHERE id=:userID");
        $stmt->bindValue(':householdName', $householdName, SQLITE3_TEXT);
        $stmt->bindValue(':userID', $userID, SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    header('location:index.php');
?>