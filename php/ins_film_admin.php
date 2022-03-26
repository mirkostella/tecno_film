<?php
require_once("sessione.php");
require_once("connessione.php");
require_once("gestore_film.php");
require_once("upload_img.php");


print_r($_POST);

if($_SESSION['admin']==false){
    header('location: login_admin.php');
    exit();
}

$pagina=file_get_contents("../html/ins_film_admin.html");
$header=file_get_contents('../componenti/header_admin_log.html');
$menu=file_get_contents('../componenti/menu_admin_log.html');
$pagina=str_replace('%headerAdmin%',$header,$pagina);
$pagina=str_replace('%menuAdmin%',$menu,$pagina);
$queryNomiGeneri="SELECT nome_genere FROM genere ORDER BY nome_genere ASC";
$connessione=new Connessione();
$connessione->apriConnessione();
$generi=$connessione->interrogaDB($queryNomiGeneri);
$generi_modifica=array();
foreach($generi as $valore){
    array_push($generi_modifica,$valore['nome_genere']) ;
}

print_r($generi_modifica);

$listaGeneri="";
foreach($generi as $valore){
    $nuovaVoce='<label for="'.$valore['nome_genere'].'">'.$valore['nome_genere'].'</label>
    <input type="checkbox" id="'.$valore['nome_genere'].'" name="generi[]" value="'.$valore['nome_genere'].'" class="checkmark" %'.$valore['nome_genere'].'%>';
    $listaGeneri=$listaGeneri.$nuovaVoce;
}  
$pagina=str_replace('%listaGeneri%',$listaGeneri,$pagina);
//se sono arrivato alla pagina cercando di inserire un film
if(isset($_POST['inserisciFilm'])){
    
    $datiNuovoFilm=array(     
        'titolo'=>trim($_POST['titoloFilm']),
        'trama'=>trim($_POST['tramaFilm']),
        'durata'=>trim($_POST['durataFilm']),
        'dataUscita'=>trim($_POST['dataUscitaFilm']),
        'prezzoA'=>trim($_POST['prezzoAcquistoFilm']),
        'prezzoN'=>trim($_POST['prezzoNoleggioFilm']),
        'copertina'=>"",
        'descrizione'=>trim($_POST['descrizione']),
        'generi'=>$_POST['generi']
    );

    $gestore=new GestoreFilm($datiNuovoFilm);
    if(!$gestore->inserisciFilm($pagina)){
        $pagina=str_replace('%titolo%', $datiNuovoFilm['titolo'], $pagina);
        $pagina=str_replace('%copertina%', $datiNuovoFilm['copertina'], $pagina);
        $pagina=str_replace('%altCopertina%', $datiNuovoFilm['descrizione'], $pagina);
        $pagina=str_replace('%dataUscita%', $datiNuovoFilm['dataUscita'], $pagina);
        $pagina=str_replace('%prezzoA%', $datiNuovoFilm['prezzoA'], $pagina);
        $pagina=str_replace('%prezzoN%', $datiNuovoFilm['prezzoN'], $pagina);
        print_r($generi);
        echo "</br>";
        print_r($_POST['generi']);


        foreach($_POST['generi'] as $valore){
            $pagina=str_replace('%'.$valore.'%', 'checked="checked"', $pagina);
        }

        $diffGeneri=array_diff($generi_modifica, $_POST['generi']);
        foreach($diffGeneri as $valore){
            $pagina=str_replace('%'.$valore.'%', "", $pagina);
        }
        $pagina=str_replace('%trama%', $datiNuovoFilm['trama'], $pagina);
    }
}
else{
    $pagina=str_replace('%titolo%', "", $pagina);
    $pagina=str_replace('%copertina%', "", $pagina);
    $pagina=str_replace('%altCopertina%', "", $pagina);
    $pagina=str_replace('%dataUscita%', "", $pagina);
    $pagina=str_replace('%prezzoA%', "", $pagina);
    $pagina=str_replace('%prezzoN%', "", $pagina);
    $pagina=str_replace('%trama%', "", $pagina);
    $pagina=str_replace("%errTitolo%","",$pagina);
    $pagina=str_replace("%errCopertina%","",$pagina);
    $pagina=str_replace("%errAlt%","",$pagina);
    $pagina=str_replace("%errTrama%","",$pagina);
    $pagina=str_replace("%errDurata%","",$pagina);
    $pagina=str_replace("%errData%","",$pagina);
    $pagina=str_replace("%errPrezzoA%","",$pagina);
    $pagina=str_replace("%errPrezzoN%","",$pagina);
    $pagina=str_replace("%esitoTransazione%","",$pagina);
}
    echo $pagina;
?>