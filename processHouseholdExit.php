<?php

    //When user leaves the household their chores are removed from the database
    //The users household is set to null in the database which will mean they must enter a household 
    //on the index.php page

    session_start();

    include "security.php";
    redirectToLogin();

    include "database.php";
    $data = new Database();

    $username = $_POST["username"];

    $stmt = $data->prepare("UPDATE users SET household=null WHERE username=:username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->execute();

    $stmt = $data->prepare("DELETE FROM chores WHERE choreUser=:username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->execute();

    $_SESSION["household"] = null;

    header("Location:index.php");

?>