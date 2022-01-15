<?php
    
    
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('recensione.php');
    require_once ('ResocontoRecensioni.php');
    require_once ('sessione.php');
    require_once ('info_film.php');
    
    $idFilm="";
    if(isset($_POST['inviaRecensione'])){
        $idFilm=$_POST['idFilm'];
    }
    else
        $idFilm=$_GET['idFilm'];
    
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

    //se sono arrivato alla pagina eliminando una recensione
    if(isset($_GET['eliminaRecensione'])){
        $idRecensione=$_GET['idRecensione'];
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
            'idFilm'=>$idFilm,
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
    if(isset($_GET['utile'])){
        RecensioneUtente::utile($_GET['idRecensione']);
    }
    if(isset($_GET['segnala'])){
        RecensioneUtente::segnala($_GET['idRecensione']);
    }
    if(isset($_GET['annullaUtile'])){
        RecensioneUtente::rimuoviUtile($_GET['idRecensione']);
    }
    if(isset($_GET['annullaSegnalazione'])){
        RecensioneUtente::rimuoviSegnala($_GET['idRecensione']); 
    }
    
        $struttura->aggiungiHeader($pagina);
        $struttura->aggiungiAccount($pagina);
        $struttura->aggiungiMenu($pagina,"","");

        //reperisco le informazioni relative al film
        //creo array con i dati del film che mi interessano
        $info=array();

        //preparo le query
        $infoFilm=recuperaInfo($idFilm);

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
        $grafico=new resocontoRecensioni($idFilm);
        $grafico->creaGrafico($pagina);

        $queryRecensioniUtenti="";
        $selezionato="";
        $inSelezionato="";
        $focus=false;
        //recensioni utenti
        if(isset($_GET['applica'])){
            $focus=true;
            if($_GET['ordine']=='recenti'){
                $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$idFilm." ORDER BY data DESC";
                $selezionato="<option value=\"recenti\">Piú recenti</option>";
                $inSelezionato="<option value=\"recenti\" selected>Piú recenti</option>";
            }
            if($_GET['ordine']=='piaciuti'){
                $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$idFilm." ORDER BY valutazione DESC";
                $selezionato="<option value=\"piaciuti\">Piú piaciuti</option>";
                $inSelezionato="<option value=\"piaciuti\" selected>Piú piaciuti</option>";
            }
            if($_GET['ordine']=='mRecenti'){
                $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$idFilm." ORDER BY data ASC";
                $selezionato="<option value=\"mRecenti\">Meno recenti</option>";
                $inSelezionato="<option value=\"mRecenti\" selected>Meno recenti</option>";

            }
            if($_GET['ordine']=='mPiaciuti'){
                $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$idFilm." ORDER BY valutazione ASC";
                $selezionato="<option value=\"mPiaciuti\">Meno piaciuti</option>";
                $inSelezionato="<option value=\"mPiaciuti\" selected>Meno piaciuti</option>";
            }
            }
        else
            $queryRecensioniUtenti="SELECT recensione.ID as idRecensione,ID_film,ID_utente,path,username,data,testo,valutazione FROM utente JOIN recensione ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID=foto_utente.ID) WHERE ID_film=".$idFilm." ORDER BY data ASC";
       
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $recensioniUtenti=$connessione->interrogaDB($queryRecensioniUtenti);
        //controllo che siano presenti delle recensioni
        $listaRecensioni="";
        if($recensioniUtenti){
            $struttura->aggiungiFiltro($pagina,$selezionato,$inSelezionato,$focus);
            //lista con tutte le recensioni relative al film 
            foreach($recensioniUtenti as $key=>$valore){
                $data=date_create($valore['data']);
                $datiRecensione=array(
                    'id'=>$valore['idRecensione'],
                    'idFilm'=>$valore['ID_film'],
                    'idUtente'=>$valore['ID_utente'],
                    'profilo'=>$valore['path'],
                'username'=>$valore['username'],
                'data'=>date_format($data, 'd-m-Y H:i:s'),
                'testo'=>$valore['testo'],
                'valutazione'=>$valore['valutazione']
            );
            $recensione=new RecensioneUtente($datiRecensione);
            $stringaRecensione=$recensione->crea();
            $listaRecensioni=$listaRecensioni.$stringaRecensione;
        }
    }
    else
        $pagina=str_replace('%filtro%',"",$pagina);


        $pagina=str_replace('%idFilm%',$idFilm,$pagina); 
        $pagina=str_replace('%listaRecensioni%',$listaRecensioni,$pagina); 
        echo $pagina;  
?>