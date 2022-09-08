<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');

    
    //se la sessione è già aperta come admin mi porta direttamente alla home dell'admin
    if($_SESSION['loggato'] == true && $_SESSION['admin']==true){
        header('location: ../php/amministratore_loggato.php');
        exit();
    }

    $valoreEmail="";
    $valorePassword="";
    if(isset($_REQUEST['email']))
        $valoreEmail=$_REQUEST['email'];
    if(isset($_REQUEST['password']))
        $valorePassword=$_REQUEST['password'];

    $pagina = file_get_contents('../html/login_admin.html');
    $pagina = str_replace('%email%',$valoreEmail,$pagina);
    $pagina = str_replace('%password%',$valorePassword,$pagina);

    
    $connessione = new Connessione();
    $connessioneAperta=$connessione->apriConnessione();
    if(isset($_POST['invia'])){
        if(isset($_POST['email'])){
            $email = $connessione->pulisciStringaSQL(trim(htmlentities($_POST['email'])));
        }
        if(isset($_POST['password'])){
            $psw = $connessione->pulisciStringaSQL(trim(htmlentities($_POST['password'])));
        }

        if($connessioneAperta){

            if(!$query_email= $connessione->interrogaDB("SELECT * FROM admin WHERE email = \"$email\"")){
                $pagina = str_replace('%error_email%', "<div class=\"error_box\">L'email inserita non è corretta</div>", $pagina);
            }
            else{
                if(!$query_psw= $connessione->interrogaDB("SELECT * FROM admin WHERE email = \"$email\" AND password = \"$psw\"")){
                    $pagina = str_replace('%error_psw%', "<div class=\"error_box\">La password inserita non è corretta</div>", $pagina);  
                }
                else{
                    $pagina = str_replace('%error_psw%', "", $pagina);
                    $pagina = str_replace('%error_email%', "", $pagina);
                }
            }

            if(!$login_array= $connessione->interrogaDB("SELECT * FROM admin WHERE email = \"$email\" AND password = \"$psw\"")){
                $pagina = str_replace('%errore_credenziali%', "<div class=\"error_box\">Le credenziali non sono corrette</div>", $pagina);
            }
            else{
                $_SESSION['loggato']=true;
                $_SESSION['id']= $login_array[0]['ID'];
                $_SESSION['admin']=true;

                $connessione->chiudiConnessione();
                header('location: ../php/amministratore_loggato.php');
                exit();
            }
            $connessione->chiudiConnessione();

        }
        else{
            $pagina = str_replace('%errore_conn%', "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>", $pagina);
        }
    }

    $pagina=str_replace('%errore_conn%', "", $pagina);
    $pagina = str_replace('%error_psw%', "", $pagina);
    $pagina = str_replace('%error_email%', "", $pagina);
    $pagina = str_replace('%errore_credenziali%', "", $pagina);

    echo $pagina;
?>