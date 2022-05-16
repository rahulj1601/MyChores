<?php

    //converts any htmlspecialcharacters
    function h($string){
        return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
    }

    //redirects to the login page if the user is not logged in
    function redirectToLogin(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])){
            session_destroy();
            session_unset();
            header('location:login.php');
        }
    }

?>