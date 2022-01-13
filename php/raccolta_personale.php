<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');
    
    $limite=20;
    if(isset($_GET['limite'])){
        $limite=$_GET['limite']+20;
    }
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
    recuperaRaccoltaPersonale($limite,$raccoltaCardNoleggi,$raccoltaCardAcquisti);
    if($raccoltaCardNoleggi || $raccoltaCardAcquisti)
        $pagina=str_replace('%raccolta%',$raccoltaCardNoleggi.$raccoltaCardAcquisti,$pagina);
    else
        $pagina=str_replace('%raccolta%',"<h1>Non sono ancora presenti film nella tua raccolta</h1>",$pagina);

    }
    else
        $pagina=str_replace('%raccolta%',"<h1>Effettua l'accesso per vedere la tua raccolta personale</h1>",$pagina);
    
    
    
    //rimuove il segnaposto classifica dalle card non classificate
    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
    
?>