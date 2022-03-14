<?php

class GestoreFilm{
    public $id=null;
    public $titolo;
    public $trama;
    public $durata;
    public $dataUscita;
    public $prezzoA;
    public $prezzoN;
    public $copertina;
    public $descrizione;
    public $genere;
    
    public function __construct($array){
        if($array['idFilm'])
            $this->id=$array['idFilm'];

        $this->titolo=$array['titolo'];
        $this->trama=$array['trama'];
        $this->durata=$array['durata'];
        $this->dataUscita=$array['dataUscita'];
        $this->prezzoA=$array['prezzoA'];
        $this->prezzoN=$array['prezzoN'];
        $this->copertina=$array['copertina'];
        $this->descrizione=$array['descrizione'];
        $this->genere=$array['genere'];
    
    }

    public function controlloPresenzaFilm(){
        //controllo per titolo del film
        $queryTuttiIFilm="SELECT film.titolo as titolo FROM film";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $films=$connessione->interrogaDB($queryTuttiIFilm);
        $connessione->chiudiConnessione();
        $presenza=false;
        foreach($films as $valore){
            if($this->titolo==$valore['titolo'])
                $presenza=true;
        }
        return $presenza;
    }

    public function getErrori(){
        //possibili errori:
        //film giá presente
        //prezzo noleggio maggiore o uguale del prezzo di acquisto
        //campo dati assente


    }
    public function inserisciFilm(){
        $queryInserimento="INSERT INTO film (titolo,copertina,trama,durata,data_uscita,prezzo_acquisto,prezzo_noleggio) VALUES 
        ($this->titolo,$this->copertina,$this->trama,$this->durata,$this->dataUscita,$this->prezzoA,$this->prezzoN)";
        $queryAppartenenza="";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $ok=$connessione->eseguiQuery($queryInserimento);
        $connessione->chiudiConnessione();
        return $ok;
    }
//fine classe GestoreFilm
}



?>