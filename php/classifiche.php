<?php
    require_once ('struttura.php');
    require_once ('sessione.php');
    require_once ('card.php');
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
    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    ////////TOP 5 DELLA SETTIMANA//////////
    $queryCard="SELECT film.ID as id,foto_film.path as copertina,descrizione,titolo,nome_genere as genere,AVG(valutazione) as valutazione,
    prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,data_uscita as annoUscita,trama,TIME_TO_SEC(durata) as durata,recensione.data FROM film JOIN foto_film ON(film.ID=foto_film.ID) JOIN appartenenza ON(film.ID=appartenenza.ID_film)
    LEFT JOIN recensione ON(film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY valutazione DESC LIMIT 5";
    
    $risultatoCard=$connessione->interrogaDB($queryCard);
    $listaCards=creaListaCardClassificata($risultatoCard);
    $pagina=str_replace('%listaCardSett%',$listaCards,$pagina);

    ////////I 10 FILM PIU VISTI (con il maggior numero di acquisti e noleggi)//////////

    $queryCard="SELECT film.ID as id,foto_film.path as copertina,descrizione,titolo,nome_genere as genere,AVG(valutazione) as valutazione,
    prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,data_uscita as annoUscita,trama,TIME_TO_SEC(durata) as durata,recensione.data FROM film JOIN foto_film ON(film.ID=foto_film.ID) JOIN appartenenza ON(film.ID=appartenenza.ID_film)
    LEFT JOIN recensione ON(film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY valutazione DESC LIMIT 5";
    
    $risultatoCard=$connessione->interrogaDB($queryCard);
    $listaCards=creaListaCardClassificata($risultatoCard);
    $pagina=str_replace('%listaCardVisti%',$listaCards,$pagina);

    ////////I 10 FILM PIU VOTATI//////////

    $queryCard="SELECT film.ID as id,foto_film.path as copertina,descrizione,titolo,nome_genere as genere,AVG(valutazione) as valutazione,
    prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,data_uscita as annoUscita,trama,TIME_TO_SEC(durata) as durata,recensione.data FROM film JOIN foto_film ON(film.ID=foto_film.ID) JOIN appartenenza ON(film.ID=appartenenza.ID_film)
    LEFT JOIN recensione ON(film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY valutazione DESC LIMIT 5";
    
    $risultatoCard=$connessione->interrogaDB($queryCard);
    $listaCards=creaListaCardClassificata($risultatoCard);
    $pagina=str_replace('%listaCardVotati%',$listaCards,$pagina);
    
    
    
    
    $connessione->chiudiConnessione();

    echo $pagina;




?>