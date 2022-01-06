<?php
    require_once ('connessione.php');
    echo "struttura";
    echo "</br>";
    echo '$_SESSION:   ';
    // print_r($_SESSION);
    echo "</br>";
    echo '$_GET:   ';
    print_r($_GET);
    echo "</br>";
    echo '$_POST:   ';
    print_r($_POST);
    echo "</br>";
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
            if($_SESSION['loggato']==true){
                $pagina=str_replace("%account%",'<a href="../php/logout.php">Logout</a>', $pagina);
            }
            else{
                $acc = file_get_contents("../componenti/account.html");
                $pagina=str_replace("%account%",$acc, $pagina);
            }
        }
        public function aggiungiMenu(&$pagina,$InAttivo,$attivo){
            $menu=file_get_contents("../componenti/menu.html");
            $menu=str_replace($InAttivo,$attivo,$menu); 
	        $pagina=str_replace('%menu%',$menu,$pagina);
        }
        public function aggiungiFormRecensione(&$pagina){
        
        $connessione=new Connessione();
        $connessione->apriConnessione();
        $queryPresenzaRecensione="SELECT * FROM recensione WHERE recensione.ID_utente=".$_SESSION['id'].""; 
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