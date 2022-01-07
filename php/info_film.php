<?php
    require_once ('connessione.php');

    function recuperaInfo($id){
        $queryMediaValutazioni="SELECT AVG(valutazione) as media from recensione where ID_film=$id GROUP BY ID_film";
        $queryInfo="SELECT titolo,trama,TIME_TO_SEC(durata) as durata,data_uscita,prezzo_acquisto,prezzo_noleggio,path,descrizione,nome_genere
         FROM film JOIN foto_film ON (film.ID=foto_film.ID) JOIN appartenenza ON (film.ID=appartenenza.ID_film) WHERE ID_film=$id";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $media=$connessione->interrogaDB($queryMediaValutazioni);
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
?>