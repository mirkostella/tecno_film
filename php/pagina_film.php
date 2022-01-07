<?php
    
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('recensione.php');
    require_once ('ResocontoRecensioni.php');
    require_once ('sessione.php');
    require_once ('info_film.php');

    
    date_default_timezone_set("Europe/Rome");
    $data=date("d/m/Y H:m:s");
    
    echo "pagina_film";
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
    
    $pagina=file_get_contents('../html/pagina_film.html');
    $struttura=new Struttura();
    $struttura->aggiungiAcquistoNoleggio($pagina);
    $provaElimina=false;
    $provaInserisci=false;
    $erroreTesto="";
    $erroreValutazione="";

    //acquisto
    if(isset($_POST['acquisto'])){}

    //noleggio
    if(isset($_POST['noleggio'])){}

    //se sono arrivato alla pagina eliminando una recensione
    if(isset($_POST['eliminaRecensione'])){
        $idRecensione=$_POST['idRecensione'];
        $provaElimina=false;
        if(RecensioneUtente::elimina($idRecensione)){
            $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione eliminata con successo!",$pagina);
        }
        else{
            $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione non eliminata..",$pagina);
        }
    }
    else 
    //se sono arrivato alla pagina inserendo una recensione
    if(isset($_POST['inviaRecensione'])){
        $provaInserisci=true;
        $testo=$_POST['testoRecensione'];
        $valutazione=$_POST['valutazioneRecensione'];
        
        $datiRecensione=array(
            'idFilm'=>$_POST['idFilm'],
            'idUtente'=>$_SESSION['id'],
            'data'=>$data,
            'testo'=>$testo,
            'valutazione'=>$valutazione
        );
        $nuovaRecensione=new RecensioneUtente($datiRecensione);
        //controllo che la recensione sia stata inserita correttamente
        $errori=$nuovaRecensione->controlloErr();
        $numErr=$nuovaRecensione->numErr($errori);
        
        if($numErr==0){
            if($nuovaRecensione->aggiungiDB()){
                $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione inserita con successo!",$pagina);
            }
            else{
                $pagina=str_replace('%messaggioEsitoRecensione%',"Connessione con il DataBase fallita..",$pagina);
            }
        }
        else{
            //stampo gli errori per ogni campo
            $pagina=str_replace('%messaggioEsitoRecensione%',"Sono presenti $numErr campi compilati incorrettamente",$pagina);
            $erroreTesto=$errori['errTesto'];
            $erroreValutazione=$errori['errValutazione'];
        }
    }
    $struttura->aggiungiFormRecensione($pagina);
    if($provaElimina){
        $pagina=str_replace('%errTesto%',"",$pagina);
        $pagina=str_replace('%errValutazione%',"",$pagina);
    }
    if($provaInserisci){
        $pagina=str_replace('%errTesto%',$erroreTesto,$pagina);
        $pagina=str_replace('%errValutazione%',$erroreValutazione,$pagina);
    }
    if(!$provaElimina && !$provaInserisci){
        $pagina=str_replace('%errTesto%',"",$pagina);
        $pagina=str_replace('%errValutazione%',"",$pagina);
        $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
    }
    if(isset($_POST['utile'])){
        RecensioneUtente::utile($_POST['idRecensione']);
    }
    if(isset($_POST['segnala'])){
        RecensioneUtente::segnala($_POST['idRecensione']);
    }
    if(isset($_POST['annullaUtile'])){
        RecensioneUtente::rimuoviUtile($_POST['idRecensione']);
    }
    if(isset($_POST['annullaSegnalazione'])){
        RecensioneUtente::rimuoviSegnala($_POST['idRecensione']);
    }
    
        $struttura->aggiungiHeader($pagina);
        $struttura->aggiungiAccount($pagina);
        $struttura->aggiungiMenu($pagina,"","");

        //reperisco le informazioni relative al film
        //creo array con i dati del film che mi interessano
        $info=array();

        //preparo le query
        $infoFilm=recuperaInfo($_POST['idFilm']);

        $film=new Card($infoFilm);
        $pagina=str_replace('%idFilm%',$film->id,$pagina);
        $pagina=str_replace('%titolo%',$film->titolo,$pagina);
        $pagina=str_replace('%path%',$film->copertina,$pagina);
        $pagina=str_replace('%annoUscita%',$film->annoUscita,$pagina);
        $pagina=str_replace('%durata%',$film->durata,$pagina);
        $pagina=str_replace('%genere%',$film->genere,$pagina);
        $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
        $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
        $pagina=str_replace('%trama%',$film->trama,$pagina);
        $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);

        //fino a qui i dati del film sono stati inseriti
        //decido se far comparire o no il form per inserire una recensione

        
    
        //creo il grafico recensioni
        $grafico=new resocontoRecensioni($_POST['idFilm']);
        $grafico->creaGrafico($pagina);

        //recensioni utenti
        
        $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$_POST['idFilm']." ORDER BY data ASC";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $recensioniUtenti=$connessione->interrogaDB($queryRecensioniUtenti);
        //lista con tutte le recensioni relative al film 
        $listaRecensioni="";
        foreach($recensioniUtenti as $key=>$valore){
            $datiRecensione=array(
                'id'=>$valore['idRecensione'],
                'idFilm'=>$valore['ID_film'],
                'idUtente'=>$valore['ID_utente'],
                'profilo'=>$valore['path'],
                'username'=>$valore['username'],
                'data'=>$valore['data'],
                'testo'=>$valore['testo'],
                'valutazione'=>$valore['valutazione']
            );
            $recensione=new RecensioneUtente($datiRecensione);
            $stringaRecensione=$recensione->crea();
            $listaRecensioni=$listaRecensioni.$stringaRecensione;
        }
        $pagina=str_replace('%listaRecensioni%',$listaRecensioni,$pagina);
        echo $pagina;  
?>