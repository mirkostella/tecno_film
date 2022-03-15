<?php

    //inclusione dei file 
    require_once ('connessione.php');
    require_once ('info_utente.php');
    require_once ('recensione.php');
    $pagina=file_get_contents("../html/segnalazioni.html");

    $connessione=new Connessione();

    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    $id=$_GET['id'];
    
    $query_info_utente="select ID,username, nome,cognome,sesso,stato,email,data_nascita
    from utente where id=$id";
    $risultato_info_utente=$connessione->interrogaDB($query_info_utente);

    $pagina=str_replace('%username%',$risultato_info_utente[0]['username'],$pagina);
    $pagina=str_replace('%nomeUtente%',$risultato_info_utente[0]['nome'],$pagina);
    $pagina=str_replace('%cognomeUtente%',$risultato_info_utente[0]['cognome'],$pagina);
    $pagina=str_replace('%dataUtente%',$risultato_info_utente[0]['data_nascita'],$pagina);
    $pagina=str_replace('%emailUtente%',$risultato_info_utente[0]['email'],$pagina);
    $pagina=str_replace('%sessoUtente%',$risultato_info_utente[0]['sesso'],$pagina);
    $pagina=str_replace('%statoUtente%',$risultato_info_utente[0]['stato'],$pagina);

    $query_foto_utente="select path from foto_utente where ID = $id";
    $risultato_foto_utente=$connessione->interrogaDB($query_foto_utente);
    $pagina=str_replace('%immagine profilo%',$risultato_foto_utente[0]['path'],$pagina);

    $segnalazioni=trovaSegnalazioni($risultato_info_utente[0]['ID'],$connessione);
    $pagina=str_replace('%numSegnalazioni%',$segnalazioni,$pagina);

    /*$query_recensione="select testo,ID_utente,data from recensione where ID_utente=$id";
    $risultato_recensione=$connessione->interrogaDB($query_recensione);
    $rec=new RecensioneUtente($risultato_recensione[0]);*/

    echo $pagina;
?>