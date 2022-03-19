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
    
    $risultatoCard=recuperaNuoveUscite(5);
    if($risultatoCard)
        $pagina=str_replace('%nuoveUscite%',$risultatoCard,$pagina);
    else{
        $pagina=str_replace('%nuoveUscite%',"",$pagina);
        $pagina=str_replace('<li><a href="#nuove">Nuove uscite</a></li>',"",$pagina);
    }
    
    if(isset($_SESSION['loggato']) && $_SESSION['loggato']==true){
        $risultatoCard=recuperaSceltiPerTe(5);
        if($risultatoCard)
            $pagina=str_replace('%sceltiPerTe%',$risultatoCard,$pagina);
        else{
            $pagina=str_replace('<li><a href=#scelti>Scelti per te</a></li>',"",$pagina);
            $pagina=str_replace('%sceltiPerTe%',"",$pagina);
        }

    }
    else{
        $pagina=str_replace('%sceltiPerTe%',"",$pagina);
        $pagina=str_replace('<li><a href=#scelti>Scelti per te</a></li>',"",$pagina);
    }
    
    $risultatoCard=recuperaAzione(5);
    if($risultatoCard){
        $pagina=str_replace('%azione%',$risultatoCard,$pagina);
        $pagina=str_replace('%hrAzione%',"<hr>",$pagina);
    }
    else{
        $pagina=str_replace('%azione%',"",$pagina);
        $pagina=str_replace('%hrAzione%',"",$pagina);
        $pagina=str_replace('<li><a href="#azione">Azione</a></li>',"",$pagina);
    }
        

    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
    
?>