<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once('struttura.php');
    require_once('card.php');

    
    $pagina=file_get_contents("../html/index.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a></li>";
    $attivo="<li xml:lang=\"en\" lang=\"en\" id=\"attivo\">Home</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    $listaCards="";

    $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
    path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza ON(film.copertina=appartenenza.ID_film) 
    JOIN foto_film ON(film.ID=foto_film.ID) JOIN recensione ON (film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY annoUscita ASC LIMIT 6";

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    $risultatoCard=$connessione->interrogaDB($queryCard);
    $connessione->chiudiConnessione();

    foreach($risultatoCard as $valore){
        $cardAttuale=new Card($valore);
        $listaCards=$listaCards.$cardAttuale->aggiungiBase();
    }

    $pagina=str_replace('%listaCardN%',$listaCards,$pagina);
    $pagina=str_replace('%classifica%',"",$pagina);
    
    echo $pagina;
?>