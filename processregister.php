<?php

    //if the user has not registered and gone straight to this page the user should register
    include "security.php";

    if(!isset($_POST['username'])){
        header("Location:register.php");
    }

    session_start();

    include "database.php";
    $data = new Database();

    //getting the post variables
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $username = $_POST["username"];
    $name = $_POST["name"];
    $email = $_POST["email"];

    //creating the user in the database
    $stmt = $data->prepare("INSERT INTO users VALUES(NULL, :name, :email, :username, :password, null)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->execute();

    //setting the session username
    $_SESSION['username'] = $username;

    //setting the session user id and household name
    $stmt = $data->prepare("SELECT id,household FROM users WHERE username=:username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $id_household = $stmt->execute()->fetchArray();
    $_SESSION['id'] = $id_household['id'];
    $_SESSION['household'] = h($id_household['household']);

    header('location:index.php');

?>