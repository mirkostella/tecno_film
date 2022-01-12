<?php
    require_once ('connessione.php');
    require_once ('sessione.php');

    //recupera le informazioni per costruire una card di un film in base all'id
    function recuperaInfo($id){
        $queryMediaValutazioni="SELECT AVG(valutazione) as media FROM recensione WHERE ID_film=$id GROUP BY ID_film";
        $queryInfo="SELECT titolo,trama,TIME_TO_SEC(durata) as durata,data_uscita,prezzo_acquisto,prezzo_noleggio,path,descrizione,nome_genere
         FROM film JOIN foto_film ON (film.ID=foto_film.ID) JOIN appartenenza ON (film.ID=appartenenza.ID_film) JOIN genere ON(appartenenza.ID_genere=genere.ID) WHERE ID_film=$id";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $media=$connessione->interrogaDB($queryMediaValutazioni);
        if(!$media){
            $media=array();
            $mediaFilm=array(
                'media'=> 0
            );
            array_push($media,$mediaFilm);
        }
        $info=$connessione->interrogaDB($queryInfo);
        $infoFilm=array(
            'id'=>$id,
            'copertina'=>$info[0]['path'],
            'descrizione'=>$info[0]['descrizione'],
            'titolo'=>$info[0]['titolo'],
            'genere'=>$info[0]['nome_genere'],
            'valutazione'=>$media[0]['media'],
            'prezzoN'=>$info[0]['prezzo_noleggio'],
            'prezzoA'=>$info[0]['prezzo_acquisto'],
            'annoUscita'=>$info[0]['data_uscita'],
            'trama'=>$info[0]['trama'],
            'durata'=>$info[0]['durata']
        );
        return $infoFilm;
    }
    //recupera le informazioni per creare le card nuove uscite
    function recuperaNuoveUscite($limite){
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY annoUscita DESC LIMIT $limite";
        
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();
        return $ris;
}

//restituisce i film che non sono stati acquistati o noleggiati dello stesso genere dell'ultimo film acquistato o noleggiato.
//i film vengono ordinati prima per data e poi per valutazione (maggiore uguale a 3 stelle)
    function recuperaSceltiPerTe($limite){
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) 
        WHERE valutazione>=3 AND film.ID NOT IN (SELECT film.ID FROM film JOIN acquisto ON(film.ID=acquisto.ID_film) WHERE acquisto.ID_utente=".$_SESSION['id']." UNION SELECT film.ID FROM film
        JOIN noleggio ON(film.ID=noleggio.ID_film) WHERE noleggio.ID_utente=".$_SESSION['id'].") AND ID_genere=(SELECT ID_genere FROM (SELECT data_noleggio as data_transizione,ID_genere,film.ID as film 
        FROM utente JOIN noleggio ON (noleggio.ID_utente=utente.ID) JOIN film ON(film.ID=noleggio.ID_film) JOIN appartenenza ON(appartenenza.ID_film=film.ID)  WHERE utente.ID=".$_SESSION['id']." UNION 
        SELECT data_acquisto as data_transizione,ID_genere,film.ID as film FROM utente JOIN acquisto ON (acquisto.ID_utente=utente.ID) JOIN film ON(film.ID=acquisto.ID_film) JOIN 
        appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id'].")ultime_transizioni WHERE data_transizione=(SELECT MAX(ultime_transizioni.data_transizione) FROM 
        (SELECT data_noleggio as data_transizione,ID_genere,film.ID as film FROM utente JOIN noleggio ON (noleggio.ID_utente=utente.ID) JOIN film ON(film.ID=noleggio.ID_film) JOIN
        appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id']." UNION SELECT data_acquisto as data_transizione,ID_genere,film.ID as film FROM utente JOIN 
        acquisto ON (acquisto.ID_utente=utente.ID) JOIN film ON(film.ID=acquisto.ID_film) JOIN appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id'].")ultime_transizioni)) 
        GROUP BY film.ID ORDER BY valutazione,annoUscita DESC LIMIT 6";
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();
        return $ris;
    }
?>





