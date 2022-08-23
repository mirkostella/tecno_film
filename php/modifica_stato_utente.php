<?php

    //inclusione dei file 
    require_once ('connessione.php');
    require_once('sessione.php');

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $id=$_GET['id'];
    $stato=$_GET['stato'];

    if($_SESSION['loggato']==true && $_SESSION['admin']==true){
        if(isset($stato)){

            $query="update utente set stato ='$stato' where ID = $id";
            $connessione->eseguiQuery($query);
        }
    }
    else{
        $connessione->chiudiConnessione();
        header('Location: ../php/login_admin.php');
        exit();
    }
    

    $connessione->chiudiConnessione();
    // redirect to page segnalazioni.php with parameter id
    header('Location: ../php/segnalazioni.php?id='.$id);
    exit();

?>