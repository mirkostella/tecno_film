<?php
    require_once('stelle.php');
    require_once('info_film.php');

    class CardBase{
        public $id;
        public $copertina;
        public $descrizione;
        public $titolo;
        public $genere;
        
        public function __construct(&$array,&$generi){
            
            $this->id=$array['id'];
            $this->copertina=$array['copertina'];
            $this->descrizione=$array['descrizione'];
            $this->titolo=$array['titolo'];
            $this->genere=$generi;
        }
        public function aggiungiBase(){
            $cardB=file_get_contents("../componenti/card.html");
            $cardB=str_replace('%id%',$this->id,$cardB);
            $cardB=str_replace('%path%',$this->copertina,$cardB);
            $cardB=str_replace('%desc%',$this->descrizione,$cardB);
            //preparo la lista dei generi
            $listaGeneri='';
            foreach($this->genere as $valore){
                $stringaGenere=$valore['generiFilm']." ";
                $listaGeneri=$listaGeneri.$stringaGenere;
            }
            $listaGeneri=$listaGeneri." ";    
            $cardB=str_replace('%gen%',$listaGeneri,$cardB);
            $cardB=str_replace("%titolo%",$this->titolo,$cardB);
            return $cardB;  
        }
        
    }
    class CardPersonale extends CardBase{

        public $dataScadenza=null;
        public function __construct(&$array,&$generi){
            CardBase::__construct($array,$generi);
            if(isset($array['scadenza_noleggio']))
                $this->dataScadenza=$array['scadenza_noleggio'];
            
        }
        public function aggiungiBase(){
            $cardB=CardBase::aggiungiBase();
            if($this->dataScadenza){
                $dataTemp=strtotime($this->dataScadenza);
                $data=date('d/m/Y',$dataTemp);
                $cardB=str_replace("%infoCard%","Scadenza:"."$data",$cardB);
            }
                
            else
                $cardB=str_replace("%infoCard%","",$cardB);
            return $cardB;  
        }


    }
    
    class Card extends CardBase{
        public $valutazione;
        public $prezzoN;
        public $prezzoA;
        public $annoUscita;
        public $trama;
        public $durata;
        
        public function __construct(&$array,&$generi){
            
            CardBase::__construct($array,$generi);
            $this->valutazione=round($array['valutazione']);
            $this->prezzoN=$array['prezzoN'];
            $this->prezzoA=$array['prezzoA'];
            $this->annoUscita=$array['annoUscita'];
            $this->trama=$array['trama'];
            $this->durata=$array['durata'];
        }
        public function aggiungiBase(){
            $cardB=CardBase::aggiungiBase();
            $cardB=str_replace('%infoCard%',$this->prezzoN." &euro;",$cardB);
            $stelle=creaStelle($this->valutazione);
            $cardB=str_replace('%valutazione%',$stelle,$cardB);
            return $cardB;  
        }
        
    }
    class CardClassificata extends Card{
        public $pos;
        public function __construct(&$array,$posizione,&$generi){
            Card::__construct($array,$generi);
            $this->pos=$posizione;
        }
        public function aggiungiBaseClassificata(){
            $cardB=Card::aggiungiBase();
            $cardB=str_replace('%classifica%',"<div class='posizione'>$this->pos</div>",$cardB);
            return $cardB;

        }
    }
    
    function creaListaCardPersonale($connessione, $risultatoQuery){
        $listaCards="";
        if($risultatoQuery){
            //ordino le card in base alla valutazione
            $i=1;
            foreach($risultatoQuery as $valore){
                $generi=recuperaGeneri($connessione, $valore['id']);
                $cardAttuale=new CardPersonale($valore,$generi);
                $stringaCard=$cardAttuale->aggiungiBase();
                if($i==6){
                    $stringaCard=str_replace('%nascosto%',"nascosto",$stringaCard);
                }
                if($i==7){
                    $stringaCard=str_replace('%nascosto%',"nascosto2",$stringaCard);
                }
                $i++;
                $listaCards=$listaCards.$stringaCard;
            }
        }
        return $listaCards;
    }

    function creaListaCard($connessione, $risultatoQuery){
        $listaCards="";   
        if($risultatoQuery){
            //ordino le card in base alla valutazione
            foreach($risultatoQuery as $valore){
                $generi=recuperaGeneri($connessione, $valore['id']);
                $cardAttuale=new Card($valore,$generi);
                $stringaCard=$cardAttuale->aggiungiBase();
                $listaCards=$listaCards.$stringaCard;
            }
        }
        return $listaCards;
    }
    
    function creaListaCardClassificata($connessione, $risultatoQuery){
        if(!$risultatoQuery)
            return "";
        $listaCards="";
        $posizione=1;
        //ordino le card in base alla valutazione
        foreach($risultatoQuery as $valore){
            $generi=recuperaGeneri($connessione, $valore['id']);
            $cardAttuale=new CardClassificata($valore,$posizione,$generi);
            $listaCards=$listaCards.$cardAttuale->aggiungiBaseClassificata();
            $posizione++;
        }
        return $listaCards;
    }

?>