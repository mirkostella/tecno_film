<?php

    //inclusione dei file 
    require_once("sessione.php");
    require_once("struttura.php");
    require_once("connessione.php");
    require_once ("info_utente.php");
    require_once ("lingua.php");

    if($_SESSION['admin']==false){
        header('location: ../php/login_admin.php');
        exit();
    }
    
    $pagina=file_get_contents("../html/riepilogo_admin.html");

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura = new Struttura();
    $struttura->aggiungiBaseAdmin($pagina);
    $pagina=str_replace("%descrizione%","Se sei un admin, qua trovi un riepilogo degli utenti e un riepilogo del numero di acquisti e noleggi per film, con relativi incassi", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Riepilogo, Utenti, Segnalazioni, Film, Noleggi, Acquist, Prezzo, Incassi", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm-Admin: Riepilogo", $pagina);
    $pagina=str_replace("%breadcrumb%","<span class=\"grassetto\">Riepilogo</span>", $pagina);

    $pagina=str_replace('<li><a href="../php/riepilogo_admin.php" accesskey="r">Riepilogo</a></li>',"<li id=\"attivo\" accesskey=\"r\">Riepilogo</li>", $pagina);

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

    $pagina=str_replace("%rUtenti%",$righe,$pagina); //sostituisco la stringa %rUtenti% con le righe costruite sopra 
        
    /* ...................parte film................................*/

    $query_film="SELECT film.ID, film.titolo, film.prezzo_noleggio, film.prezzo_acquisto, n_noleggi.N_noleggi, n_acquisti.N_acquisti FROM film LEFT JOIN n_noleggi ON (film.ID = n_noleggi.ID) LEFT JOIN n_acquisti ON (film.ID = n_acquisti.ID)";

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

        $rigaFilm=str_replace("%idFilm%","Film".$value_f['ID'],$rigaFilm);
        $rigaFilm=str_replace("%titolo%",aggiungiSpanLang($value_f['titolo']),$rigaFilm);
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

    $pagina=str_replace("%rFilm%",$righe_f,$pagina);

    $pagina=str_replace("%totNoleggi%",$totNoleggi,$pagina);
    $pagina=str_replace("%totAcquisti%",$totAcquisti,$pagina);
    $pagina=str_replace("%totIAcquisti%",$totIAcquisti,$pagina);
    $pagina=str_replace("%totINoleggi%",$totINoleggi,$pagina);
    $pagina=str_replace("%totIncassi%",$totIncassi,$pagina);

    $connessione->chiudiConnessione();
    
    echo $pagina;
?>