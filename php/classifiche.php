<?php
    require_once ('struttura.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    require_once ('connessione.php');

    $pagina=file_get_contents("../html/classifiche.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/classifiche.php\">Classifiche</a></li>";
    $attivo="<li id=\"attivo\">Classifiche</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    ////////TOP 5 DELLA SETTIMANA//////////
    
    $listaCards=recuperaTop5Sett();
    $pagina=str_replace('%listaCardSett%',$listaCards,$pagina);

    ////////I 10 FILM PIU VISTI (con il maggior numero di acquisti e noleggi)//////////

    $listaCards=recuperaPiuVisti();
    $pagina=str_replace('%listaCardVisti%',$listaCards,$pagina);

    
    ////////I 10 FILM PIU VOTATI PER GENERE//////////

    $listaCards=recuperaMiglioriPerGenere();
    $pagina=str_replace('%listaCardMiglioriGenere%',$listaCards,$pagina);
    
    
    echo $pagina;

?>