<?php

require_once ('sessione.php');
require_once ('stelle.php');
require_once ('giudizio.php');
require_once ('info_utente.php');

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
    public $messaggioErrori;
    public $nErrori;
 
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
        $err=array(
            'errTesto' => "",
            'errValutazione' => ""
        );
        if($array['testo']=="")
            $err['errTesto']="Campo testo obbligatorio";
        else{
            if(strlen($array['testo'])<50 || strlen($array['testo'])>500){
                $err['errTesto']="Il testo deve essere lungo tra i 50 e i 100 caratteri";
            }
        }
        if($array['valutazione']==""){
            $err['errValutazione']="Campo valutazione obbligatorio";
        }
        $this->messaggioErrori=$err;
        $num=0;
        foreach($err as $key=>$valore){
            if($valore!="")
                $num++;
        }
        $this->nErrori=$num;
        
    }
    public function getTesto(){
        return $this->testo;
    }
    public function getValutazione(){
        return $this->valutazione;
    }
    public function getMessaggioErrori(){
        return $this->messaggioErrori;
    }
    public function getNumErrori(){
        return $this->nErrori;
    }
    public function getData(){
        return $this->data;
    }
    public function getID(){
        return $this->id;
    }
    public function setID($nuovoID){
        $this->id=$nuovoID;
    }
    public function getIDFilm(){
        return $this->idFilm;
    }
    public function getIDUtente(){
        return $this->idUtente;
    }
    public function getProfilo(){
        return $this->profilo;
    }
    public function getUsername(){
        return $this->username;
    }

    public function getUtile($connessione){
        $queryLike="SELECT count(*) as nLike FROM utile JOIN recensione ON (utile.ID_recensione=recensione.ID) WHERE recensione.ID=$this->id";
        $ris=$connessione->interrogaDB($queryLike);
        $nLike=0;
        if($ris)
            $nLike=array_pop($ris)['nLike'];
        return $nLike;
    }

    public function getSegnalazioni($connessione){
        $querySegnalazioni="SELECT count(*) as nSegnalazioni FROM segnalazione JOIN recensione ON (segnalazione.ID_recensione=recensione.ID) WHERE recensione.ID=$this->id";
        $ris=$connessione->interrogaDB($querySegnalazioni);
        $nSegnalazioni=array_pop($ris)['nSegnalazioni'];
        return $nSegnalazioni;
    }

    public function aggiungiBase(){
        $rec=file_get_contents("../componenti/recensione.html");
        $rec=str_replace('%id%',$this->getIDUtente(),$rec);
        $rec=str_replace('%idFilm%',$this->idFilm,$rec);
        $rec=str_replace('%username%',$this->username,$rec);
        $rec=str_replace('%path%',$this->profilo,$rec);
        $rec=str_replace('%valutazione%',creaStelle($this->valutazione),$rec);  
        $rec=str_replace('%data%',$this->data,$rec);
        $rec=str_replace('%testo%',$this->testo,$rec);
        $rec=str_replace('%idRecensione%',$this->id,$rec);
        return $rec;
    }
    abstract function crea($connessione);
    //false se fallisce
    static public function elimina($connessione, $idRecensione){
        $rec="DELETE FROM recensione WHERE ID=$idRecensione";
        $ok=$connessione->eseguiQuery($rec);
        return $ok;
    }

}
               
class RecensioneUtente extends Recensione{

    public function __construct(&$array){
        Recensione::__construct($array);
    }

