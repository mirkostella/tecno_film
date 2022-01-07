<?php
     require_once ('connessione.php');

    abstract class Giudizio{
        public $id_utente;
        public $id_recensione;

        public function __construct($utente,$recensione){
            $this->id_utente=$utente;
            $this->id_recensione=$recensione;
        }
        abstract public function inserisci();
        abstract public function rimuovi();
    }

    class Utile extends Giudizio{
        public function inserisci(){
            $connessione=new Connessione();
            $connessione->apriConnessione(); 
            $rec="INSERT INTO utile (ID_utente,ID_recensione) VALUES ($this->id_utente,$this->id_recensione)";
            $ok=$connessione->eseguiQuery($rec);
            $connessione->chiudiConnessione();
            return $ok;
        }
        public function rimuovi(){
            $connessione=new Connessione();
            $connessione->apriConnessione(); 
            $rec="DELETE FROM utile WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->eseguiQuery($rec);
            $connessione->chiudiConnessione();
            return $ok;
        }
        //ritorna true se é presente utile nel db 
        public function getUtile(){
            $connessione=new Connessione();
            $connessione->apriConnessione(); 
            $rec="SELECT * FROM utile WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->interrogaDB($rec);
            $connessione->chiudiConnessione();
            if($ok && count($ok)>0)
                return true;
            else
                return false;
        }
    }

    class Segnalazione extends Utile{
        public function inserisci(){
            $connessione=new Connessione();
            $connessione->apriConnessione(); 
            $rec="INSERT INTO segnalazione (ID_utente,ID_recensione) VALUES ($this->id_utente,$this->id_recensione)"; 
            $ok=$connessione->eseguiQuery($rec);
            $connessione->chiudiConnessione();
            return $ok; 
        } 
        
        public function rimuovi(){
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $rec="DELETE FROM segnalazione WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->eseguiQuery($rec);
            $connessione->chiudiConnessione();
            return $ok;
        }
        public function getSegnalazioni(){
            $connessione=new Connessione();
            $connessione->apriConnessione(); 
            $rec="SELECT * FROM segnalazione WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->interrogaDB($rec);
            $connessione->chiudiConnessione();
            if($ok && count($ok)>0)
                return true;
            else
                return false;
        }
    }

?>