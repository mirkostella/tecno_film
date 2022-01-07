<?php
    require_once('stelle.php');
    class Card{
        public $id;
        public $copertina;
        public $descrizione;
        public $titolo;
        public $genere;
        public $valutazione;
        public $prezzoN;
        public $prezzoA;
        public $annoUscita;
        public $trama;
        public $durata;

        public function __construct(&$array){
            $this->id=$array['id'];
            $this->copertina=$array['copertina'];
            $this->descrizione=$array['descrizione'];
            $this->titolo=$array['titolo'];
            $this->genere=$array['genere'];
            $this->valutazione=round($array['valutazione']);
            $this->prezzoN=$array['prezzoN'];
            $this->prezzoA=$array['prezzoA'];
            $this->annoUscita=$array['annoUscita'];
            $this->trama=$array['trama'];
            $this->durata=$array['durata']/60;
        }
        public function aggiungiBase(){
        $cardB=file_get_contents("../componenti/card.html");
        $cardB=str_replace('%id%',$this->id,$cardB);
        $cardB=str_replace('%path%',$this->copertina,$cardB);
        $cardB=str_replace('%desc%',$this->descrizione,$cardB);
        $cardB=str_replace('%gen%',$this->genere,$cardB);
        $cardB=str_replace('%prezzo%',$this->prezzoN,$cardB);
        $cardB=str_replace("%titolo%",$this->titolo,$cardB);
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
    function creaListaCard($queryResult){
        $listaCards="";
        foreach($queryResult as $valore){
            $cardAttuale=new Card($valore);
            $listaCards=$listaCards.$cardAttuale->aggiungiBase();
        }
        return $listaCards;
    }
    function creaListaCardClassificata($queryResult){
        $posizione=1;
        $listaCards="";
        foreach($queryResult as $valore){
            $cardAttuale=new CardClassificata($valore,$posizione);
            $listaCards=$listaCards.$cardAttuale->aggiungiBaseClassificata();
            $posizione++;
        }
        return $listaCards;
    }


?>