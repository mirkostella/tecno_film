<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');

    
    $pagina=file_get_contents("../html/index.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a></li>";
    $attivo="<li xml:lang=\"en\" lang=\"en\" id=\"attivo\">Home</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    
    $risultatoCard=recuperaNuoveUscite(6);
    if($risultatoCard)
        $pagina=str_replace('%nuoveUscite%',$risultatoCard,$pagina);
    else
        $pagina=str_replace('%nuoveUscite%',"",$pagina);
    
    $risultatoCard=recuperaSceltiPerTe(6);
    if($risultatoCard)
        $pagina=str_replace('%sceltiPerTe%',$risultatoCard,$pagina);
    else
        $pagina=str_replace('%sceltiPerTe%',"",$pagina);
    
    
    $risultatoCard=recuperaAzione(6);
    if($risultatoCard)
        $pagina=str_replace('%azione%',$risultatoCard,$pagina);
    else
        $pagina=str_replace('%azione%',"",$pagina);

    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
    
?>