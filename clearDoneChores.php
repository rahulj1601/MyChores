<?php

    //This will clear all completed chores belonging to the current 
    //household who choose to clear their completed chores

    session_start();

    include "database.php";
    $data = new Database();

    $household = $_SESSION['household'];

    $stmt = $data->prepare("DELETE FROM chores WHERE choreStatus=100 AND householdName=:household");
    $stmt->bindValue(':household',$household,SQLITE3_TEXT);
    $stmt->execute();

    header("Location:index.php");

?>