<?php
    require_once("sessione.php");
    require_once("connessione.php");
    require_once("gestore_film.php");
    require_once("gestore_img.php");
    require_once("struttura.php");
    require_once("lingua.php");
    
    if($_SESSION['admin']==false){
        header('location: ../php/login_admin.php');
        exit();
    }
    
    $pagina=file_get_contents("../html/ins_film_admin.html");
    
    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }
    $queryNomiGeneri="SELECT nome_genere FROM genere ORDER BY nome_genere ASC";
    $generi=$connessione->interrogaDB($queryNomiGeneri);
    $listaGeneri="";
    foreach($generi as $valore){
        $nuovaVoce='<div class="genereFilm"><input type="checkbox" id="'.eliminaDelimitatoriLingua($valore['nome_genere']).
        '" name="generi[]" value="'.$valore['nome_genere'].'" class="checkmark" %'.$valore['nome_genere'].'%><label for="'.
        eliminaDelimitatoriLingua($valore['nome_genere']).'">'.aggiungiSpanLang($valore['nome_genere']).'</label></div>';
        $listaGeneri=$listaGeneri.$nuovaVoce;
    }   
    $pagina=str_replace('%listaGeneri%',$listaGeneri,$pagina);
    $pathCopertina="";
    if(isset($_FILES['copertinaFilm']))
    $pathCopertina=$_FILES['copertinaFilm']['name'];
    //se sono arrivato alla pagina cercando di inserire un film
    if(isset($_POST['inserisciFilm'])){
        $generiScelti;
        if(!isset($_POST['generi'])){
            $generiScelti=array();
        }
        else
            $generiScelti=$_POST['generi'];
        $datiNuovoFilm=array(     
            'titolo'=>$connessione->pulisciStringaSQL(trim($_POST['titoloFilm'])),
            'trama'=>$connessione->pulisciStringaSQL(trim($_POST['tramaFilm'])),
            'durata'=>$connessione->pulisciStringaSQL(trim($_POST['durataFilm'])),
            'dataUscita'=>$connessione->pulisciStringaSQL(trim($_POST['dataUscitaFilm'])),
            'prezzoA'=>$connessione->pulisciStringaSQL(trim($_POST['prezzoAcquistoFilm'])),
            'prezzoN'=>$connessione->pulisciStringaSQL(trim($_POST['prezzoNoleggioFilm'])),
            'copertina'=>$connessione->pulisciStringaSQL($pathCopertina),
            'descrizione'=>$connessione->pulisciStringaSQL(trim($_POST['descrizione'])),
            'generi'=>$generiScelti
        );
        foreach($datiNuovoFilm['generi'] as &$valore){
            $connessione->pulisciStringaSQL($valore);
        }
        $gestore=new GestoreFilm($datiNuovoFilm);
        //se non inserito
        if(!$gestore->inserisciFilm($connessione, $pagina)){
            $pagina=str_replace('%titolo%', $datiNuovoFilm['titolo'], $pagina);
            $pagina=str_replace('%copertina%', $pathCopertina, $pagina);
            $pagina=str_replace('%altCopertina%', $datiNuovoFilm['descrizione'], $pagina);
            $pagina=str_replace('%dataUscita%', $datiNuovoFilm['dataUscita'], $pagina);
            $pagina=str_replace('%prezzoA%', $datiNuovoFilm['prezzoA'], $pagina);
            $pagina=str_replace('%prezzoN%', $datiNuovoFilm['prezzoN'], $pagina);
            $pagina=str_replace('%valoreDurata%', $datiNuovoFilm['durata'], $pagina);
            $generi_modifica=array();
            foreach($generi as $valore){
                array_push($generi_modifica,$valore['nome_genere']) ;
            }
            foreach($generiScelti as $valore){
                $pagina=str_replace('%'.$valore.'%', 'checked="checked"', $pagina);
            }
            
            $diffGeneri=array_diff($generi_modifica, $generiScelti);
            foreach($diffGeneri as $valore){
                $pagina=str_replace('%'.$valore.'%', "", $pagina);
            }
            $pagina=str_replace('%trama%', $datiNuovoFilm['trama'], $pagina);
        }
    }
    $struttura = new Struttura();

    $struttura->aggiungiBaseAdmin($pagina);
    $pagina=str_replace("%descrizione%","Se sei un admin, qua trovi un form per inserire nuovi film", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Film, Copertina, Trama, Generi, Nuovi", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm-Admin: Aggiungi film", $pagina);
    $pagina=str_replace("%breadcrumb%","<span class=\"grassetto\">Aggiungi film</span>", $pagina);

    $pagina=str_replace('<li><a href="../php/ins_film_admin.php" accesskey="a">Aggiungi film</a></li>',"<li id=\"attivo\" accesskey=\"a\">Aggiungi film</li>", $pagina);

    
    $pagina=str_replace('%titolo%', "", $pagina);
    $pagina=str_replace('%copertina%', "", $pagina);
    $pagina=str_replace('%altCopertina%', "", $pagina);
    $pagina=str_replace('%dataUscita%', "", $pagina);
    $pagina=str_replace('%prezzoA%', "", $pagina);
    $pagina=str_replace('%prezzoN%', "", $pagina);
    $pagina=str_replace('%trama%', "", $pagina);
    $pagina=str_replace('%valoreDurata%',"00:00", $pagina);
    $pagina=str_replace("%errTitolo%","",$pagina);
    $pagina=str_replace("%errCopertina%","",$pagina);
    $pagina=str_replace("%errAlt%","",$pagina);
    $pagina=str_replace("%errTrama%","",$pagina);
    $pagina=str_replace("%errDurata%","",$pagina);
    $pagina=str_replace("%errData%","",$pagina);
    $pagina=str_replace("%errPrezzoA%","",$pagina);
    $pagina=str_replace("%errPrezzoN%","",$pagina);
    $pagina=str_replace("%esitoTransazione%","",$pagina);
    $pagina=str_replace('%errGeneri%',"", $pagina);

    $connessione->chiudiConnessione();
    echo $pagina;
?>