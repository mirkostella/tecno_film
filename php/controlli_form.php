<?php

    //controlli registrazione
    function test_input($input_string) {
        $input_string = trim(htmlentities($input_string));
        return $input_string;
    }

    function check_email($email){
        if(preg_match('/^(([^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+(\.[^{}<>()\[\]\.,;:\s@\"\'\+#&\*$]+)*))@(([^{}<>()[\]\.,;:\s@\"\'\+#&\*$]+\.)+(it|com|net|org))$/', $email))
            return true;
        else
            return false;
    }

    function check_nome($nome){
        if(preg_match('/^[a-zA-Z]/', $nome) && strlen($nome)>=2){
            return true;
        }
        return false;
    }

    function check_password($password)
    {
        if(preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/', $password))
            return true;
        else
            return false;
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
        if(strlen($titolo) < 2)
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