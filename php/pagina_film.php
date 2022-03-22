<?php
    
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('gestore_recensioni.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('recensione.php');
    require_once ('ResocontoRecensioni.php');
    require_once ('info_film.php');
    
    
    $idFilm="";
    if(isset($_POST['idFilm']))
        $idFilm=$_POST['idFilm'];
    
    if(isset($_GET['idFilm']))
        $idFilm=$_GET['idFilm'];

    $pagina=file_get_contents('../html/pagina_film.html');
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,"","");

    $struttura->aggiungiAcquistoNoleggio($pagina);

    $infoFilm=recuperaInfo($idFilm);
    $infoGeneriFilm=recuperaGeneri($idFilm);

    $film=new Card($infoFilm,$infoGeneriFilm);
    $pagina=str_replace('%idFilm%',$film->id,$pagina);
    $pagina=str_replace('%titolo%',$film->titolo,$pagina);
    $pagina=str_replace('%path%',$film->copertina,$pagina);
    $pagina=str_replace('%annoUscita%',$film->annoUscita,$pagina);
    $pagina=str_replace('%durata%',$film->durata,$pagina);
    $stringaGeneri='';
    $copiaGeneriFilm=$film->genere;
    $primoGenere=array_pop($copiaGeneriFilm)['generiFilm'];
    $stringaGeneri=$stringaGeneri.$primoGenere;
    foreach($copiaGeneriFilm as &$valore){
        $prossimoGenere=$valore['generiFilm'];
        $stringaGeneri=$stringaGeneri.' , '.$prossimoGenere;
    }
    $pagina=str_replace('%genere%',$stringaGeneri,$pagina);
    $pagina=str_replace('%prezzoN%',$film->prezzoN,$pagina);
    $pagina=str_replace('%prezzoA%',$film->prezzoA,$pagina);
    $pagina=str_replace('%trama%',$film->trama,$pagina);
    $pagina=str_replace('%valutazione%',creaStelle($film->valutazione),$pagina);

    //recensioni utenti
    $ordineRecensioni='recenti';
    if(isset($_GET['applica']))
        $ordineRecensioni=$_GET['ordine'];

    $gestore=new GestoreRecensioni($idFilm,$ordineRecensioni);

    //se eliminare recensione
    if(isset($_GET['eliminaRecensione']))
        $gestore->gestisciEliminaRecensione($_GET['idRecensione'],$pagina);
    

    //se inserire recensione
    $errTesto="";
    $errValutazione="";
    $testoNuovaRecensione="";
    $valutazioneNuovaRecensione="";
    if(isset($_POST['inviaRecensione'])){
        $testo=$_POST['testoRecensione'];
        $valutazione=$_POST['valutazioneRecensione'];
        $data=date('Y/m/d H:i:s',time());
        $datiRecensione=array(
            'idFilm'=>$idFilm,
            'idUtente'=>$_SESSION['id'],
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
    $gestore->gestisciInserisciRecensione($nuovaRecensione,$pagina);
    }

    if(isset($_GET['utile']))
        $gestore->gestisciUtileRecensione($_GET['idRecensione'],$pagina);
        
    if(isset($_GET['segnala']))
        $gestore->gestisciSegnalaRecensione($_GET['idRecensione'],$pagina);
        
    if(isset($_GET['annullaUtile']))
        $gestore->gestisciAnnullaUtileRecensione($_GET['idRecensione'],$pagina);
        
    if(isset($_GET['annullaSegnalazione']))
        $gestore->gestisciAnnullaSegnalazioneRecensione($_GET['idRecensione'],$pagina);

              
    //decido se inserire il form per l'inserimento di una recensione
    if($gestore->controlloPresenzaRecensioneUtente()){
//hai giá inserito una recensione
    $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
    $pagina=str_replace('%formRecensione%',"Hai giá inserito una recensione per questo film",$pagina);
    }
        
    else{
        if($_SESSION['loggato']){
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
            $pagina=str_replace('%formRecensione%',"Accedi per inserire una recensione",$pagina);
        }          
    }

    //creo il grafico recensioni
    $grafico=new resocontoRecensioni($idFilm);
    $grafico->creaGrafico($pagina);
    //visualizzo le recensioni degli utenti
    $stringaRecensioni=$gestore->visualizzaRecensioni();
    $stringaFiltroRecensioni=$gestore->visualizzaFiltro();

    $pagina=str_replace('%idFilm%',$idFilm,$pagina); 
    $pagina=str_replace('%listaRecensioni%',$stringaRecensioni,$pagina);
    $pagina=str_replace('%filtro%',$stringaFiltroRecensioni,$pagina); 

    echo $pagina;  
?>