<?php
require_once("sessione.php");
require_once("connessione.php");
require_once("gestore_film.php");
require_once("upload_img.php");

print_r($_POST);
$pagina=file_get_contents("../html/ins_film_admin.html");
$header=file_get_contents('../componenti/header_admin_log.html');
$menu=file_get_contents('../componenti/menu_admin_log.html');
$pagina=str_replace('%headerAdmin%',$header,$pagina);
$pagina=str_replace('%menuAdmin%',$menu,$pagina);
$queryNomiGeneri="SELECT DISTINCT nome_genere FROM genere";
$connessione=new Connessione();
$connessione->apriConnessione();
$generi=$connessione->interrogaDB($queryNomiGeneri);
$listaGeneri="";
foreach($generi as $valore){
    $nuovaVoce='<label for="'.$valore['nome_genere'].'">'.$valore['nome_genere'].'</label>
    <input type="checkbox" id="'.$valore['nome_genere'].'" name="generi[]" value="'.$valore['nome_genere'].'" class="checkmark">';
    $listaGeneri=$listaGeneri.$nuovaVoce;
}  
$pagina=str_replace('%listaGeneri%',$listaGeneri,$pagina);
//se sono arrivato alla pagina cercando di inserire un film
if(isset($_POST['inserisciFilm'])){
    $datiNuovoFilm=array(     
        'titolo'=>$_POST['titoloFilm'],
        'trama'=>$_POST['tramaFilm'],
        'durata'=>$_POST['durataFilm'],
        'dataUscita'=>$_POST['dataUscitaFilm'],
        'prezzoA'=>$_POST['prezzoAcquistoFilm'],
        'prezzoN'=>$_POST['prezzoNoleggioFilm'],
        'copertina'=>"",
        'descrizione'=>$_POST['descrizione'],
        'generi'=>$_POST['generi']
    );
    $gestore=new GestoreFilm($datiNuovoFilm);
    $gestore->inserisciFilm($pagina);
}
else{
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