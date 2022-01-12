<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');

    
    $pagina=file_get_contents("../html/index.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo="<li><a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a></li>";
    $attivo="<li xml:lang=\"en\" lang=\"en\" id=\"attivo\">Home</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
    $listaCards="";
    $risultatoCard=recuperaNuoveUscite(6);
    if($risultatoCard){
        foreach($risultatoCard as $valore){
            $cardAttuale=new Card($valore);
            $listaCards=$listaCards.$cardAttuale->aggiungiBase();
        }
    }
    $pagina=str_replace('%listaCardN%',$listaCards,$pagina);
    $listaCards="";
    $risultatoCardS=recuperaSceltiPerTe(6);
    if($risultatoCardS){
        //ordino le card in base alla valutazione
        $i=1;
        foreach($risultatoCardS as $valore){
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
    else 
        $listaCards="";
    $pagina=str_replace('%listaCardS%',$listaCards,$pagina);



    $pagina=str_replace('%classifica%',"",$pagina);
    echo $pagina;
?>