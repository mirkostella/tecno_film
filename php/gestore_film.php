<?php

require_once("reg_check.php");

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
    public $erroriFilm;
    
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
        $this->erroriFilm=array(
            'errTitolo'=>'',
            'errTrama'=>'',
            'errDurata'=>'',
            'errDataUscita'=>'',
            'errPrezzoA'=>'',
            'errPrezzoN'=>'',
            'errDimensioneImmagine'=>'',
            'errFormatoImmagine'=>'',
            'errCaricamentoImmagine'=>'',
            'errDescrizione'=>'',
            'errGeneri'=>'',
            'errMancanzaImmagine'=>''
        );    
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

    public function presenzaTitolo(){
        $queryPresenza="SELECT titolo FROM film WHERE film.titolo='".$this->titolo."'";
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $presenza=$connessione->interrogaDB($queryPresenza);
        $connessione->chiudiConnessione();
        if($presenza)
            return true;

        return false;

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

//sostituisce nella pagina i messaggi di errore
    public function stampaErrori(&$pagina){
            $pagina=str_replace('%errTitolo%',$this->erroriFilm['errTitolo'],$pagina);
            $pagina=str_replace('%errAlt%',$this->erroriFilm['errDescrizione'],$pagina);
            $pagina=str_replace('%errTrama%',$this->erroriFilm['errTrama'],$pagina);
            $pagina=str_replace('%errDurata%',$this->erroriFilm['errDurata'],$pagina);
            $pagina=str_replace('%errData%',$this->erroriFilm['errDataUscita'],$pagina);
            $pagina=str_replace('%errPrezzoA%',$this->erroriFilm['errPrezzoA'],$pagina);
            $pagina=str_replace('%errPrezzoN%',$this->erroriFilm['errPrezzoN'],$pagina);
            $pagina=str_replace('%errCopertina%',$this->erroriFilm['errDimensioneImmagine'].$this->erroriFilm['errFormatoImmagine'].$this->erroriFilm['errCaricamentoImmagine'].$this->erroriFilm['errMancanzaImmagine'],$pagina);
            $pagina=str_replace('%errGeneri%',$this->erroriFilm['errGeneri'], $pagina);
        }

    public function controlloErroriForm(){
        $no_error=true;
        if(!check_titolo($this->titolo)){
            $this->erroriFilm['errTitolo']=$this->erroriFilm['errTitolo'].'<div class="error_box">Il titolo non puó superare i 50 caratteri (spazi inclusi).</div>';
            $no_error=false;
        }
        if($this->presenzaTitolo()){     
           $this->erroriFilm['errTitolo']=$this->erroriFilm['errTitolo'].'<div class="error_box">Il film è giá presente nel database.</div>';
           $no_error=false;
        }
        if(!check_dataUscita($this->dataUscita)){
            $this->erroriFilm['errDataUscita']=$this->erroriFilm['errDataUscita'].'<div class="error_box">La data di uscita non può essere superiore a quella attuale.</div>';
            $no_error=false;
        }
        if(!check_durata($this->durata)){
            $this->erroriFilm['errDurata']=$this->erroriFilm['errDurata'].'<div class="error_box">La durata deve essere maggiore di 0.</div>';
            $no_error=false;
        }
        if(!check_prezzo($this->prezzoA)){
            $this->erroriFilm['errPrezzoA']=$this->erroriFilm['errPrezzoA'].'<div class="error_box">Il prezzo di acquisto deve essere maggiore di 0.</div>';
            $no_error=false;
        }
        if(!check_prezzo($this->prezzoN)){
            $this->erroriFilm['errPrezzoN']=$this->erroriFilm['errPrezzoN'].'<div class="error_box">Il prezzo del noleggio deve essere maggiore di 0.</div>';
            $no_error=false;
        }
        if(!check_trama($this->trama)){
            $this->erroriFilm['errTrama']=$this->erroriFilm['errTrama'].'<div class="error_box">Il film deve avere una trama lunga almeno 20 caratteri.</div>';
            $no_error=false;
        }
        if(!check_descrizione($this->descrizione)){
            $this->erroriFilm['errDescrizione']=$this->erroriFilm['errDescrizione'].'<div class="error_box">La copertina deve avere una descrizione di almeno 15 caratteri.</div>';
            $no_error=false;
        }
        if(count($this->genere)==0){
            $this->erroriFilm['errGeneri']=$this->erroriFilm['errGeneri'].'<div class="error_box">Seleziona almeno un genere.</div>';
            $no_error=false;
        }
        return $no_error;
    }
    
    public function inserisciFilm(&$pagina){
        
        $gestisci_img = new gestione_img();    
        $queryFotoCopertina='';
        $path='';
        if(isset($_FILES['copertinaFilm']) && is_uploaded_file($_FILES['copertinaFilm']['tmp_name']))
            $path=$gestisci_img->caricaImmagine("img_film/", "copertinaFilm");
        else
            $this->erroriFilm['errMancanzaImmagine']='<div class="error_box">Immagine mancante</div>';
        if($path)
            $queryFotoCopertina="INSERT INTO foto_film (path, descrizione) VALUES ('".$path."', '".$_POST['descrizione']."')";
        else{
            $this->erroriFilm['errDimensioneImmagine']=$gestisci_img->getErroreDimensione();
            $this->erroriFilm['errFormatoImmagine']=$gestisci_img->getErroreFormato();
            $this->erroriFilm['errCaricamentoImmagine']=$gestisci_img->getErroreCaricamento();
        }

        //non ci sono errori dovuti alla form e posso iniziare la transazione
        if($this->controlloErroriForm() && $path){
            //imposto i messaggi di errore relativi alla form a vuoti
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $connessione->inizioTransazione();
    
            $esitoFoto=$connessione->eseguiQuery($queryFotoCopertina);
            $ok=true;
            if(!$esitoFoto)
                $ok=false;
            
            $IDFoto=null;
            if($ok){
                $queryIDFoto="SELECT id FROM foto_film ORDER BY id DESC LIMIT 1";
                $IDFoto=$connessione->interrogaDB($queryIDFoto);
                if(count($IDFoto)==0)
                    $ok=false;
            }
            $newformat=null;
            if($ok){    
                $queryFilm="INSERT INTO film (titolo,copertina,trama,durata,data_uscita,prezzo_acquisto,prezzo_noleggio) VALUES ('".$this->titolo."',".$IDFoto[0]['id'].",'".$this->trama."','".$this->durata."','".$this->dataUscita."',".$this->prezzoA.",".$this->prezzoN.")";
                $esitoFilm=$connessione->eseguiQuery($queryFilm);
                if(!$esitoFilm)
                    $ok=false;          
            }
    
            if($ok){
                //recupero l'id del film
                $queryIDFilm="SELECT ID FROM film ORDER BY id DESC LIMIT 1";
                $this->id=$connessione->interrogaDB($queryIDFilm)[0]['ID'];
                $x=$this->recuperaIDGeneri();
                foreach($x as $valore){
                    $queryAppartenenza="INSERT INTO appartenenza (ID_film,ID_genere) VALUES 
                    ($this->id,".$valore.")";
                    $esitoAppartenenza=$connessione->eseguiQuery($queryAppartenenza);
                    if(!$esitoAppartenenza)
                        $ok=false;
                }
            }
            $connessione->fineTransazione($ok);      
            $connessione->chiudiConnessione(); 
        }
        else
            $ok=false;
        $this->stampaErrori($pagina);
            if(!$ok){
                $this->id=null;
                //segnaposto esito transazione (fallita)
                $pagina=str_replace('%esitoTransazione%','<div class="error_box">Inserimento fallito</div>',$pagina);
                return false;
            }
            else{
            //segnaposto esito transazione (successo)
            $pagina=str_replace('%esitoTransazione%','<div class="success_box">Film inserito correttamente</div>',$pagina);
            return true;
            }
    }
//fine classe GestoreFilm
}



?>