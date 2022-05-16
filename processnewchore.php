<?php

    session_start();

    include "database.php";
    include "security.php";
    $data = new Database();

    $household = $_SESSION["household"];
    $choreName = $_POST["choreName"];
    $choreDescription = $_POST["choreDescription"];
    $choreFrequency = $_POST["choreFrequency"];
    $deadlineDate = $_POST["deadlineDate"];
    $notificationDate = $_POST["notificationDate"];

    //if we are editing the chore then the previous data is deleted and we create a new chore using old data
    if (isset($_POST['choreID'])){
        $stmt = $data->prepare("DELETE FROM chores WHERE id=:choreID");
        $stmt->bindValue(':choreID', $_POST["choreID"], SQLITE3_TEXT);
        $stmt->execute();
    }

    $choreUser = null;

    //randomly select the chore user
    if ($_POST["choreUser"] == "random"){
        $stmt = $data->prepare("SELECT * FROM users WHERE household=:household");
        $stmt->bindValue(':household', $household, SQLITE3_TEXT);
        $householdUserList = $stmt->execute();
        $householdUsersChoreCount = array();
        while ($householdUser = $householdUserList->fetchArray()){
            $stmt = $data->prepare("SELECT COUNT(choreName) FROM chores WHERE choreUser=:choreUser");
            $stmt->bindValue(':choreUser', $householdUser['username'], SQLITE3_TEXT);
            $userChoreCount = $stmt->execute()->fetchArray();
            $householdUsersChoreCount[$householdUser['username']] = $userChoreCount['COUNT(choreName)'];
        }
        //forming an array of keys
        $keys = array_keys($householdUsersChoreCount); 
        //shuffling the keys
        shuffle($keys); 
        $shuffledArray = array(); 
        //Reforming an array with the keys and their corresponding values
        foreach ($keys as $key) { 
            $shuffledArray[$key] = $householdUsersChoreCount[$key]; 
        }
        //choosing a user with the least number of chores
        //if more than one user has the least number of chores
        //this will select the first user with the minimum chore count
        $choreUser = array_keys($shuffledArray, min($shuffledArray))[0];
    }

    //allocate the chore user if the chore user box is filled in
    else if (isset($_POST["choreUser"])){
        $choreUser = h($_POST["choreUser"]);
    }

    $stmt = $data->prepare("INSERT INTO chores VALUES(NULL, :choreName, :choreDescription, :choreFrequency, 0, :deadlineDate, :notificationDate, :choreUser, :householdName)");
    $stmt->bindValue(':choreName', $choreName, SQLITE3_TEXT);
    $stmt->bindValue(':choreDescription', $choreDescription, SQLITE3_TEXT);
    $stmt->bindValue(':choreFrequency', $choreFrequency, SQLITE3_INTEGER);
    $stmt->bindValue(':deadlineDate', $deadlineDate, SQLITE3_TEXT);
    $stmt->bindValue(':notificationDate', $notificationDate, SQLITE3_TEXT);
    $stmt->bindValue(':householdName', $household, SQLITE3_TEXT);
    $stmt->bindValue(':choreUser', $choreUser, SQLITE3_TEXT);
    $stmt->execute();

    header('location:index.php');

?>