<?php

    function trovaSegnalazioni($id, $connessione){
        $querySegnalazioniRecensioni="SELECT count(*) as segnalazioni_recensioni FROM recensione JOIN segnalazione ON (recensione.ID=segnalazione.ID_recensione) WHERE recensione.ID_utente=$id";
        $risSegnalazioniR=$connessione->interrogaDB($querySegnalazioniRecensioni);
        if($risSegnalazioniR)
                $segnalazioniRecensioni=$risSegnalazioniR[0]['segnalazioni_recensioni'];
        return $segnalazioniRecensioni;
    }

    function controlloStatoBloccato($connessione, $idUtenteLoggato){
        $queryStatoUtente = "SELECT * FROM utente WHERE utente.stato = 'Bloccato' AND utente.ID='".$idUtenteLoggato."'";
        $statoBloccato = $connessione->interrogaDB($queryStatoUtente);
        if($statoBloccato)
            return true;
        else
            return false;
    }


?>