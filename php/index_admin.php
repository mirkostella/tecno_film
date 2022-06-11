<?php

    require_once("sessione.php");
    require_once("struttura.php");
    require_once("connessione.php");

    if($_SESSION['admin']==false){
        header('location: login_admin.php');
        exit();
    }
    
    $pagina = file_get_contents("../html/index_admin.html");
    $struttura = new Struttura();
    $struttura->aggiungiHeader_admin($pagina);
    $struttura->aggiungMenu_admin($pagina);

    /*

    Sostituire i segnaposto delle tabelle

    $pagina = str_replace("%tabellaUtenti", $tabella_utenti, $pagina);
    $pagina = str_replace("%tabellaFilm%", $tabella_film, $pagina);ù
    
    */
    
    echo $pagina;
?>