<?php
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    
    $pagina=file_get_contents('../html/acquisto_noleggio.html');
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $infoFilm=recuperaInfo($_GET['idFilm']);
    $generiFilm=recuperaGeneri($_GET['idFilm']);
    $film=new Card($infoFilm,$generiFilm);
    if(isset($_GET['noleggio']))
        $pagina=str_replace('%tipoConferma%',"noleggio",$pagina);
    else
        $pagina=str_replace('%tipoConferma%',"acquisto",$pagina);

    $pagina=str_replace('%titoloFilmBreadcrumb%',"<a href=\"\" xml:lang=\"en\" lang=\"en\">$film->titolo</a>",$pagina);
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
    $struttura->aggiungiConfermaAcquistoNoleggio($pagina);
    $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
    $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
    $pagina=str_replace('%trama%',$film->trama,$pagina);
    $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);
    $pagina=str_replace('%idFilm%',$_GET['idFilm'],$pagina);
    
    echo $pagina;
?>