<?php
    session_start();

    if(!isset($_SESSION['loggato'])){
        $_SESSION['loggato']=false;
    }

    if(!isset($_SESSION['id'])){
        $_SESSION['id']="";
    }

    if(!isset($_SESSION['admin'])){
        $_SESSION['admin']=false;
    }
?>