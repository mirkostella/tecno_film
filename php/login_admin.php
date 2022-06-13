<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');

    
    //se la sessione è già aperta come admin mi porta direttamente alla home dell'admin
    if($_SESSION['loggato'] == true && $_SESSION['admin']==true){
        header('location: amministratore_loggato.php');
        exit();
    }

    $pagina = file_get_contents('../html/login_admin.html');
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
                $pagina = str_replace('%errore_credenziali%', "<div class=\"msg_box error_box\">Le credenziali non sono corrette</div>", $pagina);
            }
            else{
                $_SESSION['loggato']=true;
                $_SESSION['id']= $login_array[0]['ID'];
                $_SESSION['admin']=true;

                $connessione->chiudiConnessione();
                header('location: amministratore_loggato.php');
                exit();
            }
            $connessione->chiudiConnessione();

        }
        else{
            $pagina = str_replace('%errore_conn%', "<div class=\"msg_box error_box\">Errore di connessione al database</div>", $pagina);
        }
    }

    $pagina=str_replace('%errore_conn%', "", $pagina);
    $pagina = str_replace('%errore_credenziali%', "", $pagina);

    echo $pagina;
?>