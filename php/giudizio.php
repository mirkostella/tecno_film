<?php

    abstract class Giudizio{
        public $id_utente;
        public $id_recensione;

        public function __construct($utente,$recensione){
            $this->id_utente=$utente;
            $this->id_recensione=$recensione;
        }
        abstract public function inserisci($connessione);
        abstract public function rimuovi($connessione);
    }

    class Utile extends Giudizio{
        public function inserisci($connessione){ 
            $rec="INSERT INTO utile (ID_utente,ID_recensione) VALUES ($this->id_utente,$this->id_recensione)";
            $ok=$connessione->eseguiQuery($rec);
            return $ok;
        }
        public function rimuovi($connessione){
            $rec="DELETE FROM utile WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->eseguiQuery($rec);
            return $ok;
        }
        //ritorna true se é presente utile nel db 
        public function getUtile($connessione){
            $rec="SELECT * FROM utile WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->interrogaDB($rec);
            if($ok && count($ok)>0)
                return true;
            else
                return false;
        }
    }

    class Segnalazione extends Utile{
        public function inserisci($connessione){ 
            $rec="INSERT INTO segnalazione (ID_utente,ID_recensione) VALUES ($this->id_utente,$this->id_recensione)"; 
            $ok=$connessione->eseguiQuery($rec);
            return $ok; 
        } 
        
        public function rimuovi($connessione){
            $rec="DELETE FROM segnalazione WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->eseguiQuery($rec);
            return $ok;
        }
        public function getSegnalazioni($connessione){
            $rec="SELECT * FROM segnalazione WHERE ID_utente=$this->id_utente AND ID_recensione=$this->id_recensione";
            $ok=$connessione->interrogaDB($rec);
            if($ok && count($ok)>0)
                return true;
            else
                return false;
        }
    }

?>