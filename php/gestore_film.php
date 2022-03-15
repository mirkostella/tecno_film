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
        
        $this->titolo=$array['titolo'];
        $this->trama=$array['trama'];
        $this->durata=$array['durata'];
        $this->dataUscita=$array['dataUscita'];
        $this->prezzoA=$array['prezzoA'];
        $this->prezzoN=$array['prezzoN'];
        $this->copertina=$array['copertina'];
        $this->descrizione=$array['descrizione'];
        $this->genere=array();
        array_push($this->genere,$array['genere']);
    
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
        $errori=array(
            'errTitolo'=>"",
            'errCopertina'=>"",
            'errAlt'=>"",
            'errTrama'=>"",
            'errDurata'=>"",
            'errNuovoGenere'=>"",
            'errData'=>"",
            'errPrezzoA'=>"",
            'errPrezzoN'=>""
        );
        //possibili errori:
        //film giá presente
        //prezzo noleggio maggiore o uguale del prezzo di acquisto
        //campo dati assente
        


    }
    public function inserisciFilm(){
        // $queryFotoCopertina="INSERT INTO foto_film (path,descrizione) VALUES 
        // ('../img/img_film/shang_chi.jpg','sono la descrizione')";

        $connessione=new Connessione();
        $connessione->apriConnessione();
        $connessione->inizioTransazione();

        // $esitoFoto=$connessione->eseguiQuery($queryFotoCopertina);
        // if(!$esitoFoto)
        //     echo 'esito foto fallito';

        //$queryIDFoto="SELECT id FROM foto_film ORDER BY id DESC LIMIT 1";
        //$IDFoto=$connessione->interrogaDB($queryIDFoto);
        //print_r($IDFoto[0]['id']);

        $queryFilm="INSERT INTO film (titolo,copertina,trama,durata,data_uscita,prezzo_acquisto,prezzo_noleggio) VALUES ('sono il titolo',15,'sono la trama',10,12,12,13)";
        
        $ok=true;
        $esitoFilm=$connessione->eseguiQuery($queryFilm);
        if(!$esitoFilm){
            $ok=false;
            echo 'primo';
        }
            
        if($esitoFilm){
            //recupero l'id del film
            $queryIDFilm="SELECT id FROM film ORDER BY id DESC LIMIT 1";
            $this->id=$connessione->interrogaDB($queryIDFilm);
            
            foreach($this->genere as $key=>$valore){
                echo 'aaaaaaaaaaaaaaaaaaaaaaaaa' ;
                print_r($this->genere);
                print_r($key);
                print_r($valore);
                $queryAppartenenza="INSERT INTO appartenenza (ID_film,ID_genere) VALUES 
                ($this->id,$valore)";
                $esitoAppartenenza=$connessione->eseguiQuery($queryAppartenenza);
                if(!$esitoAppartenenza)
                    $ok=false;
            }
        }
        $connessione->fineTransazione($ok);      
        $connessione->chiudiConnessione();
        if(!$ok){
            $this->id=null;
            echo 'inserimento fallito';
        }
        if($ok)
            echo 'inserito con successo';
            
        return $ok;
    }
//fine classe GestoreFilm
}



?>