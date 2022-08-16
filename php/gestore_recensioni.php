<?php

require_once('sessione.php');  
require_once ('connessione.php');

//gestisce le recensioni nella pagina film
class GestoreRecensioni{
    public $idUtenteLoggato=null;
    public $idFilm=null;
    public $listaRecensioni ;
    public $ordineRecensioni;

    public function __construct($connessione, $idF, $ordine){
        if($_SESSION['loggato']==true && $_SESSION['admin']==false)
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
              
        $recensioni=$connessione->interrogaDB($queryRecuperoRecensioni); 
        $this->listaRecensioni=array();
        if($recensioni){
            foreach($recensioni as $valore){
                $dataRecensione=strtotime($valore['data']);
                $cambioFormato=date('d/m/Y H:i:s',$dataRecensione);
                $datiRecensione=array(
                    'id'=>$valore['idRecensione'],
                    'username'=>$valore['username'],
                    'profilo'=>$valore['path'],
                    'idFilm'=>$valore['ID_film'],
                    'idUtente'=>$valore['ID_utente'],
                    'data'=>$cambioFormato,
                    'testo'=>$valore['testo'],
                    'valutazione'=>$valore['valutazione']
            );
            $nuovaRecensione=new RecensioneUtente($datiRecensione);
            array_push($this->listaRecensioni,$nuovaRecensione);
            }
        }      
    }

    public function visualizzaRecensioni($connessione){
        //stringa che rappresenta il codice html da visualizzare (sostituire al segnaposto corrispondente)
        //se non sono presenti recensini ritorna la stringa vuota
        $listaRecensioni="";
        if(count($this->listaRecensioni)!=0){
            foreach($this->listaRecensioni as $recensione){
                $stringaRecensione=$recensione->crea($connessione);
                $listaRecensioni=$listaRecensioni.$stringaRecensione;
        }
        return $listaRecensioni;
    }
    else
        return "";       
    }

    //se presente una recensione dell'utente loggato restituisce true; se non loggato o non presente false
    public function controlloPresenzaRecensioneUtente($connessione){
        if($this->idUtenteLoggato){
            $queryPresenzaRecensione="SELECT * FROM recensione WHERE recensione.ID_utente='".$this->idUtenteLoggato."' and recensione.ID_film=".$this->idFilm;
            $presenzaRecensione=$connessione->interrogaDB($queryPresenzaRecensione);
            if($presenzaRecensione)
                return true;
        }
        return false;   
    }

    public function controlloPresenzaUtileUtente($connessione){
        if($this->idUtenteLoggato){
            $queryPresenzaUtile="SELECT * FROM utile WHERE utile.ID_utente='".$this->idUtenteLoggato."' and utile.ID_recensione=".$_GET['idRecensione'];
            $presenzaUtile=$connessione->interrogaDB($queryPresenzaUtile);
            if($presenzaUtile)
                return true;            
        }
        return false;
    }

    public function controlloPresenzaSegnalazioneUtente($connessione){
        if($this->idUtenteLoggato){
            $queryPresenzaSegnalazione="SELECT * FROM segnalazione WHERE segnalazione.ID_utente='".$this->idUtenteLoggato."' and segnalazione.ID_recensione=".$_GET['idRecensione'];
            $presenzaSegnalazione=$connessione->interrogaDB($queryPresenzaSegnalazione);
            if($presenzaSegnalazione){
                return true;  
            }
        }
        return false;
    }

    //ritorna la stringa da sostituire al segnaposto per il filtro recensioni
    public function visualizzaFiltro($connessione){
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
            if(!$this->controlloPresenzaRecensioneUtente($connessione))
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

    public function gestisciEliminaRecensione($connessione, $idRecensione,&$pagina){
        //elimino la recensione
        if(RecensioneUtente::elimina($connessione, $idRecensione)){
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Recensione eliminata con successo!</div>',$pagina);
            for($pos=0;$pos<count($this->listaRecensioni);++$pos){
                if($this->listaRecensioni[$pos]->getID()==$idRecensione)
                    unset($this->listaRecensioni[$pos]);
            }      
        }
        else
            $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
    }

    //true se recensione inserita false altrimenti
    public function gestisciInserisciRecensione($connessione, $nuovaRecensione,&$pagina){
        if(!$nuovaRecensione->getNumErrori()){
            $idNuovaRecensione=$nuovaRecensione->aggiungiDB($connessione)['ID'];
            if($idNuovaRecensione){
                $nuovaRecensione->setID($idNuovaRecensione);
                $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Recensione inserita correttamente!</div>',$pagina);
                array_push($this->listaRecensioni,$nuovaRecensione);
            
                return true;
            }           
            else{
                $pagina=str_replace('%messaggioEsitoRecensione%','<div class="error_box">Recensione non inserita:per favore riprova piú tardi </div>',$pagina);
                return false;
            }
        }
        //sono presenti errori nella compilazione della nuova recensione e stampo i messaggi di errore
        else{
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="error_box">Inserimento non avvenuto</div>',$pagina);
            return false;
        }

    }

    public function gestisciUtileRecensione($connessione, $idRecensione,&$pagina){
        if($this->controlloPresenzaUtileUtente($connessione)){
            $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
            return false;
        }
        else{
            if(RecensioneUtente::utile($connessione, $idRecensione))
                $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Recensione valutata utile con successo!</div>',$pagina);
            else
                $pagina=str_replace('%messaggioEsitoRecensione%','<div class="error_box">Valutazione recensione fallita.. riprova piú tardi</div>',$pagina);
        }
    }

    public function gestisciSegnalaRecensione($connessione, $idRecensione,&$pagina){
        if($this->controlloPresenzaSegnalazioneUtente($connessione)){
            $pagina=str_replace('%messaggioEsitoRecensione%',"",$pagina);
            return false;
        }
        else{
            if(RecensioneUtente::segnala($connessione, $idRecensione))
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Recensione segnalata con successo!</div>',$pagina);
        else
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="error_box">Segnalazione recensione fallita... per favore riprova piú tardi</div>',$pagina);
        }
    }
    public function gestisciAnnullaUtileRecensione($connessione, $idRecensione,&$pagina){
        if(RecensioneUtente::rimuoviUtile($connessione, $idRecensione))
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Utile rimosso dalla recensione con successo!</div>',$pagina);
        else
            $pagina=str_replace('%messaggioEsitoRecensione%','',$pagina);
    }

    public function gestisciAnnullaSegnalazioneRecensione($connessione, $idRecensione,&$pagina){
        if(RecensioneUtente::rimuoviSegnala($connessione, $idRecensione))
            $pagina=str_replace('%messaggioEsitoRecensione%','<div class="success_box">Segnalazione rimossa dalla recensione con successo!</div>',$pagina);
        else
            $pagina=str_replace('%messaggioEsitoRecensione%','',$pagina);
    }
    //fine classe GestoreRecensioni
}


?>





