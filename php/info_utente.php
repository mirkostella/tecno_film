<?php
    require_once ('connessione.php');

    function trovaSegnalazioni($id,$connessione){
        
        // $connessione=new Connessione();
        // $connessione->apriConnessione();
        $querySegnalazioniRecensioni="SELECT count(*) as segnalazioni_recensioni FROM recensione JOIN segnalazione ON (recensione.ID=segnalazione.ID_recensione) WHERE recensione.ID_utente=$id";
        $querySegnalazioniFoto="SELECT count(*) as segnalazioni_foto FROM segnalazione_foto_utente WHERE ID_utente=$id";
        $risSegnalazioniR=$connessione->interrogaDB($querySegnalazioniRecensioni);
        $risSegnalazioniF=$connessione->interrogaDB($querySegnalazioniFoto);
        $segnalazioniRecensioni=0;
        $segnalazioniFoto=0;
        if($risSegnalazioniR)
                $segnalazioniRecensioni=$risSegnalazioniR[0]['segnalazioni_recensioni'];
            if($risSegnalazioniF)
                $segnalazioniFoto=$risSegnalazioniF[0]['segnalazioni_foto'];
        // $connessione->chiudiConnessione();
        return $segnalazioniRecensioni+$segnalazioniFoto;
    }


?>