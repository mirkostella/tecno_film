<?php

    //controlli registrazione
    function test_input($input_string) {
        $input_string = trim(htmlentities($input_string));
        return $input_string;
    }

    function check_email($email){
        if(preg_match('/^([\w\-\+\.]+)\@([\w\-\+\.]+)\.([\w\-\+\.]+)$/', $email))
            return true;
        else
            return false;
    }

    function check_nome($nome){
        if(preg_match('/^([\p{L}\s]*)$/u', $nome)){
            return true;
        }
        return false;
    }

    function check_password($password)
    {
        $number = preg_match('@[0-9]@', $password);
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        
        if(strlen($password) < 8 || !$number || !$uppercase || !$lowercase) {
            return false;
        } 
        else
            return true;
    }

    function check_dataNascita($dataNascita){
        $date = new DateTime($dataNascita);
		$now = new DateTime();
		$delta = $now->diff($date);
        if($delta->y<18)
            return false;
        else
            return true;
    }

    //controlli inserimento film
    function check_titolo($titolo){
        if(strlen($titolo)>50)
            return false;
        else
            return true;
    }

    function check_dataUscita($dataUscita){
        $dataAttuale=new DateTime();
        $data=new DateTime($dataUscita);
        if($data > $dataAttuale)
            return false;
        else
            return true;
    }

    function check_durata($durata){
        if($durata <= 0)
            return false;
        else
            return true;
    }

    function check_prezzo($prezzo){
        if($prezzo <= 0)
            return false;
        else
            return true;
    }

    function check_trama($trama){
        if(strlen($trama) < 20)
            return false;
        else
            return true;
    }

    function check_descrizione($descrizione){
        if(strlen($descrizione) <15)
            return false;
        else
            return true;
    }

?>