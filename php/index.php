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
    $ncard=5;
    
    $risultatoCard=recuperaNuoveUscite($ncard);
    if($risultatoCard){
        $pagina=str_replace('%nuoveUscite%',$risultatoCard,$pagina);
        $pulsanteVedialtro=pulsanteVediAltro('Nuove Uscite','film_categoria.php', $ncard+5);
        $pagina=str_replace('%vediAltroNuoveUscite%',$pulsanteVedialtro ,$pagina);
    }
    else{
        $pagina=str_replace('%nuoveUscite%',"",$pagina);
        $pagina=str_replace('<li><a href="#nuove">Nuove uscite</a></li>',"",$pagina);
        $pagina=str_replace('%vediAltroNuoveUscite%',"",$pagina);
    }

    
    if(isset($_SESSION['loggato']) && $_SESSION['loggato']==true){
        $risultatoCard=recuperaSceltiPerTe($ncard);
        if($risultatoCard){
            $pagina=str_replace('%sceltiPerTe%',$risultatoCard,$pagina);
            $pagina=str_replace('%hrSceltiPerTe%',"<hr>",$pagina);
        }
        else{
            $pagina=str_replace('<li><a href=#scelti>Scelti per te</a></li>',"",$pagina);
            $pagina=str_replace('%sceltiPerTe%',"",$pagina);
            $pagina=str_replace('%hrSceltiPerTe%',"",$pagina);
        }
    }
    else{
        $pagina=str_replace('%sceltiPerTe%',"",$pagina);
        $pagina=str_replace('<li><a href=#scelti>Scelti per te</a></li>',"",$pagina);
        $pagina=str_replace('%hrSceltiPerTe%',"",$pagina);
    }
    
    $risultatoCard=recuperaAzione($ncard);
    if($risultatoCard){
        $pagina=str_replace('%azione%',$risultatoCard,$pagina);
        $pagina=str_replace('%hrAzione%',"<hr>",$pagina);
        $pulsanteVedialtro=pulsanteVediAltro('Azione','film_categoria.php', $ncard+5);
        $pagina=str_replace('%vediAltroAzione%',$pulsanteVedialtro ,$pagina);
    }
    else{
        $pagina=str_replace('%azione%',"",$pagina);
        $pagina=str_replace('%hrAzione%',"",$pagina);
        $pagina=str_replace('<li><a href="#azione">Azione</a></li>',"",$pagina);
        $pagina=str_replace('%vediAltroAzione%',"",$pagina);
    }
        

    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
    
?>