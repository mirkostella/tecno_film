<?php


    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');
    
    $pagina=file_get_contents("../html/raccolta_personale.html");

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace("%descrizione%","Ecco i film presenti nella tua raccolta. Puoi trovare i film che hai acquistato e noleggiato", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Film, Noleggi, Acquisti, Raccolta personale", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: I miei film", $pagina);
    $pagina=str_replace("%breadcrumb%","<a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a> &gt; <span class=\"grassetto\">I miei film</span>", $pagina);
    $inAttivo="<li><a href=\"../php/raccolta_personale.php\">I miei film</a></li>";
    $attivo="<li id=\"attivo\">I miei film</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    if($_SESSION['loggato']==true && $_SESSION['admin']==false){
        $raccoltaCardAcquisti="";
        $raccoltaCardNoleggi="";
        $raccoltaCardNoleggiScaduti="";
        recuperaRaccoltaPersonale($connessione, $raccoltaCardAcquisti, $raccoltaCardNoleggi, $raccoltaCardNoleggiScaduti);
        if(!$raccoltaCardAcquisti && !$raccoltaCardNoleggi && !$raccoltaCardNoleggiScaduti){
            $pagina=str_replace('%linkAcquisti%',"",$pagina);
            $pagina=str_replace('%linkNoleggi%',"",$pagina);
            $pagina=str_replace('%linkNoleggiScaduti%',"",$pagina);
            $pagina=str_replace('%titoloAcquisti%',"",$pagina);
            $pagina=str_replace('%titoloNoleggi%',"",$pagina);
            $pagina=str_replace('%titoloNoleggiScaduti%',"",$pagina);
            $pagina=str_replace('%raccoltaAcquisti%',"<h2>La tua raccolta personale é vuota</h2>",$pagina);
            $pagina=str_replace('%raccoltaNoleggi%',"",$pagina);
            $pagina=str_replace('%raccoltaNoleggiScaduti%',"",$pagina);
        }
        
        if($raccoltaCardAcquisti){
            $pagina=str_replace('%linkAcquisti%',"<li><a href=\"#tuoiAcquisti\">Acquisti</a></li>",$pagina);
            $pagina=str_replace('%titoloAcquisti%',"<h2 id=\"tuoiAcquisti\">I tuoi acquisti</h2>",$pagina);
            $pagina=str_replace('%raccoltaAcquisti%',$raccoltaCardAcquisti,$pagina);
        }
        else{
            $pagina=str_replace('%linkAcquisti%',"",$pagina);
            $pagina=str_replace('%titoloAcquisti%',"<h2>Non sono presenti acquisti</h2>",$pagina);
            $pagina=str_replace('%raccoltaAcquisti%',"",$pagina);
        }

        if($raccoltaCardNoleggi){
            $pagina=str_replace('%linkNoleggi%',"<li><a href=\"#tuoiNoleggi\">Noleggi</a></li>",$pagina);
            $pagina=str_replace('%raccoltaNoleggi%',$raccoltaCardNoleggi,$pagina);
            $pagina=str_replace('%titoloNoleggi%',"<h2 id=\"tuoiNoleggi\">I tuoi noleggi</h2>",$pagina);
            
        }
        else{
            $pagina=str_replace('%linkNoleggi%',"",$pagina);
            $pagina=str_replace('%titoloNoleggi%',"<h2>Non sono presenti noleggi</h2>",$pagina);
            $pagina=str_replace('%raccoltaNoleggi%',"",$pagina);
        }

        if($raccoltaCardNoleggiScaduti){
            $pagina=str_replace('%linkNoleggiScaduti%',"<li><a href=\"#tuoiNoleggiScaduti\">Noleggi scaduti</a></li>",$pagina);
            $pagina=str_replace('%raccoltaNoleggiScaduti%',$raccoltaCardNoleggiScaduti,$pagina);
            $pagina=str_replace('%titoloNoleggiScaduti%',"<h2 id=\"tuoiNoleggiScaduti\">I tuoi noleggi scaduti</h2>",$pagina);
        }
        else{
            $pagina=str_replace('%linkNoleggiScaduti%',"",$pagina);
            $pagina=str_replace('%titoloNoleggiScaduti%',"<h2>Non sono presenti noleggi scaduti</h2>",$pagina);
            $pagina=str_replace('%raccoltaNoleggiScaduti%',"",$pagina);
        }
    }
    else{
        $pagina=str_replace('%linkAcquisti%',"",$pagina);
        $pagina=str_replace('%linkNoleggi%',"",$pagina);
        $pagina=str_replace('%linkNoleggiScaduti%',"",$pagina);
        $pagina=str_replace('%titoloAcquisti%',"",$pagina);
        $pagina=str_replace('%titoloNoleggi%',"",$pagina);
        $pagina=str_replace('%titoloNoleggiScaduti%',"",$pagina);
        $pagina=str_replace('%raccoltaAcquisti%',"",$pagina);
        $pagina=str_replace('%raccoltaNoleggi%',"<h2>Effettua l'accesso per vedere la tua raccolta personale</h2>",$pagina);
        $pagina=str_replace('%raccoltaNoleggiScaduti%',"",$pagina);
   }
    
    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);

    $connessione->chiudiConnessione();
    echo $pagina;
    
?>