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
        $this->genere=$array['generi'];
    
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

    public function recuperaFilePath(){
        $file_path='';
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $gestisci_img = new gestione_img();
        if(isset($_FILES['copertinaFilm']) && is_uploaded_file($_FILES['copertinaFilm']['tmp_name'])){
            $upload_result=$gestisci_img->uploadImg("img_film/", "copertinaFilm");
            print_r($upload_result['path']);
            if($upload_result['error']==''){
                $file_path=$upload_result['path'];
                return $file_path;
            }
            else
                return false;
        }
    }

    public function recuperaIDGeneri(){
        $ris=array();
        $connessione=new Connessione();
        $connessione->apriConnessione();
        foreach($this->genere as $valore){
            $queryIDGenere="SELECT ID FROM genere WHERE nome_genere='".$valore."'";
            $IDGenere=$connessione->interrogaDB($queryIDGenere);
            array_push($ris,$IDGenere[0]['ID']);
        }
        $connessione->chiudiConnessione();
        return $ris;
    }

    public function inserisciFilm(){

        $path=$this->recuperaFilePath();
        $queryFotoCopertina="INSERT INTO foto_film (path, descrizione) VALUES 
        (\"$path\", '".$_POST['descrizione']."')";

        $connessione=new Connessione();
        $connessione->apriConnessione();
        $connessione->inizioTransazione();

        $esitoFoto=$connessione->eseguiQuery($queryFotoCopertina);
        if(!$esitoFoto)
            echo 'esito foto fallito';

        $queryIDFoto="SELECT id FROM foto_film ORDER BY id DESC LIMIT 1";
        $IDFoto=$connessione->interrogaDB($queryIDFoto);
        
        print_r($this->titolo);
        print_r($IDFoto[0]['id']);
        print_r($this->trama);
        print_r($this->durata);
        print_r($this->dataUscita);
        print_r($this->prezzoA);
        print_r($this->prezzoN);
        $time = strtotime($this->dataUscita);
        $newformat = date('d-m-Y',$time); 
        echo $newformat;
        $queryFilm="INSERT INTO film (titolo,copertina,trama,durata,data_uscita,prezzo_acquisto,prezzo_noleggio) VALUES ('".$this->titolo."',".$IDFoto[0]['id'].",'".$this->trama."',".$this->durata.",".$newformat.",".$this->prezzoA.",".$this->prezzoN.")";
        
        $ok=true;
        $esitoFilm=$connessione->eseguiQuery($queryFilm);
        if(!$esitoFilm){
            $ok=false;
            echo 'primo';
        }
             
        if($esitoFilm){
            //recupero l'id del film
            $queryIDFilm="SELECT ID FROM film ORDER BY id DESC LIMIT 1";
            $queryIDGenere="SELECT ID FROM genere WHERE nome_genere='azione'";
            $genere=$connessione->interrogaDB($queryIDGenere)[0]['ID'];
            $this->id=$connessione->interrogaDB($queryIDFilm)[0]['ID'];
            $x=$this->recuperaIDGeneri();
            foreach($x as $valore){
                $queryAppartenenza="INSERT INTO appartenenza (ID_film,ID_genere) VALUES 
                ($this->id,".$valore.")";
                $esitoAppartenenza=$connessione->eseguiQuery($queryAppartenenza);
                if(!$esitoAppartenenza)
                    $ok=false;
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
    }
//fine classe GestoreFilm
}



?>