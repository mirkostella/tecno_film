<?php
    require_once ('connessione.php');
    require_once ('sessione.php');
    require_once ('card.php');
    
    //recupera le informazioni per costruire una card di un film in base all'id
    function recuperaInfo($id){
        $queryMediaValutazioni="SELECT AVG(valutazione) as media FROM recensione WHERE ID_film=$id GROUP BY ID_film";
        $queryInfo="SELECT titolo,trama,TIME_TO_SEC(durata) as durata,data_uscita,prezzo_acquisto,prezzo_noleggio,path,descrizione,nome_genere
         FROM film JOIN foto_film ON (film.copertina=foto_film.ID) JOIN appartenenza ON (film.ID=appartenenza.ID_film) JOIN genere ON(appartenenza.ID_genere=genere.ID) WHERE ID_film=$id";
        $connessione=new Connessione();
        $connessione->apriConnessione();
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
    
    function recuperaGeneri($idFilm){
        $queryGeneri='SELECT nome_genere as generiFilm FROM film JOIN appartenenza ON(film.ID=appartenenza.ID_film) JOIN genere ON(appartenenza.ID_genere=genere.ID) WHERE ID_film='.$idFilm;
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $generi=$connessione->interrogaDB($queryGeneri);
        $connessione->chiudiConnessione();
        return $generi;
    }

    //tra gli ultimi aggiunti restituisce la lista dei film che sono tra i migliori per ogni genere (generi scelti casualmente tra tutti)
    function recuperaMiglioriPerGenere(){
        //recupero i generi associati ad almeno un film
        $queryGeneri="SELECT nome_genere FROM genere WHERE nome_genere IN (SELECT nome_genere FROM genere JOIN appartenenza ON(appartenenza.ID_genere=genere.ID))";  
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $filmScelti=array();
        $arrayGeneri=$connessione->interrogaDB($queryGeneri);
        if($arrayGeneri){
            shuffle($arrayGeneri);
            foreach($arrayGeneri as $valore){
                $genere=array_pop($arrayGeneri);
                /*$queryLorenzo='SELECT idMigliore,MAX(voto) as massimo FROM (
                    SELECT film.ID as idMigliore,AVG(valutazione) as voto FROM film JOIN recensione ON(recensione.ID_film=film.ID) JOIN appartenenza ON(appartenenza.ID_film=film.ID) JOIN genere ON(genere.ID=appartenenza.ID_genere) WHERE nome_genere=\''.$valore['nome_genere'].'\' GROUP BY idMigliore ORDER BY film.data_uscita DESC) as filmConMedieVoti LIMIT 1';
                //seleziona i film piú recenti con il voto piú alto per genere */

                //mi ritorna gli id dei film che appartengono al genere in ordine di valutazione media
                $queryValGenere='SELECT film.ID as idMigliore,AVG(valutazione) as voto FROM film JOIN recensione ON(recensione.ID_film=film.ID) JOIN appartenenza ON(appartenenza.ID_film=film.ID) JOIN genere ON(genere.ID=appartenenza.ID_genere) WHERE nome_genere=\''.$valore['nome_genere'].'\' GROUP BY idMigliore ORDER BY voto DESC';
                $film=$connessione->interrogaDB($queryValGenere);

                if(!$filmScelti){
                    array_push($filmScelti, $film[0]['idMigliore']);
                }
                else{
                    for($i=0; $i<count($film); $i++){
                        if(!in_array($film[$i]['idMigliore'], $filmScelti)){
                            array_push($filmScelti, $film[$i]['idMigliore']);
                            break;
                        }
                        else{
                            continue;
                        }
                    }
                }
            }
        }
        
        $connessione->chiudiConnessione();
        //$generi scelti a questo punto contiene gli id dei film da inserire nella lista
        $listaCardGeneri=array();
        $filmTrovati=count($filmScelti);

        if($filmTrovati>5)
            $filmTrovati=5;
        for($i=0;$i<$filmTrovati;$i++){
            $idFilmCorrente=$filmScelti[$i];
            array_push($listaCardGeneri,recuperaInfo($idFilmCorrente));
        }

        $listaCard=creaListaCardClassificata($listaCardGeneri);
        $categoriaCard=file_get_contents('../componenti/categoria_index.html');
        $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
        $categoriaCard=str_replace('%spazio%',"",$categoriaCard);
        $categoriaCard=str_replace('%categoria%',"<h2 id=\"topGenere\">Top 5 per genere</h2>",$categoriaCard);
        $categoriaCard=str_replace('%vediAltro%',"",$categoriaCard);
        return $categoriaCard;   
    }
    
    function pulsanteVediAltro($nomeCategoria,$collegamento, $limite){
        $pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
        $pulsanteVediAltro=str_replace('%nomeCategoria%',$nomeCategoria,$pulsanteVediAltro);
        $pulsanteVediAltro=str_replace('%collegamento%',$collegamento,$pulsanteVediAltro);
        $pulsanteVediAltro=str_replace('%limite%', $limite ,$pulsanteVediAltro);
        return $pulsanteVediAltro;
    }

    //recupera le informazioni per creare le card nuove uscite
    function recuperaNuoveUscite($limite){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY annoUscita DESC LIMIT $limite";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();

        $listaCard=creaListaCard($ris);
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
    
    function recuperaSceltiPerTe($limite){
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
        GROUP BY film.ID ORDER BY valutazione,annoUscita DESC LIMIT $limite";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();
        $listaCard=creaListaCard($ris);
        if(!$listaCard)
            return false;
        else{
            $pulsanteVediAltro=file_get_contents('../componenti/vediAltro.html');
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
            $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
            if(isset($_GET['nomeCategoria']))
                $categoriaCard=str_replace('%categoria%',"",$categoriaCard);
            else
                $categoriaCard=str_replace('%categoria%',"<h2 id=\"nuove\">Scelti per te</h2>",$categoriaCard);
            $pulsanteVediAltro=str_replace('%nomeCategoria%',"Scelti per te",$pulsanteVediAltro);
            $categoriaCard=str_replace('%vediAltro%',$pulsanteVediAltro,$categoriaCard);
            $categoriaCard=str_replace('%collegamento%',"film_categoria.php",$categoriaCard);
            return $categoriaCard;
        }
    }

    function recuperaPerGenere($limite, $genere){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
        path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
        ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) 
        WHERE nome_genere='".$genere."' GROUP BY id ORDER BY valutazione,annoUscita LIMIT $limite";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();
        $listaCard=creaListaCard($ris);
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

    function recuperaRaccoltaPersonale(&$raccoltaCardNoleggi,&$raccoltaCardAcquisti){
        //acquisti dell'utente
        $queryCardAcquisti="SELECT film.ID as id,titolo,copertina,TIME_TO_SEC(durata) as durata,
        path as copertina,descrizione FROM film JOIN appartenenzaNoDoppioni
    ON(film.ID=appartenenzaNoDoppioni.ID_film) JOIN genere ON (appartenenzaNoDoppioni.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) JOIN acquisto ON (film.ID=acquisto.ID_film) WHERE acquisto.ID_utente=".$_SESSION['id'];
        $queryCardNoleggi="SELECT film.ID as id,titolo,copertina,TIME_TO_SEC(durata) as durata,
        path as copertina,descrizione,scadenza_noleggio FROM film JOIN appartenenzaNoDoppioni 
        ON(film.ID=appartenenzaNoDoppioni.ID_film) JOIN genere ON (appartenenzaNoDoppioni.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) JOIN noleggio ON (film.ID=noleggio.ID_film) WHERE noleggio.ID_utente=".$_SESSION['id'] ;
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $risAcquisti=$connessione->interrogaDB($queryCardAcquisti);
        $risNoleggi=$connessione->interrogaDB($queryCardNoleggi);
        $connessione->chiudiConnessione();
        $listaNoleggi=creaListaCardPersonale($risNoleggi);
        $listaAcquisti=creaListaCardPersonale($risAcquisti);

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
        //restituisce i nuovi film con le valutazioni piú alte (non é esattamente un top 5 settimana)
        //per essere un TOP5sett devono venire inseriti almeno 5 film ogni settimana
    function recuperaTop5Sett(){
        $queryCard="SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN, path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) GROUP BY film.ID ORDER BY valutazione DESC,annoUscita DESC LIMIT 5";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();

        $listaCard=creaListaCardClassificata($ris);
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
    function recuperaPiuVisti(){
        $queryCard="SELECT idfilm,data_uscita,SUM(somma) as risultato from (SELECT acquisto.ID_film as idfilm,count(*) as somma from acquisto GROUP BY idfilm UNION ALL SELECT noleggio.ID_film as idfilm,count(*) as somma from noleggio GROUP BY idfilm) as tot JOIN film ON (tot.idfilm=film.ID) GROUP BY idfilm ORDER BY data_uscita DESC,risultato DESC LIMIT 10";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryCard);
        $connessione->chiudiConnessione();
        //creo l'array con le info dei film in base agli id dei 10 restituiti dalla query precedente
        $filmPiuVisti=array();
        if($ris){
            foreach($ris as $valore){
                array_push($filmPiuVisti,recuperaInfo($valore['idfilm']));
                }
        }
        
        $listaCard=creaListaCardClassificata($filmPiuVisti);
        if(!$listaCard)
            return false;
        else{
            $categoriaCard=file_get_contents('../componenti/categoria_index.html');
        $categoriaCard=str_replace('%listaCard%',$listaCard,$categoriaCard);
        $categoriaCard=str_replace('%spazio%',"",$categoriaCard);
        $categoriaCard=str_replace('%categoria%',"<h2 id=\"visti\">Top 10 piú visti</h2>",$categoriaCard);
        
        $categoriaCard=str_replace('%vediAltro%',"",$categoriaCard);
        
        return $categoriaCard;
        }
    }

?>