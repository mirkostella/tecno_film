<?php

    //inclusione dei file 
    require_once("sessione.php");
    require_once("struttura.php");
    require_once("connessione.php");
    require_once ("info_utente.php");

    if($_SESSION['admin']==false){
        header('location: login_admin.php');
        exit();
    }
    
    $pagina=file_get_contents("../html/amministratore_loggato.html");

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura = new Struttura();
    $struttura->aggiungiHeader_admin($pagina);
    $struttura->aggiungMenu_admin($pagina,'<li><a href="amministratore_loggato.php">Vista generale</a></li>',"<li id=\"attivo\">Vista Generale</li>");

    $query_utente="SELECT ID,username, email,stato From utente";
    $risultato=$connessione->interrogaDB($query_utente);

    
    $righe = ''; // creo righe della tabella utenti
    foreach($risultato as $value) { //per ogni riga della query 
        $rigaUtente = file_get_contents("../componenti/utente_tab.html"); //prendo la struttura della riga nel file 
        $segnalazioni=trovaSegnalazioni($value['ID'],$connessione);
        //mappo i dati della riga con i relativi campi
        $rigaUtente=str_replace("%idUtente%",$value['ID'],$rigaUtente);
        $rigaUtente=str_replace("%username%",$value['username'],$rigaUtente);
        $rigaUtente=str_replace("%stato%",$value['stato'],$rigaUtente);
        $rigaUtente=str_replace("%email%",$value['email'],$rigaUtente);
        $rigaUtente=str_replace("%nSegnalazioni%",$segnalazioni,$rigaUtente);

        //aggiungo la righa alle righe della tabella
        $righe .= $rigaUtente;
    }

    $tabellaUtenti = file_get_contents("../componenti/tabella_utenti.html"); //prendo la struttura della tabella degli utenti
    $tabellaUtenti=str_replace("%rUtenti%",$righe,$tabellaUtenti); //sostituisco la stringa %rUtenti% con le righe costruite sopra 
        
    /* ...................parte film................................*/

    $query_film="SELECT film.ID, film.titolo, film.prezzo_noleggio, film.prezzo_acquisto, n_noleggi.N_noleggi, n_acquisti.N_acquisti FROM film LEFT JOIN n_noleggi ON (film.ID = n_noleggi.ID) LEFT JOIN n_acquisti ON (film.ID = n_acquisti.ID)";

    $tabellaFilm = file_get_contents("../componenti/tabella_film.html");
    $risultato_f=$connessione->interrogaDB($query_film);
    
    $totNoleggi=0;
    $totAcquisti=0;
    $totINoleggi=0;
    $totIAcquisti=0;
    $totIncassi=0;

    $righe_f = ''; // creo righe della tabella film
    foreach($risultato_f as $value_f) { //per ogni riga della query 
        $rigaFilm = file_get_contents("../componenti/film_tab.html"); //prendo la struttura della riga nel file 
        //mappo i dati della riga con i relativi campi

        $pNoleggi=$value_f['prezzo_noleggio']*$value_f['N_noleggi'];
        $pAcquisti=$value_f['prezzo_acquisto']*$value_f['N_acquisti'];

        $rigaFilm=str_replace("%idFilm%",$value_f['ID'],$rigaFilm);
        $rigaFilm=str_replace("%titolo%",$value_f['titolo'],$rigaFilm);
        $rigaFilm=str_replace("%prezzoN%",$value_f['prezzo_noleggio'],$rigaFilm);
        $rigaFilm=str_replace("%prezzoA%",$value_f['prezzo_acquisto'],$rigaFilm);
        $rigaFilm=str_replace("%noleggi%",$value_f['N_noleggi'] == null ? 0 : $value_f['N_noleggi'] ,$rigaFilm);
        $rigaFilm=str_replace("%acquisti%",$value_f['N_acquisti'] == null ? 0: $value_f['N_acquisti'],$rigaFilm);
        $rigaFilm=str_replace("%pNoleggi%",$pNoleggi,$rigaFilm);
        $rigaFilm=str_replace("%pAcquisti%",$pAcquisti,$rigaFilm);
        $rigaFilm=str_replace("%pNoleggiAcquisti%",$pNoleggi + $pAcquisti ,$rigaFilm);

        $totNoleggi+=$value_f['N_noleggi'];
        $totAcquisti+=$value_f['N_acquisti'];
        $totINoleggi+=$pNoleggi;
        $totIAcquisti+=$pAcquisti;
        $totIncassi+=($pAcquisti+$pNoleggi);

        //aggiungo la righa alle righe della tabella
        $righe_f .= $rigaFilm;
    }

    $tabellaFilm=str_replace("%rFilm%",$righe_f,$tabellaFilm);

    $tabellaFilm=str_replace("%totNoleggi%",$totNoleggi,$tabellaFilm);
    $tabellaFilm=str_replace("%totAcquisti%",$totAcquisti,$tabellaFilm);
    $tabellaFilm=str_replace("%totIAcquisti%",$totIAcquisti,$tabellaFilm);
    $tabellaFilm=str_replace("%totINoleggi%",$totINoleggi,$tabellaFilm);
    $tabellaFilm=str_replace("%totIncassi%",$totIncassi,$tabellaFilm);
    
    
    $pagina=str_replace("%tabellaUtenti%",$tabellaUtenti,$pagina);
    $pagina=str_replace("%tabellaFilm%",$tabellaFilm,$pagina);

    $connessione->chiudiConnessione();
    
    echo $pagina;
?>