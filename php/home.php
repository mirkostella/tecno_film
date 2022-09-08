<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('info_film.php');
    require_once ('lingua.php');
    
    $pagina=file_get_contents("../html/home.html");

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace("%descrizione%","Tecno film è un sito web per l'acquisto e il noleggio di film. Nella home puoi trovare una panoramica delle nuove uscite e dei film per genere", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Nuove uscite, Acquisto, Noleggio, Film", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Home", $pagina);
    $pagina=str_replace("%breadcrumb%","<span xml:lang=\"en\" lang=\"en\" class=\"grassetto\">Home</span>", $pagina);
    $inAttivo="<li><a href=\"../php/home.php\" xml:lang=\"en\" lang=\"en\" accesskey=\"h\">Home</a></li>";
    $attivo="<li xml:lang=\"en\" lang=\"en\" id=\"attivo\" accesskey=\"h\">Home</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    $ncard=5;
    
    $risultatoCard=recuperaNuoveUscite($connessione, $ncard);
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

    
    if($_SESSION['loggato']==true && $_SESSION['admin']==false){
        $risultatoCard=recuperaSceltiPerTe($connessione);
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
    
    $queryGeneri="SELECT DISTINCT nome_genere FROM genere JOIN appartenenza ON (genere.ID = appartenenza.ID_genere)";
    $ElencoGeneriNonVuoti=$connessione->InterrogaDB($queryGeneri);
    $generi=array();
    foreach($ElencoGeneriNonVuoti as $valore){
        array_push($generi, $valore['nome_genere']);
    }

    $listaCollegamenti="";
    $listaSegnaposti="";
    foreach($generi as $valore){
        $valoreLang=aggiungiSpanLang($valore);
        $valore=eliminaDelimitatoriLingua($valore);
        $nuovoCollegamento='<li><a href="#'.$valore.'">'.$valoreLang.'</a></li>';
        $SegnapostiGenere='%hr'.$valore.'%'.'%'.$valore.'%'.'%vediAltro'.$valore.'%';
        $listaCollegamenti=$listaCollegamenti.$nuovoCollegamento;
        $listaSegnaposti=$listaSegnaposti.$SegnapostiGenere;
    }

    $pagina=str_replace('%listaSegnaposti%', $listaSegnaposti, $pagina);
    $pagina=str_replace('%listaCollegamenti%',$listaCollegamenti,$pagina);
    foreach($generi as $valore){
        $risultatoCard=recuperaPerGenere($connessione, $ncard, $valore);
        $valoreNoDel=eliminaDelimitatoriLingua($valore);
        if($risultatoCard){
        $risultatoCard=eliminaDelimitatoriLingua($risultatoCard);
        $pagina=str_replace('%'.$valoreNoDel.'%',$risultatoCard,$pagina);
        $pagina=str_replace('%hr'.$valoreNoDel.'%',"<hr>",$pagina);
        $pulsanteVedialtro=pulsanteVediAltro($valore,'film_categoria.php', $ncard+5);
        $pagina=str_replace('%vediAltro'.$valoreNoDel.'%',$pulsanteVedialtro ,$pagina);
        }
        else{
        $pagina=str_replace('%'.$valoreNoDel.'%',"",$pagina);
        $pagina=str_replace('%hr'.$valoreNoDel.'%',"",$pagina);
        $pagina=str_replace('<li><a href="#'.$valoreNoDel.'">'.$valoreNoDel.'</a></li>',"",$pagina);
        $pagina=str_replace('%vediAltro'.$valoreNoDel.'%',"",$pagina);
        }
    }      

    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);

    $connessione->chiudiConnessione();

    echo $pagina;
    
?>