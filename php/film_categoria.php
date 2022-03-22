<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    require_once ('card.php');
    require_once ('info_film.php');

    print_r($_GET);
    $pagina=file_get_contents("../html/film_categoria.html");
    $struttura=new Struttura();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,'','');
    $pagina=str_replace('%categoriaFilm%',$_GET['nomeCategoria'],$pagina);

    if($_GET['nomeCategoria']=='Nuove Uscite'){
    $risultatoCard=recuperaNuoveUscite(15);
    if($risultatoCard)
        $pagina=str_replace('%films%',$risultatoCard,$pagina);
    else
        $pagina=str_replace('%films%',"",$pagina);
    }

    if($_GET['nomeCategoria']=='Azione'){
        $risultatoCard=recuperaAzione(15);
        if($risultatoCard)
            $pagina=str_replace('%films%',$risultatoCard,$pagina);
        else
            $pagina=str_replace('%films%',"",$pagina);
        }
        
        


    $pagina=str_replace('%classifica%',"",$pagina);

     echo $pagina;


?>