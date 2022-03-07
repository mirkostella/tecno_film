<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');
    
    $pagina=file_get_contents("../html/raccolta_personale.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    
    $inAttivo="<li><a href=\"../php/raccolta_personale.php\">I miei film</a></li>";
    $attivo="<li id=\"attivo\">I miei film</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    if($_SESSION['loggato']==true){
        
        $raccoltaCardNoleggi="";
        $raccoltaCardAcquisti="";
        recuperaRaccoltaPersonale($raccoltaCardNoleggi,$raccoltaCardAcquisti);
        if(!$raccoltaCardAcquisti && !$raccoltaCardNoleggi){
            $pagina=str_replace('%titoloNoleggi%',"",$pagina);
            $pagina=str_replace('%titoloAcquisti%',"",$pagina);
            $pagina=str_replace('%raccoltaNoleggi%',"",$pagina);
            $pagina=str_replace('%linkNoleggi%',"",$pagina);
            $pagina=str_replace('%linkAcquisti%',"",$pagina);
            $pagina=str_replace('%raccoltaAcquisti%',"<h1>La tua raccolta personale é vuota</h1>",$pagina);
        }
        if($raccoltaCardNoleggi){
            $pagina=str_replace('%linkNoleggi%',"<li><a href=\"#tuoiNoleggi\">Noleggi</a></li>",$pagina);
            $pagina=str_replace('%raccoltaNoleggi%',$raccoltaCardNoleggi,$pagina);
            $pagina=str_replace('%titoloNoleggi%',"<h1 id=\"tuoiNoleggi\">I tuoi noleggi</h1>",$pagina);
            
        }
    else{
        $pagina=str_replace('%linkNoleggi%',"",$pagina);
        $pagina=str_replace('%titoloNoleggi%',"<h1>Non sono presenti noleggi</h1>",$pagina);
        $pagina=str_replace('%raccoltaNoleggi%',"",$pagina);

    }
    if($raccoltaCardAcquisti){
        $pagina=str_replace('%linkAcquisti%',"<li><a href=\"#tuoiAcquisti\">Acquisti</a></li>",$pagina);
        $pagina=str_replace('%titoloAcquisti%',"<h1 id=\"tuoiAcquisti\">I tuoi acquisti</h1>",$pagina);
        $pagina=str_replace('%raccoltaAcquisti%',$raccoltaCardAcquisti,$pagina);
    }
    else{
        $pagina=str_replace('%linkAcquisti%',"",$pagina);
        $pagina=str_replace('%titoloAcquisti%',"<h1>Non sono presenti acquisti</h1>",$pagina);
        $pagina=str_replace('%raccoltaAcquisti%',"",$pagina);
    }
    }
   else{
        $pagina=str_replace('%titoloAcquisti%',"",$pagina);
        $pagina=str_replace('%titoloNoleggi%',"",$pagina);
       $pagina=str_replace('%raccoltaNoleggi%',"<h1>Effettua l'accesso per vedere la tua raccolta personale</h1>",$pagina);
       $pagina=str_replace('%raccoltaAcquisti%',"",$pagina);
       $pagina=str_replace('%linkNoleggi%',"",$pagina);
       $pagina=str_replace('%linkAcquisti%',"",$pagina);
   }
    
    
    
    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
    
?>