<?php
    session_start();

    header('location: login_admin.php');

    session_unset();
?>