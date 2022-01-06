<?php 
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
?>