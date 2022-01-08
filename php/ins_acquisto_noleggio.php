<?php

    require_once ('sessione.php');
    require_once ('connessione.php');

    date_default_timezone_set("Europe/Rome");
    $data=date("Y-m-d H:m:s");
    $data=
    print_r($data);
    echo "ins";
    echo "</br>";
    echo '$_SESSION:   ';
    print_r($_SESSION);
    echo "</br>";
    echo '$_GET:   ';
    print_r($_GET);
    echo "</br>";
    echo '$_POST:   ';
    print_r($_POST);
    echo "</br>";

    if(isset($_GET['acquisto'])){
        $queryAcquisto="INSERT INTO acquisto (ID_film,ID_utente,data_acquisto) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",".$data.")";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        if($connessione->eseguiQuery($queryAcquisto))
            echo "film aggiunto con successo";
        else
            echo "film non inserito";
    }
    if(isset($_GET['noleggio'])){
        $queryNoleggio="INSERT INTO noleggio (ID_film,ID_utente,data_noleggio) VALUES ()";
        $connessione=new Connessione();
        $connessione->eseguiQuery($queryNoleggio);
    }

?>