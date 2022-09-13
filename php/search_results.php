<?php

    require_once('struttura.php');
    require_once('connessione.php');
    require_once('card.php');
    require_once('info_film.php');
    require_once('controlli_form.php');
    require_once('lingua.php');

    $pagina=file_get_contents("../html/search_results.html");

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura = new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace("%descrizione%",'Qui trovi i risultati di ricerca per le parole: '.$_GET['input_ricerca'].'', $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Risultati, Ricerca", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Risultati ricerca", $pagina);
    $pagina=str_replace("%breadcrumb%",'<a href = "../php/index.php" xml:lang = "en" lang="en">Home </a> &gt; <span class="grassetto">Risultati per "'.$_GET['input_ricerca'].'" </span>', $pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $pagina=str_replace("%input_ricerca%", $_GET['input_ricerca'], $pagina);
            
    if(isset($_GET['submit_ricerca'])){
        if(isset($_GET['input_ricerca'])){
            $inputRicerca=$connessione->pulisciStringaSQL(test_input($_GET['input_ricerca']));
            $queryTitoli = "SELECT film.ID as id, titolo FROM film";
            $risQuery = $connessione->interrogaDB($queryTitoli);
            $films = array();
            $risultatiRicerca = array();

            foreach($risQuery as &$valore){
                $valore['titolo']=eliminaDelimitatoriLingua($valore['titolo']);
                if(stristr($valore['titolo'], $inputRicerca))
                    array_push($films, $valore['id']);
            }
            
            if($films){
                foreach($films as $valore){
                    array_push($risultatiRicerca,recuperaInfo($connessione, $valore));
                    }
            }
            
            $listaCard=creaListaCard($connessione, $risultatiRicerca);
            if($listaCard){
                $pagina=str_replace("%risultatiRicerca%", $listaCard, $pagina);
                $pagina=str_replace("%classifica%", "", $pagina);
            }
            else{
                $pagina=str_replace("<dl class=\"listaCards\">%risultatiRicerca%</dl>", "<h3>Ci dispiace, non sono stati trovati risultati</h3>", $pagina);
            }
        }
    }

    $connessione->chiudiConnessione();

    echo $pagina;
?>