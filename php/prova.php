<?php
require_once ('recensione.php');
require_once('sessione.php');
require_once('giudizio.php');
$_SESSION['loggato']=true;
$_SESSION['id']=5;

$pagina=file_get_contents('../html/amministratore.html');

date_default_timezone_set("Europe/Rome");
$data=date("d-m-Y H-m-s");
if(isset($_GET['elimina'])){
    $idRecensione=$_GET['idRecensione'];
    RecensioneUtente::elimina($idRecensione);
}

if(isset($_GET['utile'])){
    $idRecensione=$_GET['idRecensione'];
    $like=new Utile($_SESSION['id'],$idRecensione);
}
if(isset($_GET['segnala'])){
    $idRecensione=$_GET['idRecensione'];
    $segnala=new Segnalazione($_SESSION['id'],$idRecensione);
}
if(isset($_GET['annullaUtile'])){
    $idRecensione=$_GET['idRecensione'];
    $like=new Utile($_SESSION['id'],$idRecensione);
}
if(isset($_GET['annullaSegnalazione'])){
    $idRecensione=$_GET['idRecensione'];
    $segnala=new Segnalazione($_SESSION['id'],$idRecensione);
}

// $array=array(
//     'ID' => 1,
//     'ID_film' => 1,
//     'ID_utente' => $_SESSION['id'],
//     'path' => '../img/img_componenti/profilo.jpg',
//     'username' => 'mirko',
//     'valutazione' => 2,
//     'data' => $data,
//     'testo' => 'sono una recensione',
// );

$connessione=new Connessione();
$query="SELECT recensione.ID,ID_film,ID_utente,path,username,valutazione,data,testo FROM recensione JOIN utente ON (recensione.ID_utente=utente.ID)
JOIN foto_utente ON (utente.ID_foto=foto_utente.ID) ORDER BY data ASC";
$connessione->apriConnessione();
$ris=$connessione->interrogaDB($query);
$lista="";
foreach($ris as $key=>$value){
    $recensione=new RecensioneUtente($value);
    $lista=$lista.$recensione->crea();
}
echo $pagina.$lista;
?>