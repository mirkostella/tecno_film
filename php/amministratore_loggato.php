<?php

    //inclusione dei file 
    require_once ('connessione.php');
    require_once ('info_utente.php');
    
    $pagina=file_get_contents("../html/amministratore_loggato.html");
    $connessione=new Connessione();

    if(!$connessione->apriConnessione()){
        echo "errore di connessione al db";
    }

    $query_utente="SELECT ID,username, email,stato From utente";
    $risultato=$connessione->interrogaDB($query_utente);
    
   
   $righe = ''; // creo righe della tabella utenti
   foreach($risultato as $value) { //per ogni riga della query 
        $rigaUtente = file_get_contents("../componenti/utente_tab.html"); //prendo la struttura della riga nel file 
        $segnalazioni=trovaSegnalazioni($value['ID'],$connessione);
        //mappo i dati della riga con i relativi campi
        $rigaUtente=str_replace("%idUtente%",$value['ID'],$rigaUtente);
        $rigaUtente=str_replace("%username%",$value['username'],$rigaUtente);
        $rigaUtente=str_replace("%stato%",$value['stato'],$rigaUtente);
        $rigaUtente=str_replace("%email%",$value['email'],$rigaUtente);
        $rigaUtente=str_replace("%nSegnalazioni%",$segnalazioni,$rigaUtente);

        //aggiungo la righa alle righe della tabella
        $righe .= $rigaUtente;
    }

    $tabellaUtenti = file_get_contents("../componenti/tabella_utenti.html"); //prendo la struttura della tabella degli utenti
    $tabellaUtenti=str_replace("%rUtenti%",$righe,$tabellaUtenti); //sostituisco la stringa %rUtenti% con le righe costruite sopra 



    function build_table($risultato){
        // start table
        $html = '<table>';
        // header row
        $html .= '<tr>';
        foreach($risultato[0] as $key=>$value){
                $html .= '<th>' . htmlspecialchars($key) . '</th>';
            }
        $html .= '</tr>';
    
        // data rows
        foreach( $risultato as $key=>$value){
            $html .= '<tr>';
            foreach($value as $key2=>$value2){
                $html .= '<td>' . htmlspecialchars($value2) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    

    } 
        
    /* ...................parte film................................*/

    $query_film="select titolo as 'Titolo',prezzo_acquisto as 'Prezzo acquisto',
        prezzo_noleggio as 'Prezzo noleggio',a.num_acquisto as 'Incasso acquisto',n.num_noleggio as 'Incasso neleggio'
        from film join (
        select count(ID_film) as num_acquisto ,ID_film
        from acquisto
        group by ID_film) as a
        join(
        select count(ID_film) as num_noleggio ,ID_film
        from noleggio
        group by ID_film) as n
        on ID= n.ID_film";

    $risultato_f=$connessione->interrogaDB($query_film);


    $header_admin=file_get_contents("../componenti/header_admin_log.html");
    $pagina=str_replace("%headerAdmin%",$header_admin,$pagina);

    $menu_admin=file_get_contents("../componenti/menu_admin_log.html");
    $pagina=str_replace("%menuAdmin%",$menu_admin,$pagina);
    
    
    $pagina=str_replace("%tabellaUtenti%",$tabellaUtenti,$pagina); //QUI metto la mia tabella degli utenti nella mia pagina
    $pagina=str_replace("%tabellaFilm%",build_table($risultato_f),$pagina);
    $connessione->chiudiConnessione();
    
    echo $pagina;
?>