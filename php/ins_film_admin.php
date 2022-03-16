<?php
require_once("sessione.php");
require_once("connessione.php");
require_once("gestore_film.php");

print_r($_POST);
$pagina=file_get_contents("../html/ins_film_admin.html");
//se sono arrivato alla pagina cercando di inserire un film
if(isset($_POST['inserisciFilm'])){
    $datiNuovoFilm=array(     
        'titolo'=>$_POST['titoloFilm'],
        'trama'=>$_POST['tramaFilm'],
        'durata'=>$_POST['durataFilm'],
        'dataUscita'=>$_POST['dataUscitaFilm'],
        'prezzoA'=>$_POST['prezzoAcquistoFilm'],
        'prezzoN'=>$_POST['prezzoNoleggioFilm'],
        'copertina'=>$_POST['copertinaFilm'],
        'descrizione'=>$_POST['descrizione'],
        'generi'=>$_POST['generi']
    );
    $gestore=new GestoreFilm($datiNuovoFilm);
    //NON inserisco e ricarico la pagina con i messaggi d'errore
    //mantenendo i campi delle form
    // if($gestore->getErrori()){

    // }
    // else{
        
    //     if(!$gestore->inserisciFilm()){
            
    //     }
    // }
    $successo=$gestore->inserisciFilm();
    
}
else{
    $queryNomiGeneri="SELECT DISTINCT nome_genere FROM genere";
    $connessione=new Connessione();
    $connessione->apriConnessione();
    $generi=$connessione->interrogaDB($queryNomiGeneri);
    $listaGeneri="";
    echo "generi";
    print_r($generi);
    foreach($generi as $valore){
        echo "</br> valore";
        print_r($valore['nome_genere']);
        $nuovaVoce='<label for="'.$valore['nome_genere'].'">'.$valore['nome_genere'].'</label>
        <input type="checkbox" id="'.$valore['nome_genere'].'" name="generi[]" value="'.$valore['nome_genere'].'" class="checkmark">';
        
        $listaGeneri=$listaGeneri.$nuovaVoce;
    }
    
    $pagina=str_replace('%listaGeneri%',$listaGeneri,$pagina);
    $pagina=str_replace("%errTitolo%","",$pagina);
    $pagina=str_replace("%errCopertina%","",$pagina);
    $pagina=str_replace("%errAlt%","",$pagina);
    $pagina=str_replace("%errTrama%","",$pagina);
    $pagina=str_replace("%errDurata%","",$pagina);
    $pagina=str_replace("%errNuovoGenere%","",$pagina);
    $pagina=str_replace("%errData%","",$pagina);
    $pagina=str_replace("%errPrezzoA%","",$pagina);
    $pagina=str_replace("%errPrezzoN%","",$pagina);

}
    echo $pagina;
?>