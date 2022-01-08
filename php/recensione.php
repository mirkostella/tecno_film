<?php

require_once ('sessione.php');
require_once ('connessione.php');
require_once ('stelle.php');
require_once ('giudizio.php');

//valutazione contiene la stringa che rappresenta le stelle date al film
abstract class Recensione{
    
    public $id;
    public $idFilm;
    public $idUtente;
    public $profilo;
    public $username;
    public $data;
    public $testo;
    public $valutazione;
    
//costruisce una recensione passando un array associativo
    public function __construct(&$array){
        if(isset($array['id']))
            $this->id=$array['id'];
        $this->idFilm=$array['idFilm'];
        $this->idUtente=$array['idUtente'];
        if(isset($array['profilo']))
            $this->profilo=$array['profilo'];
        if(isset($array['username']))
            $this->username=$array['username'];
        $this->valutazione=$array['valutazione'];
        $this->data=$array['data'];
        $this->testo=$array['testo'];
    }
    public function getUtile(){
        $queryLike="SELECT count(*) as nLike FROM utile JOIN recensione ON (utile.ID_recensione=recensione.ID) WHERE recensione.ID=$this->id";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ris=$connessione->interrogaDB($queryLike);
        $connessione->chiudiConnessione();
        $nLike=array_pop($ris)['nLike'];
        return $nLike;
    }
    public function getSegnalazioni(){
        $querySegnalazioni="SELECT count(*) as nSegnalazioni FROM segnalazione JOIN recensione ON (segnalazione.ID_recensione=recensione.ID) WHERE recensione.ID=$this->id";
        $connessione=new Connessione();
        $connessione->apriConn();
        $ris=$connessione->queryToArray($querySegnalazioni);
        $connessione->chiudiConn();
        $nSegnalazioni=array_pop($ris)['nSegnalazioni'];
        return $nSegnalazioni;
    }

    public function aggiungiBase(){
        $rec=file_get_contents("../componenti/recensione.html");
        $rec=str_replace('%idFilm%',$this->idFilm,$rec);
        $rec=str_replace('%username%',$this->username,$rec);
        $rec=str_replace('%path%',$this->profilo,$rec);
        $rec=str_replace('%valutazione%',creaStelle($this->valutazione),$rec);  
        $rec=str_replace('%data%',$this->data,$rec);
        $rec=str_replace('%testo%',$this->testo,$rec);
        $rec=str_replace('%idRecensione%',$this->id,$rec);
        return $rec;
    }
    abstract function crea();

}
               
class RecensioneUtente extends Recensione{

    public function __construct(&$array){
        Recensione::__construct($array);
    }

