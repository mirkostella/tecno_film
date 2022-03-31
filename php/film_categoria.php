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
    $connessione=new Connessione();
    $connessione->apriConnessione();
    $struttura->aggiungiHeader($pagina);
    $struttura->aggiungiAccount($pagina);
    $struttura->aggiungiMenu($pagina,'','');
    $pagina=str_replace('%categoriaFilm%',$_GET['nomeCategoria'],$pagina);
    $limite = $_GET['limite'];
    $risultatoCard=NULL;

    if($_GET['nomeCategoria']=='Nuove Uscite'){
        $risultatoCard=recuperaNuoveUscite($limite);
        $query_n_film="SELECT COUNT(*) as numero FROM film WHERE year(film.data_uscita) > (year(CURRENT_TIMESTAMP)-1)";
        $num_max_film=$connessione->interrogaDB($query_n_film);
        if($limite <= $num_max_film[0]['numero']){
            $pulsanteVedialtro=pulsanteVediAltro('Nuove Uscite','film_categoria.php', $limite+5);
            $pagina=str_replace('%vedialtro%', $pulsanteVedialtro, $pagina);
        }
        else
            $pagina=str_replace('%vedialtro%', '', $pagina);
    }

    $queryGeneri="SELECT DISTINCT nome_genere FROM genere JOIN appartenenza ON (genere.ID = appartenenza.ID_genere)";
    $ElencoGeneriNonVuoti=$connessione->InterrogaDB($queryGeneri);
    $generi=array();
    foreach($ElencoGeneriNonVuoti as $valore){
        array_push($generi, $valore['nome_genere']);
    }

    foreach($generi as $valore){
        if($_GET['nomeCategoria']==$valore){
            $risultatoCard=recuperaPerGenere($limite, $valore);
            $query_n_film="SELECT COUNT(*) as numero FROM film JOIN appartenenza ON(film.ID=appartenenza.ID_film) JOIN genere ON (appartenenza.ID_genere=genere.ID) WHERE nome_genere='.$valore.'";
            $num_max_film=$connessione->interrogaDB($query_n_film);
            if($risultatoCard){
                if($limite <= $num_max_film[0]['numero']){
                $pulsanteVedialtro=pulsanteVediAltro($valore,'film_categoria.php', $limite+5);
                $pagina=str_replace('%vedialtro%', $pulsanteVedialtro, $pagina);
                }
                else
                    $pagina=str_replace('%vedialtro%', '', $pagina);
            }
            else{
                $pagina=str_replace('%vedialtro%', '', $pagina);
            }   
        }
    }

    if($risultatoCard)
        $pagina=str_replace('%films%',$risultatoCard,$pagina);
    else
        $pagina=str_replace('%films%',"",$pagina);

    $pagina=str_replace('%classifica%',"",$pagina);
    $connessione->chiudiConnessione();

     echo $pagina;
?>