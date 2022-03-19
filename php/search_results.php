<?php

    require_once('struttura.php');
    require_once('connessione.php');
    require_once('card.php');
    require_once('info_film.php');

    print_r($_GET);
    $pagina=file_get_contents("../html/search_results.html");
    $struttura = new Struttura();
    $struttura->aggiungiHeader($pagina);
	$struttura->aggiungiAccount($pagina);
	$inAttivo=file_get_contents("../componenti/menu.html");
	$attivo=file_get_contents("../componenti/menu.html");
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    $pagina=str_replace("%input_ricerca%", $_GET['input_ricerca'], $pagina);
            
    if(isset($_GET['submit_ricerca'])){
        if(isset($_GET['input_ricerca'])){
            $queryRisultatiRicerca = "SELECT film.ID as id,titolo,nome_genere as genere,copertina,trama,TIME_TO_SEC(durata) as durata,data_uscita as annoUscita,prezzo_acquisto as prezzoA,prezzo_noleggio as prezzoN,
            path as copertina,descrizione,AVG(valutazione) as valutazione FROM film JOIN appartenenza 
            ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) JOIN foto_film ON(film.copertina=foto_film.ID) LEFT JOIN recensione ON (film.ID=recensione.ID_film) WHERE titolo LIKE '%".$_GET['input_ricerca']."%' GROUP BY id";
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $ris=$connessione->interrogaDB($queryRisultatiRicerca);
            $listaCard=creaListaCard($ris);
            $connessione->chiudiConnessione();
            if($listaCard){
                $pagina=str_replace("%risultatiRicerca%", $listaCard, $pagina);
                $pagina=str_replace("%classifica%", "", $pagina);
            }
            else{
                $pagina=str_replace("%risultatiRicerca%", "<h3>Ci dispiace, non sono stati trovati risultati</h3>", $pagina);
            }
        }
    }

    echo $pagina;
?>