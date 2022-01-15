<?php
    require_once ('struttura.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    require_once ('connessione.php');

    date_default_timezone_set("Europe/Rome");
    $data=date("d/m/Y H:m:s");
    print_r($data);
    echo "</br>";
    $unaSettimanaIndietro=date("d/m/Y H:m:s",strtotime('-1 week'));
    print_r($unaSettimanaIndietro);

    $pagina=file_get_contents("../html/classifiche.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/classifiche.php\">Classifiche</a></li>";
    $attivo="<li id=\"attivo\">Classifiche</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    $connessione=new Connessione();
    $connessione->apriConnessione();

    ////////TOP 5 DELLA SETTIMANA//////////
    
    $listaCards=recuperaTop5Sett();
    $pagina=str_replace('%listaCardSett%',$listaCards,$pagina);

    ////////I 10 FILM PIU VISTI (con il maggior numero di acquisti e noleggi)//////////

    $listaCards=recuperaPiuVisti();
    $pagina=str_replace('%listaCardVisti%',$listaCards,$pagina);

    
    ////////I 10 FILM PIU VOTATI//////////

    $listaCards=recuperaMiglioriPerGenere();
    $pagina=str_replace('%listaCardMiglioriGenere%',$listaCards,$pagina);
    
    
    
    
    $connessione->chiudiConnessione();
    echo $pagina;




?>