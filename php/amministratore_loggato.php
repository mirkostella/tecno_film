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

    


   /* function build_table($risultato){
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
    

    } */
        
    /* ...................parte film................................*/

    $query_film="select film.ID,film.titolo ,film.prezzo_noleggio ,
    film.prezzo_acquisto ,
     t.numero_noleggio , t.numero_acquisti 
    from film 
    left join
    (select film.ID,film.prezzo_acquisto,film.prezzo_noleggio , n.numero_noleggio, a.numero_acquisti
    from film join 
    (select n1.ID_film, count(n1.ID_film) as numero_noleggio
     from noleggio as n1
     group by n1.ID_film) as n
     join
     (select a1.ID_film, count(a1.ID_film) as numero_acquisti
     from acquisto as a1
     group by a1.ID_film) as a
     on film.ID = n.ID_film and film.ID = a.ID_film) as t
     on t.ID = film.ID
    
    ";
    $tabellaFilm = file_get_contents("../componenti/tabella_film.html");
    $risultato_f=$connessione->interrogaDB($query_film);
    
    $totNoleggi=0;
    $totAcquisti=0;
    $totINoleggi=0;
    $totIAcquisti=0;
    $totIncassi=0;

 $righe_f = ''; // creo righe della tabella utenti
   foreach($risultato_f as $value_f) { //per ogni riga della query 
        $rigaFilm = file_get_contents("../componenti/film_tab.html"); //prendo la struttura della riga nel file 
        //mappo i dati della riga con i relativi campi

        $pNoleggi=$value_f['prezzo_noleggio']*$value_f['numero_noleggio'];
        $pAcquisti=$value_f['prezzo_acquisto']*$value_f['numero_acquisti'];

        $rigaFilm=str_replace("%idFilm%",$value_f['ID'],$rigaFilm);
        $rigaFilm=str_replace("%titolo%",$value_f['titolo'],$rigaFilm);
        $rigaFilm=str_replace("%prezzoN%",$value_f['prezzo_noleggio'],$rigaFilm);
        $rigaFilm=str_replace("%prezzoA%",$value_f['prezzo_acquisto'],$rigaFilm);
        $rigaFilm=str_replace("%noleggi%",$value_f['numero_noleggio'] == null ? 0 : $value_f['numero_noleggio'] ,$rigaFilm);
        $rigaFilm=str_replace("%acquisti%",$value_f['numero_acquisti'] == null ? 0: $value_f['numero_acquisti'],$rigaFilm);
        $rigaFilm=str_replace("%pNoleggi%",$pNoleggi,$rigaFilm);
        $rigaFilm=str_replace("%pAcquisti%",$pAcquisti,$rigaFilm);
        $rigaFilm=str_replace("%pNoleggiAcquisti%",$pNoleggi + $pAcquisti ,$rigaFilm);

        $totNoleggi+=$value_f['numero_noleggio'];
        $totAcquisti+=$value_f['numero_acquisti'];
        $totINoleggi+=$pNoleggi;
        $totIAcquisti+=$pAcquisti;
        $totIncassi+=($pAcquisti+$pNoleggi);

        //aggiungo la righa alle righe della tabella
        $righe_f .= $rigaFilm;
    }
    $tabellaFilm=str_replace("%rFilm%",$righe_f,$tabellaFilm);

    $tabellaFilm=str_replace("%totNoleggi%",$totNoleggi,$tabellaFilm);
    $tabellaFilm=str_replace("%totAcquisti%",$totAcquisti,$tabellaFilm);
    $tabellaFilm=str_replace("%totIAcquisti%",$totIAcquisti,$tabellaFilm);
    $tabellaFilm=str_replace("%totINoleggi%",$totINoleggi,$tabellaFilm);
    $tabellaFilm=str_replace("%totIncassi%",$totIncassi,$tabellaFilm);

    $header_admin=file_get_contents("../componenti/header_admin_log.html");
    $pagina=str_replace("%headerAdmin%",$header_admin,$pagina);

    $menu_admin=file_get_contents("../componenti/menu_admin_log.html");
    $pagina=str_replace("%menuAdmin%",$menu_admin,$pagina);
    
    
    $pagina=str_replace("%tabellaUtenti%",$tabellaUtenti,$pagina); //QUI metto la mia tabella degli utenti nella mia pagina
   // $pagina=str_replace("%tabellaFilm%",build_table($risultato_f),$pagina);
    $pagina=str_replace("%tabellaFilm%",$tabellaFilm,$pagina);
    $connessione->chiudiConnessione();
    
    echo $pagina;
?>