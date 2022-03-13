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
                $lista=$lista."<option value=10>ciao</option>";
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
                $menu=str_replace('<li><a href="../php/raccolta_personale.php">I miei film</a></li>', '', $menu);
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
        
        //PRE:sono loggato ed il film non é presente in acquisto/noleggio dell'utente loggato
        public function aggiungiConfermaAcquistoNoleggio(&$pagina){
            $pulsanti=file_get_contents('../componenti/pulsanti_acquisto_noleggio.html');
            $controlloPresenza="";
            $connessione=new Connessione();
            $connessione->apriConnessione();
            if(isset($_GET['noleggio'])){
                $pulsanti=str_replace('%pulsanteAcquisto%',"",$pulsanti);
                $queryNoleggio="SELECT ID_utente FROM noleggio WHERE ID_utente=".$_SESSION['id']." and ID_film=".$_GET['idFilm'];
                $controlloPresenza=$connessione->interrogaDB($queryNoleggio);
                
            }
            if(isset($_GET['acquisto'])){
                $pulsanti=str_replace('%pulsanteNoleggio%',"",$pulsanti); 
                $queryAcquisto="SELECT ID_utente FROM acquisto WHERE ID_utente=".$_SESSION['id']." and ID_film=".$_GET['idFilm'];
                $controlloPresenza=$connessione->interrogaDB($queryAcquisto);
            }
            $connessione->chiudiConnessione();
            //non presenza film
            if(!$controlloPresenza){
                $pulsanti=str_replace('%pulsanteAcquisto%',
                '<form action="ins_acquisto_noleggio.php" method="get">
                <input type="hidden" name="idFilm" value="%idFilm%">
                <input id="acquisto" type="submit" value="Conferma Acquisto a %prezzoA%&euro;" name="confermaAcquisto" class="btn">
                </form>'  
                ,$pulsanti);
                $pulsanti=str_replace('%pulsanteNoleggio%',
                '<form action="ins_acquisto_noleggio.php" method="get">
                <input type="hidden" name="idFilm" value="%idFilm%">
                <input id="noleggio" type="submit" value="Conferma Noleggio a %prezzoN%&euro;" name="confermaNoleggio" class="btn">
                </form>'  
                ,$pulsanti);
                $pagina=str_replace('%pulsantiAcquistoNoleggio%',$pulsanti,$pagina);
            }
            //presenza film
            else{
                $pagina=str_replace('%pulsantiAcquistoNoleggio%',"Questo film é giá presente nella tua raccolta personale",$pagina);
            }
        }

        public function aggiungiAcquistoNoleggio(&$pagina){ 
            $idFilm="";
            if(isset($_POST['inviaRecensione']))
                $idFilm=$_POST['idFilm'];
            else
                $idFilm=$_GET['idFilm'];
            $pulsanti=file_get_contents('../componenti/pulsanti_acquisto_noleggio.html');
            
            if($_SESSION['loggato']==true){  
            $queryAcquisto="SELECT ID_utente FROM acquisto WHERE ID_utente=".$_SESSION['id']." and ID_film=".$idFilm;
            $queryNoleggio="SELECT ID_utente FROM noleggio WHERE ID_utente=".$_SESSION['id']." and ID_film=".$idFilm;
            $connessione=new Connessione();
            $connessione->apriConnessione();
            $presenzaAcquisto=$connessione->interrogaDB($queryAcquisto);
            if(!$presenzaAcquisto){
                $pulsanti=str_replace('%pulsanteAcquisto%',
                '<form action="acquisto_noleggio.php" method="get">
                <input type="hidden" name="idFilm" value="%idFilm%">
                <input id="acquisto" type="submit" value="Acquista a %prezzoA%&euro;" name="acquisto" class="btn">
                </form>'  
                ,$pulsanti);
            }
            else
                $pulsanti=str_replace('%pulsanteAcquisto%',"",$pulsanti);
    
            $presenzaNoleggio=$connessione->interrogaDB($queryNoleggio);
            if(!$presenzaNoleggio){
                $pulsanti=str_replace('%pulsanteNoleggio%',
                '<form action="acquisto_noleggio.php" method="get">
                <input type="hidden" name="idFilm" value="%idFilm%">
                <input id="noleggio" type="submit" value="Noleggia a %prezzoN%&euro;" name="noleggio" class="btn">
                </form>'  
                ,$pulsanti);
            }
            else
                $pulsanti=str_replace('%pulsanteNoleggio%',"",$pulsanti);
    
            if(!$presenzaAcquisto && !$presenzaNoleggio)
                $pagina=str_replace('%pulsantiAcquistoNoleggio%',$pulsanti,$pagina);
            else
                $pagina=str_replace('%pulsantiAcquistoNoleggio%',"",$pagina);
        }
        else{
                $pulsanti=str_replace('%pulsanteAcquisto%',
                '<form action="login.php" method="get">
                <input id="acquisto" type="submit" value="Acquista a %prezzoA%&euro;" name="acquisto" class="btn">
                </form>'  
                ,$pulsanti);
                $pulsanti=str_replace('%pulsanteNoleggio%',
                '<form action="login.php" method="get">
                <input id="noleggio" type="submit" value="Noleggia a %prezzoN%&euro;" name="noleggio" class="btn">
                </form>'  
                ,$pulsanti);
                $pagina=str_replace('%pulsantiAcquistoNoleggio%',$pulsanti,$pagina);
        }
    } 
    
}

?>