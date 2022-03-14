<?php

    require_once ('sessione.php');
    require_once ('connessione.php');

    date_default_timezone_set("Europe/Rome");
    $data=date("Y-m-d H:m:s");
    $pagina=file_get_contents('../html/esito_transazione.html');
    $pagina=str_replace('%idFilm%',$_GET['idFilm'],$pagina);
    $connessione=new Connessione();
    $connessione->apriConnessione();
    if(isset($_GET['confermaAcquisto'])){
        //se é giá stato fatto l'acquisto indirizzo l'utente alla pagina del film
        $queryPresenzaAcquisto="SELECT* FROM acquisto JOIN utente ON(acquisto.ID_utente=utente.ID) JOIN film ON(acquisto.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id']." AND film.ID=".$_GET['idFilm'];
        
        if($connessione->interrogaDB($queryPresenzaAcquisto)){
            header('location: pagina_film.php?idFilm='.$_GET['idFilm']);
            exit;
        }
        $queryAcquisto="INSERT INTO acquisto (ID_film,ID_utente,data_acquisto) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",'".$data."')";
        if($connessione->eseguiQuery($queryAcquisto)){
            $pagina=str_replace('%esito%',"<h1 class=\"esitoPositivo\">Transazione avvenuta con successo!</h1>",$pagina);
            $pagina=str_replace('%infoAggiuntive%',"Puoi trovare il film nella tua raccolta personale",$pagina);
            $pagina=str_replace('%immagineEsito%',"<img src=\"../img/img_sfondi/successo.jpeg\"> id=\"imgEsito\"",$pagina);
        }
        else{
            $pagina=str_replace('%esito%',"<h1>Ti preghiamo di riprovare piú tardi..</h1>",$pagina);
            $pagina=str_replace('%infoAggiuntive%',"",$pagina);
            $pagina=str_replace('%immagineEsito%',"<img src=\"../img/img_sfondi/errore.jpeg\"  id=\"imgEsito\">",$pagina);
        }
    }


    if(isset($_GET['confermaNoleggio'])){
        $queryPresenzaNoleggio="SELECT* FROM noleggio JOIN utente ON(noleggio.ID_utente=utente.ID) JOIN film ON(noleggio.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id']." AND film.ID=".$_GET['idFilm'];
        if($connessione->interrogaDB($queryPresenzaNoleggio)){
            header('location: pagina_film.php?idFilm='.$_GET['idFilm']);
            exit;
        }
        $scadenzaNoleggio=date("Y-m-d H:m:s",mktime(0, 0, 0, date("m"),   date("d")+7,   date("Y")));
        $queryNoleggio="INSERT INTO noleggio (ID_film,ID_utente,data_noleggio,scadenza_noleggio) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",'".$data."','".$scadenzaNoleggio."')";
        if($connessione->eseguiQuery($queryNoleggio)){
            $pagina=str_replace('%esito%',"<h1 class=\"esitoPositivo\">Transazione avvenuta con successo!",$pagina);
            $pagina=str_replace('%infoAggiuntive%',"Puoi trovare il film nella tua raccolta personale fino al termine del periodo di noleggio",$pagina);
            $pagina=str_replace('%immagineEsito%',"<img src=\"../img/img_sfondi/successo.jpeg\" id=\"imgEsito\">",$pagina);
        }
        else{
            $pagina=str_replace('%esito%',"<h1>Ti preghiamo di riprovare piú tardi..</h1>",$pagina);
            $pagina=str_replace('%infoAggiuntive%',"",$pagina);
            $pagina=str_replace('%immagineEsito%',"<img src=\"../img/img_sfondi/errore.jpeg\" id=\"imgEsito\">",$pagina);
        }
    }
    $connessione->chiudiConnessione();
    echo $pagina;


?>