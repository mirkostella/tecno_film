<?php
    require_once("sessione.php");
    require_once("connessione.php");
    require_once("gestore_film.php");
    require_once("upload_img.php");
    require_once("struttura.php");

    if($_SESSION['admin']==false){
        header('location: login_admin.php');
        exit();
    }

    $pagina=file_get_contents("../html/ins_film_admin.html");
    $header=file_get_contents('../componenti/header_admin_log.html');
    $menu=file_get_contents('../componenti/menu_admin_log.html');

    $connessione=new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura = new Struttura();

    $struttura->aggiungiHeader_admin($pagina);


    $struttura->aggiungMenu_admin($pagina,'<li><a href="ins_film_admin.php" accesskey="a">Aggiungi film</a></li>',"<li id=\"attivo\">Aggiungi film</li>");
    $queryNomiGeneri="SELECT nome_genere FROM genere ORDER BY nome_genere ASC";
    $generi=$connessione->interrogaDB($queryNomiGeneri);
    $generi_modifica=array();
    foreach($generi as $valore){
        array_push($generi_modifica,$valore['nome_genere']) ;
    }

    $listaGeneri="";
    $pathCopertina="";
    if(isset($_FILES['copertinaFilm']))
        $pathCopertina=$_FILES['copertinaFilm']['name'];

    foreach($generi as $valore){
        $nuovaVoce='<div class="genereFilm"><input type="checkbox" id="'.$valore['nome_genere'].'" name="generi[]" value="'.$valore['nome_genere'].'" class="checkmark"><label for="'.$valore['nome_genere'].'">'.$valore['nome_genere'].'</label></div>';
        $listaGeneri=$listaGeneri.$nuovaVoce;
    }  
    $pagina=str_replace('%listaGeneri%',$listaGeneri,$pagina);
    //se sono arrivato alla pagina cercando di inserire un film
    if(isset($_POST['inserisciFilm'])){
        $generiScelti;
        if(!isset($_POST['generi'])){
            $generiScelti=array();
        }
        else
            $generiScelti=$_POST['generi'];
        $datiNuovoFilm=array(     
            'titolo'=>trim($_POST['titoloFilm']),
            'trama'=>trim($_POST['tramaFilm']),
            'durata'=>trim($_POST['durataFilm']),
            'dataUscita'=>trim($_POST['dataUscitaFilm']),
            'prezzoA'=>trim($_POST['prezzoAcquistoFilm']),
            'prezzoN'=>trim($_POST['prezzoNoleggioFilm']),
            'copertina'=>$pathCopertina,
            'descrizione'=>trim($_POST['descrizione']),
            'generi'=>$generiScelti
        );

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