<?php
    require_once ('sessione.php');

    class Struttura{

        public function aggiungiHeader($connessione, &$pagina){
            $componente=file_get_contents("../componenti/header.html");
            $querySuggerimenti="SELECT titolo FROM film";
            $suggerimenti=$connessione->interrogaDB($querySuggerimenti);
            $lista="";
            if($suggerimenti){
                foreach($suggerimenti as $i=>$valore){
                    $titolo=$suggerimenti[$i]["titolo"];
                    $lista=$lista."<option>$titolo</option>";
                }
            }
            $componente=str_replace("%suggerimenti%",$lista,$componente);
            $pagina=str_replace("%header%",$componente,$pagina);
        }
        
        public function aggiungiAccount(&$pagina){
            if($_SESSION['loggato']==true && $_SESSION['admin']==false){
                $acc = file_get_contents("../componenti/account.html");
                $acc=str_replace('<a href="../php/registrazione.php" accesskey="r">Registrati</a>', '', $acc);
                $acc=str_replace('<a href="../php/login.php" accesskey="l">Login</a>', '<a href="../php/logout.php" accesskey="l">Logout</a>', $acc);
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
      public function aggiungMenu_admin(&$pagina,$InAttivo,$attivo)
        {
            $menu = file_get_contents("../componenti/menu_admin_log.html");
            $menu=str_replace($InAttivo,$attivo,$menu); 
            $pagina=str_replace("%menuAdmin%", $menu, $pagina);
        }

        public function aggiungiHeader_admin(&$pagina){
            $header = file_get_contents("../componenti/header_admin_log.html");
            $pagina = str_replace("%headerAdmin%", $header, $pagina);
        }
        
        //PRE:sono loggato ed il film non é presente in acquisto/noleggio dell'utente loggato
        public function aggiungiConfermaAcquistoNoleggio($connessione, &$pagina){
            if(isset($_GET['noleggio'])){
                $queryNoleggio="SELECT ID_utente FROM noleggio WHERE scadenza_noleggio > CURRENT_TIMESTAMP AND noleggio.ID_utente=".$_SESSION['id']." and noleggio.ID_film=".$_GET['idFilm'];
                $controlloPresenzaNoleggio=$connessione->interrogaDB($queryNoleggio);
                if(!$controlloPresenzaNoleggio){
                    $pagina=str_replace('%pulsanteNoleggio%',
                    '<form action="pagina_film.php" method="get">
                    <input type="hidden" name="idFilm" value="%idFilm%">
                    <input id="noleggio" type="submit" value="Conferma Noleggio a %prezzoN%&euro;" name="confermaNoleggio" class="btn">
                    </form>'
                    ,$pagina);
                    $pagina=str_replace('%pulsanteAcquisto%',"",$pagina);
                }
            }

            if(isset($_GET['acquisto'])){
                $queryAcquisto="SELECT ID_utente FROM acquisto WHERE ID_utente=".$_SESSION['id']." and ID_film=".$_GET['idFilm'];
                $controlloPresenzaAcquisto=$connessione->interrogaDB($queryAcquisto);

                if(!$controlloPresenzaAcquisto){
                    $pagina=str_replace('%pulsanteAcquisto%',
                    '<form action="pagina_film.php" method="get">
                    <input type="hidden" name="idFilm" value="%idFilm%">
                    <input id="acquisto" type="submit" value="Conferma Acquisto a %prezzoA%&euro;" name="confermaAcquisto" class="btn">
                    </form>'  
                    ,$pagina);
                    $pagina=str_replace("%pulsanteNoleggio%", "", $pagina);
                }
            }
        }

        public function aggiungiAcquistoNoleggio($connessione, &$pagina){ 
            $idFilm="";
            if(isset($_POST['inviaRecensione']))
                $idFilm=$_POST['idFilm'];
            else
                $idFilm=$_GET['idFilm'];
            
            if($_SESSION['loggato']==true && $_SESSION['admin']==false){  
                $queryAcquisto="SELECT ID_utente FROM acquisto WHERE ID_utente=".$_SESSION['id']." and ID_film=".$idFilm;
                $queryNoleggio="SELECT ID_utente FROM noleggio WHERE scadenza_noleggio > CURRENT_TIMESTAMP AND noleggio.ID_utente=".$_SESSION['id']." and noleggio.ID_film=".$idFilm;
                $presenzaAcquisto=$connessione->interrogaDB($queryAcquisto);
                $presenzaNoleggio=$connessione->interrogaDB($queryNoleggio);

                if(!$presenzaAcquisto){
                    $pagina=str_replace('%pulsanteAcquisto%',
                    '<form action="acquisto_noleggio.php" method="get">
                    <input type="hidden" name="idFilm" value="%idFilm%">
                    <input id="acquisto" type="submit" value="Acquista a %prezzoA%&euro;" name="acquisto" class="btn">
                    </form>'  
                    ,$pagina);

                    if($presenzaNoleggio){
                        $pagina=str_replace('%pulsanteNoleggio%',"Hai già noleggiato questo film. Fino alla scadenza lo puoi trovare nella <a href = '../php/raccolta_personale.php'>tua raccolta</a>.",$pagina);
                    }
                    else{
                        $pagina=str_replace('%pulsanteNoleggio%',
                        '<form action="acquisto_noleggio.php" method="get">
                        <input type="hidden" name="idFilm" value="%idFilm%">
                        <input id="noleggio" type="submit" value="Noleggia a %prezzoN%&euro;" name="noleggio" class="btn">
                        </form>'  
                        ,$pagina);
                    }
                }
                else{
                    $pagina=str_replace('%pulsanteAcquisto%',"Hai già acquistato questo film. Lo puoi trovare nella <a href = '../php/raccolta_personale.php'>tua raccolta</a>",$pagina);

                    $pagina=str_replace('%pulsanteNoleggio%',"",$pagina);
                }
            }
            else{
                $pagina=str_replace('%pulsanteAcquisto%',
                '<form action="login.php" method="get">
                <input type="hidden" name="idFilm" value='.$_GET['idFilm'].'>
                <input id="acquisto" type="submit" value="Acquista a %prezzoA%&euro;" name="acquisto" class="btn">
                </form>'  
                ,$pagina);
                $pagina=str_replace('%pulsanteNoleggio%',
                '<form action="login.php" method="get">
                <input type="hidden" name="idFilm" value='.$_GET['idFilm'].'>
                <input id="noleggio" type="submit" value="Noleggia a %prezzoN%&euro;" name="noleggio" class="btn">
                </form>'  
                ,$pagina);
            }
        }
    }   

?>