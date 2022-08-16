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

    if(isset($_SESSION['pagina_corrente'])){
        if(basename($_SERVER['REQUEST_URI'])!=$_SESSION['pagina_corrente'])
            $_SESSION['pagina_precedente']=$_SESSION['pagina_corrente'];

    }

    $_SESSION['pagina_corrente']=basename($_SERVER['REQUEST_URI']);

    if(!isset($_SESSION['pagina_precedente']))
        $_SESSION['pagina_precedente']='index.php';
?>