<?php
    session_start();

    header('location: ../php/login_admin.php');

    session_unset();
?>