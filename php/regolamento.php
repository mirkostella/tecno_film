<?php

    //inclusione dei file 
    require_once ('sessione.php');
    require_once ('connessione.php');
    require_once ('struttura.php');
    

    $pagina=file_get_contents("../html/regolamento.html");

    $connessione = new Connessione();
    if(!$connessione->apriConnessione()){
        echo "<div class=\"error_box\">ERRORE DI CONNESSIONE AL DATABASE</div>";
    }

    $struttura=new Struttura();
    $struttura->aggiungiBase($connessione, $pagina);
    $pagina=str_replace("%descrizione%","Qui trovi il regolamento del sito Tecnofilm", $pagina);
    $pagina=str_replace("%keywords%","TecnoFilm, Regolamento, Regole, Condizioni, Noleggio, Acquisto, Metodi di pagamento", $pagina);
    $pagina=str_replace("%titoloPagina%","TecnoFilm: Regolamento", $pagina);
    $pagina=str_replace("%breadcrumb%","<a href=\"../php/index.php\" xml:lang=\"en\" lang=\"en\">Home</a> &gt; <span class=\"grassetto\">Regolamento</span>", $pagina);

    $inAttivo="<li><a href=\"../php/regolamento.php\">Regolamento</a></li>";
    $attivo="<li id=\"attivo\">Regolamento</li>";
    $struttura->aggiungiMenu($pagina,$inAttivo,$attivo);

    $connessione->chiudiConnessione();
    echo $pagina;