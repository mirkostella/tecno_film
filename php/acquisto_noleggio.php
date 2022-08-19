<?php
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    
    $pagina=file_get_contents('../html/acquisto_noleggio.html');

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $pagina=str_replace("%descrizione%","Se stai acquistando o noleggiando un film, conferma e il gioco è fatto!", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Conferma, Acquisto, Noleggio, Film", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Conferma %tipoConferma%", $pagina);
    
    $idFilm = $_GET['idFilm'];
    $infoFilm=recuperaInfo($connessione, $_GET['idFilm']);
    $generiFilm=recuperaGeneri($connessione, $_GET['idFilm']);
    $film=new Card($infoFilm,$generiFilm);
    
    $pagina=str_replace("%breadcrumb%", "<a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a> &gt; <a href=\"../php/pagina_film.php?idFilm=".$_GET['idFilm']."\">$film->titolo</a> &gt; <span class=\"grassetto\">Conferma %tipoConferma%</span>", $pagina);

    if(isset($_GET['noleggio']))
        $pagina=str_replace("%tipoConferma%", "noleggio", $pagina);
    else
        $pagina=str_replace("%tipoConferma%", "acquisto", $pagina);

    $pagina=str_replace('%titolo%',$film->titolo,$pagina);
    $pagina=str_replace('%path%',$film->copertina,$pagina);
    $pagina=str_replace('%annoUscita%',$film->annoUscita,$pagina);
    $pagina=str_replace('%durata%',$film->durata,$pagina);
    $stringaGeneri='';
    $copiaGeneriFilm=$film->genere;
    $primoGenere=array_pop($copiaGeneriFilm)['generiFilm'];
    $stringaGeneri=$stringaGeneri.$primoGenere;
    foreach($copiaGeneriFilm as &$valore){
        $prossimoGenere=$valore['generiFilm'];
        $stringaGeneri=$stringaGeneri.' , '.$prossimoGenere;
    }
    $pagina=str_replace('%genere%',$stringaGeneri,$pagina);
    $struttura->aggiungiConfermaAcquistoNoleggio($connessione, $pagina);
    $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
    $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
    $pagina=str_replace('%trama%',$film->trama,$pagina);
    $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);
    $pagina=str_replace('%idFilm%',$_GET['idFilm'],$pagina);

    $connessione->chiudiConnessione();
    
    echo $pagina;
?>