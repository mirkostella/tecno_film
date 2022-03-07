<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    

    $pagina=file_get_contents("../html/chi_siamo.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/chi_siamo.php\">Chi siamo</a></li>";
    $attivo="<li id=\"attivo\">Chi siamo</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    echo $pagina;