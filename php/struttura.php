<?php

    require_once ('connessione.php');
    require_once ('sessione.php');
    
    class Struttura{

        public function aggiungiHeader(&$pagina){
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $querySuggerimenti="SELECT titolo FROM film";
            $suggerimenti=$connessione->interrogaDB($querySuggerimenti);
            $connessione->chiudiConnessione();
            $componente=file_get_contents("../componenti/header.html");
            $lista="";
            foreach($suggerimenti as $i=>$valore){
                $titolo=$suggerimenti[$i]["titolo"];
                $lista=$lista."<option value=\"$titolo\" />";
            }
            $componente=str_replace("%suggerimenti%",$lista,$componente);
            $pagina=str_replace("%header%",$componente,$pagina);
        }

        public function aggiungiAccount(&$pagina){
            if($_SESSION['loggato']==true && $_SESSION['admin']==false){
                $acc = file_get_contents("../componenti/account.html");
                $acc=str_replace('<a href="../php/registrazione.php">Registrati</a>', '', $acc);
                $acc=str_replace('<a href="../php/login.php">Login</a>', '<a href="../php/logout.php">Logout</a>', $acc);
                $pagina=str_replace('%account%', $acc, $pagina);
            }
            
            else{
                $acc = file_get_contents("../componenti/account.html");
                $pagina=str_replace("%account%",$acc, $pagina);
            }
        }

        public function aggiungiMenu(&$pagina,$InAttivo,$attivo){
            $menu=file_get_contents("../componenti/menu.html");

            if($_SESSION['loggato']==false || $_SESSION['admin']==true){
                $menu=str_replace('<li><a href="../php/raccolta.php">I miei film</a></li>', '', $menu);
            }
            
            $menu=str_replace($InAttivo,$attivo,$menu); 
            $pagina=str_replace('%menu%',$menu,$pagina);
        }

        public function aggiungMenu_admin(&$pagina)
        {
            $menu = file_get_contents("../componenti/menu_admin_log.html");
            $pagina=str_replace("%menuAdmin%", $menu, $pagina);
        }

        public function aggiungiHeader_admin(&$pagina){
            $header = file_get_contents("../componenti/header_admin_log.html");
            $pagina = str_replace("%headerAdmin%", $header, $pagina);
        }

        public function aggiungiFormRecensione(&$pagina){
        
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $queryPresenzaRecensione="SELECT * FROM recensione WHERE ID_utente='".$_SESSION['id']."'"; 
            $presenzaRecensione=$connessione->interrogaDB($queryPresenzaRecensione);
            if($_SESSION['loggato']==true && !$presenzaRecensione){
                $form=file_get_contents('../componenti/ins_recensione.html');
                $form=str_replace('%id%',$_POST['idFilm'],$form);
                $pagina=str_replace('%formRecensione%',$form,$pagina);
                
            }
            if($_SESSION['loggato']==true && $presenzaRecensione){
                $pagina=str_replace('%formRecensione%',"Hai giá inserito una recensione per questo film",$pagina);
            }
            if($_SESSION['loggato']==false){
                $pagina=str_replace('%formRecensione%',"Accedi per inserire una recensione",$pagina);
            }
            $connessione->chiudiConnessione();
        }
    }
?>