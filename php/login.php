<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');

    //se la sessione è già aperta come utente normale, mi porta direttamente alla home
    if($_SESSION['loggato'] == true && $_SESSION['admin'] == false){
        header('location: ../php/home.php');
        exit();
    }

    $pagina = file_get_contents('../html/login.html');
    
    $connessione = new Connessione();
    
    if(!$connessione->apriConnessione()){
        $pagina = str_replace('%errore_conn%',"<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>", $pagina);
    }
    
    $struttura = new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace('<a href="../php/login.php" accesskey="l" xml:lang="en" lang="en">Login</a>', '', $pagina);
    $struttura->aggiungiMenu($pagina,"","");
    $pagina=str_replace("%descrizione%","Login del sito TecnoFilm. Loggati e potrai noleggiare e acquistare film", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Login", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Login", $pagina);
    $pagina=str_replace("%breadcrumb%","<span class=\"grassetto\">Login</span>", $pagina);
    
    $idFilm="";
    $valoreEmail="";
    $valorePassword="";
    if(isset($_REQUEST['idFilm']))
        $idFilm=$_REQUEST['idFilm'];
    if(isset($_REQUEST['email']))
        $valoreEmail=$_REQUEST['email'];
    if(isset($_REQUEST['password']))
        $valorePassword=$_REQUEST['password'];
    
    $pagina = str_replace('%idFilm%',$idFilm,$pagina);
    $pagina = str_replace('%email%',$valoreEmail,$pagina);
    $pagina = str_replace('%password%',$valorePassword,$pagina);

    $email = NULL;
    $psw = NULL;

    if(isset($_POST['invia'])){
        if(isset($_POST['email'])){
            $email = $connessione->pulisciStringaSQL(trim(htmlentities($_POST['email'])));
        }
        if(isset($_POST['password'])){
            $psw = $connessione->pulisciStringaSQL(trim(htmlentities($_POST['password'])));
        }

        if(!$query_email= $connessione->interrogaDB("SELECT * FROM utente WHERE email = \"$email\"")){
            $pagina = str_replace('%error_email%', "<div class=\"error_box\">L'email inserita non è corretta</div>", $pagina);
        }
        else{
            if(!$query_psw= $connessione->interrogaDB("SELECT * FROM utente WHERE email = \"$email\" AND password = \"$psw\"")){
                $pagina = str_replace('%error_psw%', "<div class=\"error_box\">La password inserita non è corretta</div>", $pagina);  
            }
            else{
                $pagina = str_replace('%error_psw%', "", $pagina);
                $pagina = str_replace('%error_email%', "", $pagina);
            }
        }

        if(!$login_array= $connessione->interrogaDB("SELECT * FROM utente WHERE email = \"$email\" AND password = \"$psw\"")){
            $pagina = str_replace('%errore_credenziali%', "<div class=\"error_box\">Le credenziali non sono corrette</div>", $pagina);
        }
        else{
            $_SESSION['loggato']=true;
            $_SESSION['id']= $login_array[0]['ID'];
            $_SESSION['admin']=false;
            $connessione->chiudiConnessione();
            if($idFilm){
                header('location: ../php/pagina_film.php?idFilm='.$idFilm);
                exit();
            }
            else{
                header('location: ../php/home.php');
                exit();
            }      
        }
        
        $connessione->chiudiConnessione();
    }

    $pagina=str_replace('%errore_conn%', '', $pagina);
    $pagina=str_replace('%error_email%', '', $pagina);
    $pagina=str_replace('%error_psw%', '', $pagina);
    $pagina=str_replace('%errore_credenziali%', '', $pagina);
    echo $pagina;
?>