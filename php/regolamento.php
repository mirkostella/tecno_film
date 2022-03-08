<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    

    $pagina=file_get_contents("../html/regolamento.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/regolamento.php\">Regolamento</a></li>";
    $attivo="<li id=\"attivo\">Regolamento</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    echo $pagina;