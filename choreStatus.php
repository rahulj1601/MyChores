<?php

//This script will update the status of a chore

session_start();

include "database.php";
$data = new Database();

$choreStatus = $_POST['choreStatus'];
$choreID = $_POST['choreID'];

//Allocates the corresponding number based on the chore status
if ($choreStatus=="Pending" || $choreStatus=="Started" || $choreStatus=="In Progress" || $choreStatus=="Almost Done"){
    switch ($choreStatus){
        case ("Pending"):
            $chorePercent = 0;
            break;
        case ("Started"):
            $chorePercent = 25;
            break;
        case ("In Progress"):
            $chorePercent = 50;
            break;
        case ("Almost Done"):
            $chorePercent = 75;
            break;
    }
    $stmt = $data->prepare("UPDATE chores SET choreStatus=:chorePercent WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $stmt->bindValue(':chorePercent', $chorePercent, SQLITE3_TEXT);
    $stmt->execute();
}

else if ($choreStatus == "Done"){
    //Change to completed and leave chore up on screen for 1 day then remove
    $stmt = $data->prepare("SELECT * FROM chores WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $choreInfo = $stmt->execute()->fetchArray();

    //need to update deadline date
    $deadlineDate = date_create($choreInfo['deadlineDate']); 
    date_add($deadlineDate, date_interval_create_from_date_string($choreInfo['choreFrequency']." day"));
    $newDeadline=$deadlineDate->format("Y-m-d\TH:i");

    //Updating the notification date
    $notificationDate = date_create($choreInfo['notificationDate']); 
    date_add($notificationDate, date_interval_create_from_date_string($choreInfo['choreFrequency']." day"));
    $newNotification=$notificationDate->format("Y-m-d\TH:i");

    //Create new chore with updated deadline
    $stmt = $data->prepare("INSERT INTO chores VALUES(NULL, :choreName, :choreDescription, :choreFrequency, 0, :deadlineDate, :notificationDate, :choreUser, :householdName)");
    $stmt->bindValue(':choreName', $choreInfo["choreName"], SQLITE3_TEXT);
    $stmt->bindValue(':choreDescription', $choreInfo["choreDescription"], SQLITE3_TEXT);
    $stmt->bindValue(':choreFrequency', $choreInfo["choreFrequency"], SQLITE3_INTEGER);
    $stmt->bindValue(':deadlineDate', $newDeadline, SQLITE3_TEXT);
    $stmt->bindValue(':notificationDate', $newNotification, SQLITE3_TEXT);
    $stmt->bindValue(':householdName', $choreInfo["householdName"], SQLITE3_TEXT);
    $stmt->bindValue(':choreUser', $choreInfo["choreUser"], SQLITE3_TEXT);
    $stmt->execute();

    //Updating choreStatus
    $stmt = $data->prepare("UPDATE chores SET choreStatus=100 WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $stmt->bindValue(':newDeadline', $newDeadline, SQLITE3_TEXT);
    $stmt->execute();
}

else if ($choreStatus == "Skip"){
    //get the chore date and chore frequency to update to new date
    $stmt = $data->prepare("SELECT * FROM chores WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $choreInfo = $stmt->execute()->fetchArray();

    $deadlineDate = date_create($choreInfo['deadlineDate']); 
    date_add($deadlineDate, date_interval_create_from_date_string($choreInfo['choreFrequency']." day"));
    $newDeadline=$deadlineDate->format("Y-m-d\TH:i");

    //Updating the notification date
    $notificationDate = date_create($choreInfo['notificationDate']); 
    date_add($notificationDate, date_interval_create_from_date_string($choreInfo['choreFrequency']." day"));
    $newNotification=$notificationDate->format("Y-m-d\TH:i");

    $stmt = $data->prepare("UPDATE chores SET notificationDate=:newNotification,deadlineDate=:newDeadline,choreStatus=0 WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $stmt->bindValue(':newDeadline', $newDeadline, SQLITE3_TEXT);
    $stmt->bindValue(':newNotification', $newNotification, SQLITE3_TEXT);
    $stmt->execute();
}

//Removes a chore when called, this is when a chore has been selected to be deleted
else if ($choreStatus = "Remove"){
    $stmt = $data->prepare("DELETE FROM chores WHERE id=:choreID");
    $stmt->bindValue(':choreID', $choreID, SQLITE3_TEXT);
    $stmt->execute();
    header("Location:index.php");
}

?>