<?php
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('sessione.php');
    require_once ('info_film.php');

    $pagina=file_get_contents('../html/acquisto_noleggio.html');
    $struttura=new Struttura();
    $connessione=new Connessione();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $infoFilm=recuperaInfo($_POST['idFilm']);
    $film=new Card($infoFilm);

    $pagina=str_replace('%titolo%',$film->titolo,$pagina);
    $pagina=str_replace('%path%',$film->copertina,$pagina);
    $pagina=str_replace('%annoUscita%',$film->annoUscita,$pagina);
    $pagina=str_replace('%durata%',$film->durata,$pagina);
    $pagina=str_replace('%genere%',$film->genere,$pagina);
    $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
    $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
    $pagina=str_replace('%trama%',$film->trama,$pagina);
    $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);

    

    echo $pagina;
?>