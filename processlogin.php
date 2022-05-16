<?php
    //if there are no login details set the file has been run on its own
    //return to login page
    
    if(!isset($_POST['username'])){
        header("Location:login.php");
    }

    session_start();

    include "database.php";
    include "security.php";
    $data = new Database();

    $password = $_POST["password"];
    $username = $_POST["username"];

    $stmt = $data->prepare("SELECT id,password,household FROM users WHERE username=:username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray();

    if ($user!=null && password_verify($password,$user["password"])) {
        $_SESSION['username'] = h($username);
        $_SESSION['id'] = $user['id'];
        $_SESSION['household'] = h($user['household']);
        header('location:index.php');
    } 

    else {
        session_destroy();
        session_unset();
        header('location:login.php');
    }
?>