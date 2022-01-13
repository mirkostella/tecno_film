<?php
    require_once('stelle.php');

    class CardBase{
        public $id;
        public $copertina;
        public $descrizione;
        public $titolo;
        public $genere;
        
        public function __construct(&$array){
            
            $this->id=$array['id'];
            $this->copertina=$array['copertina'];
            $this->descrizione=$array['descrizione'];
            $this->titolo=$array['titolo'];
            $this->genere=$array['genere'];
        }
        public function aggiungiBase(){
            $cardB=file_get_contents("../componenti/card.html");
            $cardB=str_replace('%id%',$this->id,$cardB);
            $cardB=str_replace('%path%',$this->copertina,$cardB);
            $cardB=str_replace('%desc%',$this->descrizione,$cardB);
            $cardB=str_replace('%gen%',$this->genere,$cardB);
            $cardB=str_replace("%titolo%",$this->titolo,$cardB);
            return $cardB;  
        }
        
    }
    class CardPersonale extends CardBase{

        public $dataScadenza=null;
        public function __construct(&$array){
            CardBase::__construct($array);
            if(isset($array['scadenza_noleggio']))
                $this->dataScadenza=$array['scadenza_noleggio'];
            
        }
        public function aggiungiBase(){
            $cardB=CardBase::aggiungiBase();
            if($this->dataScadenza)
                $cardB=str_replace("%infoCard%","Scadenza:".$this->dataScadenza,$cardB);
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
        
        public function __construct(&$array){
            
            CardBase::__construct($array);
            $this->valutazione=round($array['valutazione']);
            $this->prezzoN=$array['prezzoN'];
            $this->prezzoA=$array['prezzoA'];
            $this->annoUscita=$array['annoUscita'];
            $this->trama=$array['trama'];
            $this->durata=$array['durata']/60;
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
        public function __construct(&$array,$posizione){
            Card::__construct($array);
            $this->pos=$posizione;
        }
        public function aggiungiBaseClassificata(){
            $cardB=Card::aggiungiBase();
            $cardB=str_replace('%classifica%',"<div class=\"posizione\">$this->pos</div>",$cardB);
            return $cardB;

        }

    }
    function creaListaCardPersonale($risultatoQuery){
        $listaCards="";
        if($risultatoQuery){
            //ordino le card in base alla valutazione
            $i=1;
            foreach($risultatoQuery as $valore){
                $cardAttuale=new CardPersonale($valore);
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
    function creaListaCard($risultatoQuery){
        $listaCards="";
        if($risultatoQuery){
            //ordino le card in base alla valutazione
            $i=1;
            foreach($risultatoQuery as $valore){
                $cardAttuale=new Card($valore);
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
    
    function creaListaCardClassificata($risultatoQuery){
        $listaCards="";
        $posizione=1;

        if($risultatoQuery){
            //ordino le card in base alla valutazione
            $i=1;
                foreach($risultatoQuery as $valore){
                    $cardAttuale=new CardClassificata($valore,$posizione);
                    $listaCards=$listaCards.$cardAttuale->aggiungiBaseClassificata();
                    $posizione++;
                    
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

?>