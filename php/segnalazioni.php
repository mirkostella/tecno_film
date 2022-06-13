<?php

    //inclusione dei file 
    require_once ('connessione.php');
    require_once("struttura.php");
    require_once ('info_utente.php');
    require_once ('recensione.php');

    if($_SESSION['admin']==false){
        header('location: login_admin.php');
        exit();
    }

    $pagina=file_get_contents("../html/segnalazioni.html");
    $struttura = new Struttura();
    $struttura->aggiungiHeader_admin($pagina);
    $struttura->aggiungMenu_admin($pagina);

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    $id=$_GET['id'];
    
    $query_info_utente="SELECT utente.ID as u_ID, username, nome, cognome, sesso, stato, email, data_nascita, foto_utente.path as path
    FROM utente JOIN foto_utente ON (utente.ID_foto = foto_utente.ID) WHERE utente.ID=$id";
    $risultato_info_utente=$connessione->interrogaDB($query_info_utente);

    $pagina=str_replace('%username%',$risultato_info_utente[0]['username'],$pagina);
    $pagina=str_replace('%nomeUtente%',$risultato_info_utente[0]['nome'],$pagina);
    $pagina=str_replace('%cognomeUtente%',$risultato_info_utente[0]['cognome'],$pagina);
    $pagina=str_replace('%dataUtente%',$risultato_info_utente[0]['data_nascita'],$pagina);
    $pagina=str_replace('%emailUtente%',$risultato_info_utente[0]['email'],$pagina);
    $pagina=str_replace('%sessoUtente%',$risultato_info_utente[0]['sesso'],$pagina);
    $pagina=str_replace('%statoUtente%',$risultato_info_utente[0]['stato'],$pagina);
    $pagina=str_replace('%path%',$risultato_info_utente[0]['path'], $pagina);

    $segnalazioni=trovaSegnalazioni($risultato_info_utente[0]['u_ID'],$connessione);
    $pagina=str_replace('%numSegnalazioni%',$segnalazioni,$pagina);

    $query_recensione = "SELECT recensione.ID as id,recensione.ID_film as idFilm,recensione.ID_utente as idUtente,
    foto_utente.path as profilo,
    utente.username as username,recensione.data as data,recensione.testo as testo,recensione.valutazione as valutazione
    
    FROM utente JOIN recensione 
    on (utente.ID=recensione.ID_utente) JOIN foto_utente ON (utente.ID_foto=foto_utente.ID) 
    WHERE ID_utente=$id";

    $risultato_recensione=$connessione->interrogaDB($query_recensione);
    $stringaRecensioni="";
    foreach($risultato_recensione as $ris){
        $rec=new RecensioneAdmin($ris);
        $stringaRecensione=$rec->crea();
        $stringaRecensioni=$stringaRecensioni.$stringaRecensione;

    }
    $pagina=str_replace('%recensioni%',$stringaRecensioni,$pagina);
    $pagina=str_replace('%idUtente%',$id,$pagina);

    echo $pagina;
?>