<?php
    session_start();

    header('location: ../php/login.php');
    session_unset();
?>
