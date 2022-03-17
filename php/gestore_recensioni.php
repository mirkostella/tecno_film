<?php

require_once('sessione.php');
require_once('connessione.php');    
print_r($_SESSION);

//gestisce le recensioni nella pagina film
class GestoreRecensioni{
    public $idUtenteLoggato=null;
    public $idFilm=null;
    public $listaRecensioni ;
    public $ordineRecensioni;

    public function __construct($idF,$ordine){
        if($_SESSION['loggato']==true)
            $this->idUtenteLoggato=$_SESSION['id'];
        $this->idFilm=$idF;
        $this->ordineRecensioni=$ordine; 
        $queryRecuperoRecensioni="";

        if($this->ordineRecensioni=='recenti')
            $queryRecuperoRecensioni="SELECT recensione.ID as idRecensione,recensione.ID_film,recensione.ID_utente,foto_utente.path,utente.username,recensione.testo,recensione.valutazione,recensione.data FROM recensione JOIN film ON (recensione.ID_film=film.ID) JOIN utente ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID_foto=foto_utente.ID) WHERE film.ID=".$this->idFilm." ORDER BY data DESC";
        
        if($this->ordineRecensioni=='piaciuti')
            $queryRecuperoRecensioni="SELECT recensione.ID as idRecensione,recensione.ID_film,recensione.ID_utente,foto_utente.path,utente.username,recensione.testo,recensione.valutazione,recensione.data FROM recensione JOIN film ON (recensione.ID_film=film.ID) JOIN utente ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID_foto=foto_utente.ID) WHERE film.ID=".$this->idFilm." ORDER BY valutazione DESC";

        
        if($this->ordineRecensioni=='mRecenti')
            $queryRecuperoRecensioni="SELECT recensione.ID as idRecensione,recensione.ID_film,recensione.ID_utente,foto_utente.path,utente.username,recensione.testo,recensione.valutazione,recensione.data FROM recensione JOIN film ON (recensione.ID_film=film.ID) JOIN utente ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID_foto=foto_utente.ID) WHERE film.ID=".$this->idFilm." ORDER BY data ASC";
        
        if($this->ordineRecensioni=='mPiaciuti')
            $queryRecuperoRecensioni="SELECT recensione.ID as idRecensione,recensione.ID_film,recensione.ID_utente,foto_utente.path,utente.username,recensione.testo,recensione.valutazione,recensione.data FROM recensione JOIN film ON (recensione.ID_film=film.ID) JOIN utente ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID_foto=foto_utente.ID) WHERE film.ID=".$this->idFilm." ORDER BY valutazione ASC";
        
        if($this->ordineRecensioni=='miaRecensione')
            $queryRecuperoRecensioni="SELECT recensione.ID as idRecensione,recensione.ID_film,recensione.ID_utente,foto_utente.path,utente.username,recensione.testo,recensione.valutazione,recensione.data FROM recensione JOIN film ON (recensione.ID_film=film.ID) JOIN utente ON (recensione.ID_utente=utente.ID) JOIN foto_utente ON(utente.ID_foto=foto_utente.ID) WHERE film.ID=".$this->idFilm." AND utente.ID=".$this->idUtenteLoggato;
        
        $connessione=new connessione();
        $connessione->apriConnessione();
              
        $recensioni=$connessione->interrogaDB($queryRecuperoRecensioni); 
        $this->listaRecensioni=array();
        if($recensioni){
            foreach($recensioni as $valore){
                $datiRecensione=array(
                    'id'=>$valore['idRecensione'],
                    'idFilm'=>$valore['ID_film'],
                    'idUtente'=>$valore['ID_utente'],
                    'data'=>$valore['data'],
                    'testo'=>$valore['testo'],
                    'valutazione'=>$valore['valutazione']
            );
            $nuovaRecensione=new RecensioneUtente($datiRecensione);
            array_push($this->listaRecensioni,$nuovaRecensione);
            }
        }         
        $connessione->chiudiConnessione();       
    }

