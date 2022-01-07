<?php

    require_once('struttura.php');

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