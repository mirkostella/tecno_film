<?php
    
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('gestore_recensioni.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('recensione.php');
    require_once ('ResocontoRecensioni.php');
    require_once ('info_film.php');
    require_once ('info_utente.php');
    require_once ('controlli_form.php');
    require_once ('lingua.php');

    date_default_timezone_set("Europe/Rome");
    $data=date("Y-m-d H:m:s");
    
    $idFilm=$_REQUEST['idFilm'];
    $pagina=file_get_contents('../html/pagina_film.html');

    if($_SESSION['loggato'] == true && $_SESSION['admin'] == true){
        $_SESSION['loggato']=false;
        $_SESSION['admin']=false;
    }

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $pagina=str_replace("%descrizione%","Informazioni e recensioni del film %titoloNoLang%", $pagina);
    $pagina=str_replace("%keywords%","%titoloNoLang%, TecnoFilm, %genereNoLang%, Acquisto, Noleggio, recensione", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: %titoloLang%", $pagina);
    $pagina=str_replace("%breadcrumb%", "<a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a> &gt; <span class=\"grassetto\">%titoloLang%</span>", $pagina);

    if(isset($_GET['confermaAcquisto'])){
        //se é giá stato fatto l'acquisto indirizzo l'utente alla pagina del film
        $queryPresenzaAcquisto="SELECT* FROM acquisto JOIN utente ON(acquisto.ID_utente=utente.ID) JOIN film ON(acquisto.ID_film=film.ID) WHERE utente.ID=".$_SESSION['id']." AND film.ID=".$_GET['idFilm'];
        
        if(!$connessione->interrogaDB($queryPresenzaAcquisto)){

            $queryAcquisto="INSERT INTO acquisto (ID_film,ID_utente,data_acquisto) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",'".$data."')";
            if($connessione->eseguiQuery($queryAcquisto)){
                $pagina=str_replace('%esitoAcquistoNoleggio%',"<div class=\"success_box\">Il tuo acquisto è andato a buon fine. Buona visione!</div>",$pagina);
            }
            else{
                $pagina=str_replace('%esitoAcquistoNoleggio%',"<div class=\"error_box\">Il tuo acquisto non è andato a buon fine. Riprova.</div>",$pagina);
            }
        }
    }

    if(isset($_GET['confermaNoleggio'])){

        $queryPresenzaNoleggio="SELECT* FROM noleggio JOIN utente ON(noleggio.ID_utente=utente.ID) JOIN film ON(noleggio.ID_film=film.ID) WHERE noleggio.scadenza_noleggio > CURRENT_TIMESTAMP AND utente.ID=".$_SESSION['id']." AND film.ID=".$_GET['idFilm'];

        if(!$connessione->interrogaDB($queryPresenzaNoleggio)){
            $scadenzaNoleggio=date("Y-m-d H:m:s",mktime(0, 0, 0, date("m"),   date("d")+7,   date("Y")));
            $queryNoleggio="INSERT INTO noleggio (ID_film,ID_utente,data_noleggio,scadenza_noleggio) VALUES (".$_GET['idFilm'].",".$_SESSION['id'].",'".$data."','".$scadenzaNoleggio."')";
            if($connessione->eseguiQuery($queryNoleggio)){
                $pagina=str_replace('%esitoAcquistoNoleggio%',"<div class=\" msg_box success_box\">Il tuo noleggio è andato a buon fine. Buona visione!</div>",$pagina);
            }
            else{
                $pagina=str_replace('%esitoAcquistoNoleggio%',"<div class=\"error_box\">Il tuo noleggio non è andato a buon fine. Riprova.</div>",$pagina);
            }
        }
    }

    $struttura->aggiungiAcquistoNoleggio($connessione, $pagina);

    //recensioni utenti
    $ordineRecensioni='recenti';
    if(isset($_GET['applica']))
        $ordineRecensioni=$_GET['ordine'];

    $gestore=new GestoreRecensioni($connessione, $idFilm,$ordineRecensioni);
    
    //se eliminare recensione
    if(isset($_GET['eliminaRecensione']))
        $gestore->gestisciEliminaRecensione($connessione, $_GET['idRecensione'],$pagina);
    

    //se inserire recensione
    $errTesto="";
    $errValutazione="";
    $testoNuovaRecensione="";
    $valutazioneNuovaRecensione=""; 
    if(isset($_POST['inviaRecensione']) && !$gestore->controlloPresenzaRecensioneUtente($connessione)){
        if($_SESSION['admin']==false){
            $testo=test_input($_POST['testoRecensione']);
            $valutazione=trim($_POST['valutazioneRecensione']);
            $data=date('Y/m/d H:i:s',time());
            $queryDati="SELECT username, path FROM utente JOIN foto_utente ON (utente.ID_foto=foto_utente.ID) WHERE utente.ID = '".$_SESSION['id']."'";
            $ris=$connessione->interrogaDB($queryDati);
            $datiRecensione=array(
                'idFilm'=>$idFilm,
                'idUtente'=>$_SESSION['id'],
                'username' => $ris[0]['username'],
                'profilo'=> $ris[0]['path'],
                'data'=>$data,
                'testo'=>$testo,
                'valutazione'=>$valutazione
            );

            $nuovaRecensione=new RecensioneUtente($datiRecensione);
            $errTesto=$nuovaRecensione->getMessaggioErrori()['errTesto'];
            $errValutazione=$nuovaRecensione->getMessaggioErrori()['errValutazione'];
            $testoNuovaRecensione=$nuovaRecensione->getTesto();
            $valutazioneNuovaRecensione=$nuovaRecensione->getValutazione();

    //se la recensione non viene inserita ripristino i campi della form
            if($gestore->gestisciInserisciRecensione($connessione, $nuovaRecensione,$pagina)){
                $pagina=str_replace('%formRecensione%',"",$pagina);
            }
            else
                header('location: ../php/login.php');

        }
    }   


    if(isset($_GET['utile']))
        $gestore->gestisciUtileRecensione($connessione, $_GET['idRecensione'],$pagina);
        
    if(isset($_GET['segnala']))
        $gestore->gestisciSegnalaRecensione($connessione, $_GET['idRecensione'],$pagina);
        
    if(isset($_GET['annullaUtile']))
        $gestore->gestisciAnnullaUtileRecensione($connessione, $_GET['idRecensione'],$pagina);
        
    if(isset($_GET['annullaSegnalazione']))
        $gestore->gestisciAnnullaSegnalazioneRecensione($connessione, $_GET['idRecensione'],$pagina);

              
    if(controlloStatoBloccato($connessione, $_SESSION['id'])){
        $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
        $pagina=str_replace('%formRecensione%',"Il tuo account risulta bloccato. Gli utenti bloccati non possono inserire una recensione.",$pagina);
    }

    //decido se inserire il form per l'inserimento di una recensione
    else if($gestore->controlloPresenzaRecensioneUtente($connessione)){
        //hai giá inserito una recensione
        $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
        $pagina=str_replace('%formRecensione%',"Hai giá inserito una recensione per questo film",$pagina);
    }
        
    else{
        if($_SESSION['loggato'] && $_SESSION['admin']==false){
            //inserire il form
            $formRecensione=file_get_contents("../componenti/ins_recensione.html");
            $formRecensione=str_replace('%id%',$idFilm,$formRecensione);
            $formRecensione=str_replace('%testoRecensione%',$testoNuovaRecensione,$formRecensione);
            $formRecensione=str_replace('<option value="'.$valutazioneNuovaRecensione.'">'.$valutazioneNuovaRecensione.' stelle</option>','<option value="'.$valutazioneNuovaRecensione.'" selected>'.$valutazioneNuovaRecensione.' stelle</option>',$formRecensione);
            $formRecensione=str_replace('%errTesto%',$errTesto,$formRecensione);
            $formRecensione=str_replace('%errValutazione%',$errValutazione,$formRecensione);
            $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
            $pagina=str_replace('%formRecensione%',$formRecensione,$pagina);
        }
        else{
            //accedi per inserire una recensione
            $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
            $pagina=str_replace('%formRecensione%',"<p>Per inserire una recensione devi essere loggato. <a href=\"../php/login.php\">Accedi qui</a></p>",$pagina);
        }          
    }

    //creo il grafico recensioni
    $grafico=new resocontoRecensioni($connessione, $idFilm);
    $grafico->creaGrafico($pagina);
    //visualizzo le recensioni degli utenti
    $stringaRecensioni=$gestore->visualizzaRecensioni($connessione);
    if(!$stringaRecensioni)
        $pagina= str_replace('Recensioni degli altri utenti', 'Non sono ancora presenti recensioni per questo film', $pagina);
        
    $stringaFiltroRecensioni=$gestore->visualizzaFiltro($connessione);

    $infoFilm=recuperaInfo($connessione, $idFilm);
    $infoGeneriFilm=recuperaGeneri($connessione, $idFilm);

    $film=new Card($infoFilm,$infoGeneriFilm);

    $pagina=str_replace('%idFilm%',$film->id,$pagina);
    $titoloLang=aggiungiSpanLang($film->titolo);
    $titoloNoLang=eliminaDelimitatoriLingua($film->titolo);
    $pagina=str_replace('%titoloLang%',$titoloLang,$pagina);
    $pagina=str_replace('%titoloNoLang%',$titoloNoLang,$pagina);
    $pagina=str_replace('%path%',$film->copertina,$pagina);
    $descrizione=eliminaDelimitatoriLingua($film->descrizione);
    $pagina=str_replace('%desc%',$descrizione,$pagina);
    $pagina=str_replace('%annoUscita%',$film->annoUscita,$pagina);
    $pagina=str_replace('%durata%',$film->durata,$pagina);
    $stringaGeneri='';
    $copiaGeneriFilm=$film->genere;
    $primoGenere=array_pop($copiaGeneriFilm)['generiFilm'];
    $stringaGeneri=$stringaGeneri.$primoGenere;
    foreach($copiaGeneriFilm as &$valore){
        $prossimoGenere=$valore['generiFilm'];
        $stringaGeneri=$stringaGeneri.', '.$prossimoGenere;
    }
    $stringaGeneriLang=aggiungiSpanLang($stringaGeneri);
    $stringaGeneriNoLang=eliminaDelimitatoriLingua($stringaGeneri);
    $pagina=str_replace('%genere%',$stringaGeneriLang,$pagina);
    $pagina=str_replace('%genereNoLang%',$stringaGeneriNoLang,$pagina);
    $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
    $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
    $tramaLang=aggiungiSpanLang($film->trama);
    $pagina=str_replace('%trama%',$tramaLang,$pagina);
    $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);

    $pagina=str_replace('%idFilm%',$idFilm,$pagina); 
    $pagina=str_replace('%listaRecensioni%',$stringaRecensioni,$pagina);
    $pagina=str_replace('%filtro%',$stringaFiltroRecensioni,$pagina); 

    $pagina=str_replace('%esitoAcquistoNoleggio%',"",$pagina);

    $connessione->chiudiConnessione();
    echo $pagina;  
?>