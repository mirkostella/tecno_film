<?php
    require_once('sessione.php');
    require_once('struttura.php');
    require_once('connessione.php');

    //se la sessione è già aperta come admin, effettua il logout e mi porta alla home
    if($_SESSION['loggato'] == true && $_SESSION['admin'] == true)
    {
        header('location: logout.php');
        exit();
    }

    //se la sessione è già aperta come utente normale, mi porta direttamente alla home
  //  if($_SESSION['loggato'] == true && $_SESSION['admin'] == false){
  //      header('location: index.php');
  //      exit();
   // }
   $idFilm="";
    if(isset($_REQUEST['idFilm']))
        $idFilm=$_REQUEST['idFilm'];

    $pagina = file_get_contents('../html/login.html');
    $pagina = str_replace('%idFilm%',$idFilm,$pagina);
    $struttura = new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $inAttivo=file_get_contents("../componenti/menu.html");
    $attivo=file_get_contents("../componenti/menu.html");
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);
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
                $pagina = str_replace('%errore_credenziali%', "<div class=\"msg_box error_box\">Le credenziali non sono corrette</div>", $pagina);
            }
            else{
                $_SESSION['loggato']=true;
                $_SESSION['id']= $login_array[0]['ID'];
                $_SESSION['admin']=false;
                $connessione->chiudiConnessione();
                if($idFilm){
                    header('location: pagina_film.php?idFilm='.$idFilm);
                    exit();
                }
                else{
                    header('location: index.php');
                    exit();
                }      
            }
            $connessione->chiudiConnessione();

        }
        else{
            $pagina = str_replace('%errore_conn%', "<div class=\"msg_box error_box\">Errore di connessione al database</div>", $pagina);
        }
    }
    $pagina=str_replace('%errore_conn%', '', $pagina);
    $pagina=str_replace('%errore_credenziali%', '', $pagina);
    echo $pagina;
?>