    public function visualizzaRecensioni(){
        //stringa che rappresenta il codice html da visualizzare (sostituire al segnaposto corrispondente)
        //se non sono presenti recensini ritorna la stringa vuota
        $listaRecensioni="";
        if(count($this->listaRecensioni)!=0){
            foreach($this->listaRecensioni as $recensione){
            $stringaRecensione=$recensione->crea();
            $listaRecensioni=$listaRecensioni.$stringaRecensione;
        }
        return $listaRecensioni;
    }
    else
        return "";       
    }
    //se presente una recensione dell'utente loggato restituisce true; se non loggato o non presente false
    public function controlloPresenzaRecensioneUtente(){
        $connessione=new Connessione();
        $connessione->apriConnessione();
        if($this->idUtenteLoggato){
            $queryPresenzaRecensione="SELECT * FROM recensione WHERE recensione.ID_utente='".$this->idUtenteLoggato."' and recensione.ID_film=".$this->idFilm;
            $presenzaRecensione=$connessione->interrogaDB($queryPresenzaRecensione);
            if($presenzaRecensione){
                return true;  
            }
                
        }
        return false;
    
    }
    //ritorna la stringa da sostituire al segnaposto per il filtro recensioni
    public function visualizzaFiltro(){
        $htmlFiltro=file_get_contents('../componenti/filtro.html');
        $selezionato="";
        $inSelezionato="";
        if($this->listaRecensioni){
            if($this->ordineRecensioni=='recenti'){
                $selezionato="<option value=\"recenti\">Piú recenti</option>";
                $inSelezionato="<option value=\"recenti\" selected>Piú recenti</option>";
            }
            if($this->ordineRecensioni=='piaciuti'){
                $selezionato="<option value=\"piaciuti\">Piú piaciuti</option>";
                $inSelezionato="<option value=\"piaciuti\" selected>Piú piaciuti</option>";
            }
            if($this->ordineRecensioni=='mRecenti'){
                $selezionato="<option value=\"mRecenti\">Meno recenti</option>";
                $inSelezionato="<option value=\"mRecenti\" selected>Meno recenti</option>";
            }
            if($this->ordineRecensioni=='mPiaciuti'){
                $selezionato="<option value=\"mPiaciuti\">Meno piaciuti</option>";
                $inSelezionato="<option value=\"mPiaciuti\" selected>Meno piaciuti</option>";
            }
            if($this->ordineRecensioni=='miaRecensione'){
                $selezionato="<option value=\"miaRecensione\">La mia recensione</option>";
                $inSelezionato="<option value=\"miaRecensione\" selected>La mia recensione</option>";
            }
            $htmlFiltro=str_replace('%focus%',"autofocus",$htmlFiltro);
            if(!$this->controlloPresenzaRecensioneUtente())
                $htmlFiltro=str_replace('%disattiva%',"disabled",$htmlFiltro);
            else
                $htmlFiltro=str_replace('%disattiva%',"",$htmlFiltro);
            $htmlFiltro=str_replace('%idFilm%',$this->idFilm,$htmlFiltro);
            $htmlFiltro=str_replace($selezionato,$inSelezionato,$htmlFiltro);
            return $htmlFiltro;
        }
        else
            return "";
    }

    public function gestisciEliminaRecensione($idRecensione,&$pagina){

        //elimino la recensione
    if(RecensioneUtente::elimina($idRecensione)){
        $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione eliminata con successo!",$pagina);
        for($pos=0;$pos<count($this->listaRecensioni);++$pos){
            if($this->listaRecensioni[$pos]->getID()==$idRecensione)
                unset($this->listaRecensioni[$pos]);
        }
        echo 'campo lista recensioni dopo eliminazione'; 
        print_r($this->listaRecensioni);      
    }
    else
        $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
}

//true se recensione inserita false altrimenti
public function gestisciInserisciRecensione($nuovaRecensione,&$pagina){
    //prima di procedere controllo se é giá presente una recensione dell'utente
    if($this->controlloPresenzaRecensioneUtente($pagina)){
        $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
        return false;
    }
    else
    //se NON sono presenti errori ed inserisco la recensione
    if(!$nuovaRecensione->getNumErrori()){
        $idNuovaRecensione=$nuovaRecensione->aggiungiDB()['ID'];
        if($idNuovaRecensione){
            $nuovaRecensione->setID($idNuovaRecensione);
            $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione inserita correttamente!",$pagina);
            array_push($this->listaRecensioni,$nuovaRecensione);
           
            return true;
        }           
        else{
            $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione non inserita:per favore riprova piú tardi ",$pagina);
            return false;
        }
    }
    //sono presenti errori nella compilazione della nuova recensione e stampo i messaggi di errore
    else{
        $pagina=str_replace('%messaggioEsitoRecensione%',"Inserimento non avvenuto",$pagina);
        return false;
    }

}
public function gestisciUtileRecensione($idRecensione,&$pagina){
    if(RecensioneUtente::utile($idRecensione))
        $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione valutata Utile con successo!",$pagina);
    else
        $pagina=str_replace('%messaggioEsitoRecensione%',"Valutazione recensione fallita... Per favore riprova piú tardi ",$pagina);
}
public function gestisciSegnalaRecensione($idRecensione,&$pagina){
    if(RecensioneUtente::segnala($idRecensione))
        $pagina=str_replace('%messaggioEsitoRecensione%',"Recensione Segnalata con successo!",$pagina);
    else
        $pagina=str_replace('%messaggioEsitoRecensione%',"Segnalazione recensione fallita... Per favore riprova piú tardi ",$pagina);

}
public function gestisciAnnullaUtileRecensione($idRecensione,&$pagina){
    if(RecensioneUtente::rimuoviUtile($idRecensione))
        $pagina=str_replace('%messaggioEsitoRecensione%',"Utile rimosso dalla recensione con successo!",$pagina);
    else
        $pagina=str_replace('%messaggioEsitoRecensione%',"Rimozione utile fallita... Per favore riprova piú tardi",$pagina);

}
public function gestisciAnnullaSegnalazioneRecensione($idRecensione,&$pagina){
    if(RecensioneUtente::rimuoviSegnala($idRecensione))
        $pagina=str_replace('%messaggioEsitoRecensione%',"Segnalazione rimossa dalla recensione con successo!",$pagina);
    else
        $pagina=str_replace('%messaggioEsitoRecensione%',"Rimozione segnalazione fallita... Per favore riprova piú tardi ",$pagina);

}

    //fine classe GestoreRecensioni
}


?>





