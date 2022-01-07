<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');


    if($_SESSION['loggato'] == true){
        header('location: index.php');
        exit();
    }

    $pagina = file_get_contents('../html/login.html');
    $struttura = new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,"","");

    $error = NULL;
    $email = NULL;
    $psw = NULL;

    if(isset($_POST['invia'])){
        if(isset($_POST['email'])){
            $email = trim(htmlentities($_POST['email']));
        }
        if(isset($_POST['password'])){
            $psw = trim(htmlentities($_POST['password']));
        }

        $connessione = new Connessione();
        if($connessione->apriConnessione()){
            if(!$login_array= $connessione->interrogaDB("SELECT * FROM utente WHERE email = \"$email\" AND password = \"$psw\"")){
                $error = "<div class=\"msg_error_box\">La query non è andata a buon fine o le credenziali non sono corrette</div>";
            }
            else{
                $_SESSION['loggato']=true;
                $_SESSION['id']= $login_array[0]['ID'];

                $connessione->chiudiConnessione();
                header('location: index.php');
                exit();
            }
            $connessione->chiudiConnessione();

        }
        else{
            $error = "<div class=\"msg_error_box\">Errore di connessione al database</div>";
        }
    }

    $pagina=str_replace('%error%', $error, $pagina);
    echo $pagina;
?>