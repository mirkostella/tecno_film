<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    

    $pagina=file_get_contents("../html/chi_siamo.html");

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace("%descrizione%","Una breve descrizione di chi ha creato il sito e chi ci lavora", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Progetto, Chi siamo", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Chi siamo", $pagina);
    $pagina=str_replace("%breadcrumb%","<a href = \"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a> &gt; <span class=\"grassetto\">Chi siamo</span>", $pagina);
    $inAttivo="<li><a href=\"../php/chi_siamo.php\">Chi siamo</a></li>";
    $attivo="<li id=\"attivo\">Chi siamo</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    $connessione->chiudiConnessione();

    echo $pagina;