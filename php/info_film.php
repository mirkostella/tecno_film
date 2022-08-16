<?php
    require_once ('sessione.php');
    require_once ('card.php');
    
    //recupera le informazioni per costruire una card di un film in base all'id
    function recuperaInfo($connessione, $id){
        $queryMediaValutazioni="SELECT AVG(valutazione) as media FROM recensione WHERE ID_film=$id GROUP BY ID_film";
        $queryInfo="SELECT titolo,trama,TIME_TO_SEC(durata) as durata,data_uscita,prezzo_acquisto,prezzo_noleggio,path,descrizione,nome_genere
         FROM film JOIN foto_film ON (film.copertina=foto_film.ID) JOIN appartenenza ON (film.ID=appartenenza.ID_film) JOIN genere ON(appartenenza.ID_genere=genere.ID) WHERE ID_film=$id";
        $media=$connessione->interrogaDB($queryMediaValutazioni);
        $copiaMedia=null;
        if(!$media || is_null($media[0]['media'])){
            $copiaMedia=array(
                'media'=> 0
            );
        }
        else
            $copiaMedia=array(
                'media'=>$media[0]['media']
            );

        $info=$connessione->interrogaDB($queryInfo);
        $durata=$info[0]['durata']/60;
        $dataUscita=strtotime($info[0]['data_uscita']);
        $cambioFormato=date('d/m/Y', $dataUscita);
        $infoFilm=array(
            'id'=>$id,
            'copertina'=>$info[0]['path'],
            'descrizione'=>$info[0]['descrizione'],
            'titolo'=>$info[0]['titolo'],
            'genere'=>$info[0]['nome_genere'],
            'valutazione'=>$copiaMedia['media'],
            'prezzoN'=>$info[0]['prezzo_noleggio'],
            'prezzoA'=>$info[0]['prezzo_acquisto'],
            'annoUscita'=>$cambioFormato,
            'trama'=>$info[0]['trama'],
            'durata'=>$durata
        );
        return $infoFilm;
    }
    
    function recuperaGeneri($connessione, $idFilm){
        $queryGeneri='SELECT nome_genere as generiFilm FROM film JOIN appartenenza ON(film.ID=appartenenza.ID_film) JOIN genere ON(appartenenza.ID_genere=genere.ID) WHERE ID_film='.$idFilm;
        $generi=$connessione->interrogaDB($queryGeneri);
        return $generi;
    }
    
    function pulsanteVediAltro($nomeCategoria,$collegamento, $limite){
        $pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
        $pulsanteVediAltro=str_replace('%nomeCategoria%',$nomeCategoria,$pulsanteVediAltro);
        $pulsanteVediAltro=str_replace('%collegamento%',$collegamento,$pulsanteVediAltro);
        $pulsanteVediAltro=str_replace('%limite%', $limite ,$pulsanteVediAltro);
        return $pulsanteVediAltro;
    }

    //recupera le informazioni per creare le card nuove uscite
    function recuperaNuoveUscite($connessione, $limite){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY annoUscita DESC, valutazione DESC LIMIT $limite";
        $ris=$connessione->interrogaDB($queryCard);

        $listaCard=creaListaCard($connessione, $ris);
        if(!$listaCard)
            return false;
        else{
            //$pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"nuove\">Nuove uscite</h2>",$categoriaCard);
            //$pulsanteVediAltro=str_replace('%nomeCategoria%',"Nuove Uscite",$pulsanteVediAltro);
            //$categoriaCard=str_replace('%vediAltro%',$pulsanteVediAltro,$categoriaCard);
            //$categoriaCard=str_replace('%collegamento%',"film_categoria.php",$categoriaCard);
            return $categoriaCard;
        }
    }

    //restituisce i film che non sono stati acquistati o noleggiati dello stesso genere dell'ultimo film acquistato o noleggiato.
    //i film vengono ordinati prima per data e poi per valutazione (maggiore uguale a 3 stelle)
    
    function recuperaSceltiPerTe($connessione, $limite){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) 
        WHERE valutazione>=3 AND film.ID NOT IN (SELECT film.ID FROM film JOIN acquisto ON(film.ID=acquisto.ID_film) WHERE acquisto.ID_utente=".$_SESSION['id']." UNION SELECT film.ID FROM film
        JOIN noleggio ON(film.ID=noleggio.ID_film) WHERE noleggio.ID_utente=".$_SESSION['id'].") AND ID_genere IN (SELECT ID_genere FROM (SELECT data_noleggio as data_transizione,ID_genere,film.ID as film 
        FROM utente JOIN noleggio ON (noleggio.ID_utente=utente.ID) JOIN film ON(film.ID=noleggio.ID_film) JOIN appartenenza ON(appartenenza.ID_film=film.ID)  WHERE utente.ID=".$_SESSION['id']." UNION 
        SELECT data_acquisto as data_transizione,ID_genere,film.ID as film FROM utente JOIN acquisto ON (acquisto.ID_utente=utente.ID) JOIN film ON(film.ID=acquisto.ID_film) JOIN 
        appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id'].")ultime_transizioni WHERE data_transizione=(SELECT MAX(ultime_transizioni.data_transizione) FROM 
        (SELECT data_noleggio as data_transizione,ID_genere,film.ID as film FROM utente JOIN noleggio ON (noleggio.ID_utente=utente.ID) JOIN film ON(film.ID=noleggio.ID_film) JOIN
        appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id']." UNION SELECT data_acquisto as data_transizione,ID_genere,film.ID as film FROM utente JOIN 
        acquisto ON (acquisto.ID_utente=utente.ID) JOIN film ON(film.ID=acquisto.ID_film) JOIN appartenenza ON(appartenenza.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id'].")ultime_transizioni)) 
        GROUP BY film.ID ORDER BY valutazione DESC, annoUscita DESC LIMIT $limite";
        $ris=$connessione->interrogaDB($queryCard);
        $listaCard=creaListaCard($connessione, $ris);
        if(!$listaCard)
            return false;
        else{
            $pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"scelti\">Scelti per te</h2>",$categoriaCard);
            $pulsanteVediAltro=str_replace('%nomeCategoria%',"Scelti per te",$pulsanteVediAltro);
            $categoriaCard=str_replace('%vediAltro%',$pulsanteVediAltro,$categoriaCard);
            $categoriaCard=str_replace('%collegamento%',"film_categoria.php",$categoriaCard);
            return $categoriaCard;
        }
    }

    function recuperaPerGenere($connessione, $limite, $genere){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) 
        WHERE nome_genere='".$genere."' GROUP BY id ORDER BY valutazione DESC, annoUscita DESC LIMIT $limite";
        $ris=$connessione->interrogaDB($queryCard);
        $listaCard=creaListaCard($connessione, $ris);
        if(!$listaCard)
            return false;
        else{
            $pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"$genere\">".$genere."</h2>",$categoriaCard);
            $pulsanteVediAltro=str_replace('%nomeCategoria%',".$genere.",$pulsanteVediAltro);
            $categoriaCard=str_replace('%vediAltro%',$pulsanteVediAltro,$categoriaCard);
            $categoriaCard=str_replace('%collegamento%',"film_categoria.php",$categoriaCard);
            return $categoriaCard;
        }
    }

    function recuperaRaccoltaPersonale($connessione, &$raccoltaCardNoleggi,&$raccoltaCardAcquisti){
        //acquisti dell'utente
        $queryCardAcquisti="SELECT film.ID as id,titolo,copertina,TIME_TO_SEC(durata) as durata,
        path as copertina,descrizione FROM film JOIN appartenenzaNoDoppioni
    ON(film.ID=appartenenzaNoDoppioni.ID_film) JOIN genere ON (appartenenzaNoDoppioni.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) JOIN acquisto ON (film.ID=acquisto.ID_film) WHERE acquisto.ID_utente=".$_SESSION['id'];
        $queryCardNoleggi="SELECT film.ID as id,titolo,copertina,TIME_TO_SEC(durata) as durata,
        path as copertina,descrizione,scadenza_noleggio FROM film JOIN appartenenzaNoDoppioni 
        ON(film.ID=appartenenzaNoDoppioni.ID_film) JOIN genere ON (appartenenzaNoDoppioni.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) JOIN noleggio ON (film.ID=noleggio.ID_film) WHERE noleggio.ID_utente=".$_SESSION['id'] ;
        $risAcquisti=$connessione->interrogaDB($queryCardAcquisti);
        $risNoleggi=$connessione->interrogaDB($queryCardNoleggi);
        $listaNoleggi=creaListaCardPersonale($connessione, $risNoleggi);
        $listaAcquisti=creaListaCardPersonale($connessione, $risAcquisti);

        if($listaNoleggi){
            $raccoltaCardNoleggi=file_get_contents('../componenti/categoria_index.html');
            $raccoltaCardNoleggi=str_replace('%listaCard%',$listaNoleggi,$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%spazio%',"",$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%categoria%',"",$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%nomeSubmit%',"raccoltaPersonale",$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%prezzo%',"",$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%valutazione%',"",$raccoltaCardNoleggi);
            $raccoltaCardNoleggi=str_replace('%vediAltro%',"",$raccoltaCardNoleggi);
        }
        if($listaAcquisti){
            $raccoltaCardAcquisti=file_get_contents('../componenti/categoria_index.html');
            $raccoltaCardAcquisti=str_replace('%listaCard%',$listaAcquisti,$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%spazio%',"",$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%categoria%',"",$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%nomeSubmit%',"raccoltaPersonale",$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%prezzo%',"",$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%valutazione%',"",$raccoltaCardAcquisti);
            $raccoltaCardAcquisti=str_replace('%vediAltro%',"",$raccoltaCardAcquisti);
        }
    }

    //restituisce i film più acquistati/noleggiati nell'ultima settimana
    function recuperaTop5VistiSettimana($connessione){
        $queryCard="SELECT idfilm, SUM(somma) as risultato from (SELECT acquisto.ID_film as idfilm,count(*) as somma from acquisto WHERE acquisto.data_acquisto >= DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK) GROUP BY idfilm UNION ALL SELECT noleggio.ID_film as idfilm,count(*) as somma from noleggio WHERE noleggio.data_noleggio>= DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK) GROUP BY idfilm) as tot JOIN film ON (tot.idfilm=film.ID) GROUP BY idfilm ORDER BY risultato DESC LIMIT 5";
        $ris=$connessione->interrogaDB($queryCard);
        $Top5Settimana=array();
        if($ris){
            foreach($ris as $valore){
                array_push($Top5Settimana,recuperaInfo($connessione, $valore['idfilm']));
                }
        }
        
        $listaCard=creaListaCardClassificata($connessione, $Top5Settimana);
        if(!$listaCard)
            return false;
        else{
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            $categoriaCard=str_replace('%spazio%',"",$categoriaCard);
            $categoriaCard=str_replace('%categoria%',"<h2 id=\"top\">Top 5 della settimana</h2>",$categoriaCard);
            
            $categoriaCard=str_replace('%vediAltro%',"",$categoriaCard);
            
            return $categoriaCard;
        }
    }

    function recuperaPiuVisti($connessione){
        $queryCard="SELECT idfilm,data_uscita,SUM(somma) as risultato from (SELECT acquisto.ID_film as idfilm,count(*) as somma from acquisto GROUP BY idfilm UNION ALL SELECT noleggio.ID_film as idfilm,count(*) as somma from noleggio GROUP BY idfilm) as tot JOIN film ON (tot.idfilm=film.ID) GROUP BY idfilm ORDER BY risultato DESC LIMIT 10";
        $ris=$connessione->interrogaDB($queryCard);
        //creo l'array con le info dei film in base agli id dei 10 restituiti dalla query precedente
        $filmPiuVisti=array();
        if($ris){
            foreach($ris as $valore){
                array_push($filmPiuVisti,recuperaInfo($connessione, $valore['idfilm']));
                }
        }
        
        $listaCard=creaListaCardClassificata($connessione, $filmPiuVisti);
        if(!$listaCard)
            return false;
        else{
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else{
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"visti\"> Top 10 più visti</h2>",$categoriaCard);
            }
            return $categoriaCard;
        }
    }

    function recuperaPiuVotati($connessione){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN, path as copertina,descrizione,AVG(valutazione) as valutazione, n_voti FROM film JOIN appartenenza ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (recensione.ID_film=film.ID) LEFT JOIN nvoti ON (film.ID=nvoti.ID_film) GROUP BY film.ID ORDER BY valutazione DESC, n_voti DESC LIMIT 10";
        $ris=$connessione->interrogaDB($queryCard);
        
        $listaCard=creaListaCardClassificata($connessione, $ris);
        if(!$listaCard)
            return false;
        else{
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else{
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"votati\"> Top 10 più votati</h2>",$categoriaCard);
            }
            return $categoriaCard;
        }
    }

?>