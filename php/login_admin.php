<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');

    //se la sessione è già aperta come admin mi porta direttamente alla home dell'admin
    if($_SESSION['loggato'] == true && $_SESSION['admin']==true){
        header('location: index_admin.php');
        exit();
    }

    $pagina = file_get_contents('../html/login_admin.html');

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
            if(!$login_array= $connessione->interrogaDB("SELECT * FROM admin WHERE email = \"$email\" AND password = \"$psw\"")){
                $error = "<div class=\"msg_error_box\">Le credenziali non sono corrette</div>";
            }
            else{
                $_SESSION['loggato']=true;
                $_SESSION['id']= $login_array[0]['ID'];
                $_SESSION['admin']=true;

                $connessione->chiudiConnessione();
                header('location: index_admin.php');
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