    public function crea(){
        $rec=Recensione::aggiungiBase();
        $rec=str_replace('%idRecensione%',$this->id,$rec);
        $rec=str_replace('%segnalazioni%',"",$rec);
        $rec=str_replace('%like%',"<span class=\"grassetto\">Recensione piaciuta a: </span>".$this->getUtile()." persone",$rec);
        if($_SESSION['loggato']==true && $_SESSION['id']==$this->idUtente){
            $rec=str_replace('%pulsanteUtile%',"",$rec);
            $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
            $rec=str_replace('%elimina%',"<input type=\"submit\" value=\"Elimina\" name=\"eliminaRecensione\" class=\"btn btnRecensione\">",$rec);
        }
        if($_SESSION['loggato']==true && $_SESSION['id']!=$this->idUtente){
                $rec=str_replace('%elimina%',"",$rec);
                $utile=new Utile($_SESSION['id'],$this->id);
                $segnalazione=new Segnalazione($_SESSION['id'],$this->id);
                if(!$utile->getUtile()){
                    $rec=str_replace('%pulsanteUtile%',"<input type=\"submit\" value=\"Utile\" name=\"utile\" class=\"btn btnRecensione\">",$rec);
                }
                else{
                    $rec=str_replace('%pulsanteUtile%',"<input type=\"submit\" value=\"Annulla Utile\" name=\"annullaUtile\" class=\"btn btnRecensione\">",$rec);
                }
                if(!$segnalazione->getSegnalazioni()){
                    $rec=str_replace('%pulsanteSegnalazione%',"<input type=\"submit\" value=\"Segnala\" name=\"segnala\" class=\"btn btnRecensione\">",$rec);
                }
                else{
                    $rec=str_replace('%pulsanteSegnalazione%',"<input type=\"submit\" value=\"Rimuovi segnalazione\" name=\"annullaSegnalazione\" class=\"btn btnRecensione\">",$rec);
                }

                
            }
            if($_SESSION['loggato']==false){
                $rec=str_replace('%pulsanteUtile%',"",$rec);
            $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
            $rec=str_replace('%elimina%',"",$rec);
            }
        return $rec;
    }
    //non controlla gli errori ma fa solo l'inserimento
    public function aggiungiDB(){
        $connessione=new Connessione();
        $connessione->apriConnessione(); 
        $ins="INSERT INTO recensione (ID_film,ID_utente,testo,data,valutazione) VALUES 
        ($this->idFilm,$this->idUtente,\"$this->testo\",\"$this->data\",$this->valutazione)";
        if($connessione->eseguiQuery($ins)){
            $connessione->chiudiConnessione();
            return true;
        }
        else{
            return  false;
        }
    }
    //false se fallisce
    static public function elimina($idRecensione){
        $connessione=new Connessione();
        $connessione->apriConnessione(); 
        $rec="DELETE FROM recensione WHERE ID=$idRecensione";
        $ok=$connessione->eseguiQuery($rec);
        $connessione->chiudiConnessione();
        return $ok;
    }
    
    static public function segnala($idRecensione){
        $segnalazione=new Segnalazione($_SESSION['id'],$idRecensione);
        return $segnalazione->inserisci();
    }
    static public function utile($idRecensione){
        $utile=new Utile($_SESSION['id'],$idRecensione);
        return $utile->inserisci();
    }
    static public function rimuoviSegnala($idRecensione){
        $segnalazione=new Segnalazione($_SESSION['id'],$idRecensione);
        return $segnalazione->rimuovi();
    }
    static public function rimuoviUtile($idRecensione){
        $utile=new Utile($_SESSION['id'],$idRecensione);
        return $utile->rimuovi();
    }

    //controlla che il testo e la valutazione siano stati inseriti correttamente e restituisce un array con i messaggi di errore
    public function controlloErr(){
        $err=array(
            'errTesto' => "",
            'errValutazione' => ""
        );
        if($this->testo=="")
            $err['errTesto']="campo testo obbligatorio";
        else{
            if(strlen($this->testo)<50 || strlen($this->testo)>500){
                $err['errTesto']="il testo deve essere lungo tra i 50 e i 100 caratteri";
            }
        }
        if($this->valutazione==""){
            $err['errValutazione']="campo valutazione obbligatorio";
        }
        return $err;
    }
    //conta il numero di errori di una recensione
    public function numErr($arrayErr){
        $num=0;
        foreach($arrayErr as $key=>$valore){
            if($valore!="")
                $num++;
        }
        return $num;
    }
}
class RecensioneAdmin extends Recensione{

    public function __construct(&$array){
        Recensione::__construct($array);
    }

    public function crea(){
        $rec=Recensione::aggiungiBase();
        $rec=str_replace('%like%',"",$rec);
        $rec=str_replace('%segnalazioni%',"<span class=\"grassetto\">Recensione segnalata da: </span>".$this->getSegnalazioni()." persone",$rec);
        $rec=str_replace('%pulsanteUtile%',"",$rec);
        $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
        $rec=str_replace('%elimina%','<input type="submit" value="Elimina" class="btn btnRecensione">',$rec);
        return $rec;
    }

}

?>
