<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    

    $pagina=file_get_contents("../html/chi_siamo.html");

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiHeader($connessione, $pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/chi_siamo.php\">Chi siamo</a></li>";
    $attivo="<li id=\"attivo\">Chi siamo</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    $connessione->chiudiConnessione();

    echo $pagina;