<?php
    require_once ('struttura.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    require_once ('connessione.php');

    $pagina=file_get_contents("../html/classifiche.html");

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiHeader($connessione, $pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/classifiche.php\" accesskey=\"c\">Classifiche</a></li>";
    $attivo="<li id=\"attivo\" accesskey=\"c\">Classifiche</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    $ncard=5;

    ////////TOP 5 DELLA SETTIMANA//////////
    
    $listaCards=recuperaTop5VistiSettimana($connessione);
    $pagina=str_replace('%listaCardSett%',$listaCards,$pagina);
    if($listaCards){
        $pagina=str_replace('%hrCardSett%',"<hr>",$pagina);
    }
    else
        $pagina=str_replace('<li><a href="#top">Top 5 della settimana</a></li>',"", $pagina);
        $pagina=str_replace('%hrCardSett%',"",$pagina);

    ////////I 10 FILM PIU VISTI (con il maggior numero di acquisti e noleggi)//////////

    $listaCards=recuperaPiuVisti($connessione);
    $pagina=str_replace('%listaCardVisti%',$listaCards,$pagina);
    if($listaCards){
        $pagina=str_replace('%hrCardVisti%',"<hr>",$pagina);
    }
    else{
        $pagina=str_replace('<li><a href="#visti">Top 10 più visti</a></li>',"",$pagina);
        $pagina=str_replace('%hrCardVisti%',"",$pagina);
    }

    
    ////////I 10 FILM PIU VOTATI//////////

    $listaCards=recuperaPiuVotati($connessione);

    if($listaCards){
        $pagina=str_replace('%listaCardVotati%',$listaCards,$pagina);
    }
    else{
        $pagina=str_replace('<li><a href="#votati">Top 10 più votati</a></li>',"",$pagina);;
    }

    $connessione->chiudiConnessione();
    
    echo $pagina;

?>