<?php

    //inclusione dei file 
    require_once ('connessione.php');

    $connessione=new Connessione();

    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    $id=$_GET['id'];
    $stato=$_GET['stato'];

    if(isset($stato)){

        $query="update utente set stato ='$stato' where ID = $id";

        $connessione->eseguiQuery($query);
    }

    // redirect to page segnalazioni.php with parameter id
    header('Location: ' . "segnalazioni.php?id=$id");

    exit();

?>