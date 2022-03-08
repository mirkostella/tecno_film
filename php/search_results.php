<?php

    require_once('struttura.php');
    require_once('connessione.php');

    $pagina=file_get_contents();
    $struttura = new Struttura();
    $struttura->aggiungiHeader($pagina);
	$struttura->aggiungiAccount($pagina);
	$inAttivo=file_get_contents("../componenti/menu.html");
	$attivo=file_get_contents("../componenti/menu.html");
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    if(isset($_GET['ricerca'])){
        if(isset($_GET['input_ricerca']))
        {
            
        }
    }

    echo $pagina;
?>