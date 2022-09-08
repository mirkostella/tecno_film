<?php

//racchiude le parti della stringa delimitate da ^^ in <span xml:lang="en"></span>
function aggiungiSpanLang($stringa,$delimitatore='^',$l='en'){
    $aperturaSpan='<span xml:lang="'.$l.'" lang="'.$l.'">';
    $chiusuraSpan='</span>';
    $risultato="";
    $lung=strlen($stringa);
    //dall'inizio fino alla fine della stringa
    for($i=0;$i<$lung;$i++){
        //finche non trovo il delimitatore tutto italiano 
        if($stringa[$i]!=$delimitatore)
        $risultato=$risultato.$stringa[$i];
        //ho trovato il delimitatore
            else{
                //apro il tag
                $risultato=$risultato.$aperturaSpan;
                //inserisco il testo fino a che non trovo il delimitatore o finisce la stringa
                $continua=true;
                while($i+1<$lung && $continua){
                    $i++;
                    if($stringa[$i]!=$delimitatore)
                    $risultato=$risultato.$stringa[$i];
                    else
                    $continua=false;
                }
                //POST:l'indice $i punta all'ultimo carattere inserito o al delimitatore di chiusura
                //chiudo il tag
                $risultato=$risultato.$chiusuraSpan;
            }
    }    
    return $risultato;          
}

//elimina i simboli di delimitazione che hanno la forma ^^ all'interno della stringa
function eliminaDelimitatoriLingua($stringa,$delimitatore='^'){
    $risultato="";
    $lung=strlen($stringa);
    for($i=0;$i<$lung;$i++){
        if($stringa[$i]!=$delimitatore)
            $risultato=$risultato.$stringa[$i];
    }
    return $risultato;
}




?>