<?php
    require_once ('connessione.php');
    function calcoloPercentuale($voti,$tot){
        if($tot==0)
            return false;
        $percentuale=round($voti/$tot*100,2);
        return $percentuale;
    }
    class ResocontoRecensioni{
        public $val5;
        public $val4;
        public $val3;
        public $val2;
        public $val1;
        public $totRecensioni;
        public function __construct($idFilm){
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $this->val5=$connessione->interrogaDB("SELECT count(valutazione) as voto FROM recensione WHERE ID_film=$idFilm AND valutazione=5")[0]['voto'];
            $this->val4=$connessione->interrogaDB("SELECT count(valutazione) as voto FROM recensione WHERE ID_film=$idFilm AND valutazione=4")[0]['voto'];
            $this->val3=$connessione->interrogaDB("SELECT count(valutazione) as voto FROM recensione WHERE ID_film=$idFilm AND valutazione=3")[0]['voto'];
            $this->val2=$connessione->interrogaDB("SELECT count(valutazione) as voto FROM recensione WHERE ID_film=$idFilm AND valutazione=2")[0]['voto'];
            $this->val1=$connessione->interrogaDB("SELECT count(valutazione) as voto FROM recensione WHERE ID_film=$idFilm AND valutazione=1")[0]['voto'];
            $this->totRecensioni=$connessione->interrogaDB("SELECT count(*) as tot FROM recensione WHERE ID_film=$idFilm")[0]['tot'];
            $connessione->chiudiConnessione();
        }
        //crea il grafico in corrispondenza del segnaposto all'interno della pagina
        public function creaGrafico(&$pagina){
            if($this->totRecensioni!=0){
                $pagina=str_replace('%perc5%',calcoloPercentuale($this->val5,$this->totRecensioni),$pagina);
                $pagina=str_replace('%perc4%',calcoloPercentuale($this->val4,$this->totRecensioni),$pagina);
                $pagina=str_replace('%perc3%',calcoloPercentuale($this->val3,$this->totRecensioni),$pagina);
                $pagina=str_replace('%perc2%',calcoloPercentuale($this->val2,$this->totRecensioni),$pagina);
                $pagina=str_replace('%perc1%',calcoloPercentuale($this->val1,$this->totRecensioni),$pagina);   
            }
            else{
                $pagina=str_replace('%perc5%',0,$pagina);
                $pagina=str_replace('%perc4%',0,$pagina);
                $pagina=str_replace('%perc3%',0,$pagina);
                $pagina=str_replace('%perc2%',0,$pagina);
                $pagina=str_replace('%perc1%',0,$pagina);
            }
    }
}

?>