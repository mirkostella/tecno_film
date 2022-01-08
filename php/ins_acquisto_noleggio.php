<?php

    require_once ('sessione.php');
    require_once ('connessione.php');

    date_default_timezone_set("Europe/Rome");
    $data=date("Y-m-d H:m:s");
    print_r($data);
    echo "</br>";
    $scadenzaNoleggio=date("Y-m-d H:m:s",mktime(0, 0, 0, date("m"),   date("d")+7,   date("Y")));
    print_r($scadenzaNoleggio);
    
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

    $pagina=file_get_contents('../html/esito_transazione.html');
    if(isset($_GET['confermaAcquisto'])){
        $queryAcquisto="INSERT INTO acquisto (ID_film,ID_utente,data_acquisto) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",".$data.")";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        if($connessione->eseguiQuery($queryAcquisto))
            $pagina=str_replace('%esito%',"Transazione avvenuta con successo!Puoi trovare il film nella tua raccolta personale!",$pagina);
        else
            $pagina=str_replace('%esito%',"Ti preghiamo di riprovare piú tardi!",$pagina);
    }
    if(isset($_GET['confermaNoleggio'])){
        $scadenzaNoleggio=date("Y-m-d H:m:s",mktime(0, 0, 0, date("m"),   date("d")+7,   date("Y")));
        $queryNoleggio="INSERT INTO noleggio (ID_film,ID_utente,data_noleggio,scadenza_noleggio) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",".$data.",".$scadenzaNoleggio.")";
        $connessione=new Connessione();
        if($connessione->eseguiQuery($queryNoleggio))
            $pagina=str_replace('%esito%',"Transazione avvenuta con successo!Puoi trovare il film nella tua raccolta personale fino al termine del periodo di noleggio!",$pagina);
        else
            $pagina=str_replace('%esito%',"Ti preghiamo di riprovare piú tardi!",$pagina);
    }
    echo $pagina;


?>