    public function crea($connessione){
        $rec=Recensione::aggiungiBase();
        $rec=str_replace('%idRecensione%',$this->id,$rec);
        $rec=str_replace('%destinazione%',"pagina_film.php",$rec);
        $rec=str_replace('%segnalazioni%',"",$rec);
        $rec=str_replace('%like%',"<span class=\"grassetto\">Recensione piaciuta a: </span>".$this->getUtile($connessione)." persone",$rec);
        if($_SESSION['loggato']==true && $_SESSION['id']==$this->idUtente){
            $rec=str_replace('%pulsanteUtile%',"",$rec);
            $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
            $rec=str_replace('%elimina%',"<input type=\"submit\" value=\"Elimina\" name=\"eliminaRecensione\" class=\"btn btnRecensione\">",$rec);
        }
        if($_SESSION['loggato']==true && $_SESSION['id']!=$this->idUtente){
                $rec=str_replace('%elimina%',"",$rec);
                $utile=new Utile($_SESSION['id'],$this->id);
                $segnalazione=new Segnalazione($_SESSION['id'],$this->id);
                if(!controlloStatoBloccato($connessione, $_SESSION['id'])){
                    if(!$utile->getUtile($connessione)){
                        $rec=str_replace('%pulsanteUtile%',"<input type=\"submit\" value=\"Utile\" name=\"utile\" class=\"btn btnRecensione\">",$rec);
                    }
                    else{
                        $rec=str_replace('%pulsanteUtile%',"<input type=\"submit\" value=\"Annulla Utile\" name=\"annullaUtile\" class=\"btn btnRecensione\">",$rec);
                    }
                    if(!$segnalazione->getSegnalazioni($connessione)){
                        $rec=str_replace('%pulsanteSegnalazione%',"<input type=\"submit\" value=\"Segnala\" name=\"segnala\" class=\"btn btnRecensione\">",$rec);
                    }
                    else{
                        $rec=str_replace('%pulsanteSegnalazione%',"<input type=\"submit\" value=\"Rimuovi segnalazione\" name=\"annullaSegnalazione\" class=\"btn btnRecensione\">",$rec);
                    }
                }
                else
                {
                    if(!$utile->getUtile($connessione)){
                        $rec=str_replace('%pulsanteUtile%',"",$rec);
                    }
                    else{
                        $rec=str_replace('%pulsanteUtile%',"<input type=\"submit\" value=\"Annulla Utile\" name=\"annullaUtile\" class=\"btn btnRecensione\">",$rec);
                    }
                    if(!$segnalazione->getSegnalazioni($connessione)){
                        $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
                    }
                    else{
                        $rec=str_replace('%pulsanteSegnalazione%',"<input type=\"submit\" value=\"Rimuovi segnalazione\" name=\"annullaSegnalazione\" class=\"btn btnRecensione\">",$rec);
                    }
                }
            }
            if($_SESSION['loggato']==false){
                $rec=str_replace('%pulsanteUtile%',"",$rec);
                $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
                $rec=str_replace('%elimina%',"",$rec);
            }
        return $rec;
    }
    
    
    //inserisce senza controllare errori
    //ritorna l'id della recensione appena inserita altrimenti false
    public function aggiungiDB($connessione){ 
        $ins="INSERT INTO recensione (ID_film,ID_utente,testo,data,valutazione) VALUES 
        ($this->idFilm,$this->idUtente,\"$this->testo\",\"$this->data\",$this->valutazione)";
        if($connessione->eseguiQuery($ins)){
            $queryIdNuovaRecensione="SELECT ID FROM recensione WHERE ID_film=".$this->idFilm." AND ID_utente=".$this->idUtente;
            $idNuovaRecensione=$connessione->interrogaDB($queryIdNuovaRecensione);
            return $idNuovaRecensione[0];
        }
        else{
            return  false;
        }
    }
      
    static public function segnala($connessione, $idRecensione){
        $segnalazione=new Segnalazione($_SESSION['id'],$idRecensione);
        return $segnalazione->inserisci($connessione);
    }
    static public function utile($connessione, $idRecensione){
        $utile=new Utile($_SESSION['id'],$idRecensione);
        return $utile->inserisci($connessione);
    }
    static public function rimuoviSegnala($connessione, $idRecensione){
        $segnalazione=new Segnalazione($_SESSION['id'],$idRecensione);
        return $segnalazione->rimuovi($connessione);
    }
    static public function rimuoviUtile($connessione, $idRecensione){
        $utile=new Utile($_SESSION['id'],$idRecensione);
        return $utile->rimuovi($connessione);
    }

    
}
class RecensioneAdmin extends Recensione{

    public function __construct(&$array){
        Recensione::__construct($array);
    }

    public function crea($connessione){
        $rec=Recensione::aggiungiBase();
        $rec=str_replace('%destinazione%',"segnalazioni.php",$rec);
        $rec=str_replace('%like%',"",$rec);
        $rec=str_replace('%segnalazioni%',"<span class=\"grassetto\">Recensione segnalata da: </span>".$this->getSegnalazioni($connessione)." persone",$rec);
        $rec=str_replace('%pulsanteUtile%',"",$rec);
        $rec=str_replace('%pulsanteSegnalazione%',"",$rec);
        $rec=str_replace('%elimina%','<input type="submit" value="Elimina" name="eliminaRecensione" class="btn btnRecensione">',$rec);
        return $rec;
    }
}

?>
