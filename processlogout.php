<?php

//Logs the user out by destroying the session if a session is set
session_start();

if (isset($_SESSION['id'])){
    session_destroy();
    session_unset();
    header('location:login.php');
}
else{
    header('location:index.php');
}

?>