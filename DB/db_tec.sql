SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
SET @@session.time_zone = "+01:00";
SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS acquisto;
DROP TABLE IF EXISTS noleggio;
DROP TABLE IF EXISTS appartenenza;
DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS genere;
DROP TABLE IF EXISTS film;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS segnalazione;
DROP TABLE IF EXISTS utile;
DROP TABLE IF EXISTS foto_film;
DROP TABLE IF EXISTS foto_utente;

DROP VIEW IF EXISTS nvoti;
DROP VIEW IF EXISTS n_acquisti;
DROP VIEW IF EXISTS n_noleggi;


CREATE TABLE `admin`(
  `ID` int(11) PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL
);

INSERT INTO `admin` (`email`,`password`) VALUES ('admin','admin');

CREATE TABLE `foto_utente`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL)ENGINE = InnoDB;

INSERT INTO `foto_utente` (`path`) VALUES 
('../img/Utenti/profilo.jpg'),
('../img/Utenti/1.jpg'),
('../img/Utenti/2.jpg'),
('../img/Utenti/3.jpg'),
('../img/Utenti/4.jpg'),
('../img/Utenti/5.jpg'),
('../img/Utenti/6.jpg'),
('../img/Utenti/7.png'),
('../img/Utenti/8.jpg'),
('../img/Utenti/9.jpg');


CREATE TABLE `utente` (
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`username` varchar(16) NOT NULL,
`password` varchar(32) NOT NULL,
`email` varchar(64) NOT NULL,
`nome` varchar(32) NOT NULL,
`cognome` varchar(32) NOT NULL,
`data_nascita` date NOT NULL,
`sesso` enum('M','F') NOT NULL,
`stato` enum('Attivo','Bloccato') NOT NULL,
`ID_foto` INT(10) NOT NULL,
FOREIGN KEY (`ID_foto`) REFERENCES `foto_utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `utente` (`username`,`password`,`email`,`nome`,`cognome`,`data_nascita`,`sesso`,`stato`,`ID_foto`) VALUES 

('user','user','user','utente','generico','1998/04/12','M','Attivo',1),
('paolorossi1','Tappeto10','paolo.rossi@gmail.com','Paolo','Rossi','1980/07/5','M','Attivo',2),
('mariobianchi7','Telefono8','mario.bianchi@gmail.com','Mario','Bianchi','1975/08/06','M','Attivo',3),
('antoniodinatale','Udinese10','totò.dinatale@gmail.com','Antonio','Di Natale','1980/12/27','M','Attivo',4),
('lucatoni9','Fiorentina9','luca.toni@gmail.com','Luca','Toni','1989/10/8','M','Attivo',5),
('saragama','Juventus4','sara.gama@gmail.com','Sara','Gama','1993/08/19','F','Attivo',6),
('crisgirelli','Juventus10','cristiana.girelli@gmail.com','Cristiana','Girelli','1992/09/17','F','Attivo',7),
('manugiugliano6','Romanista99','manu.giugliano@gmail.com','Manuela','Giugliano','1999/11/01','F','Attivo',8),
('antoniocassano','Bobotv10','antonio.cassano@gmail.com','Antonio','Cassano','1979/10/12','M','Bloccato',9),
('valegiacinti','Milanista9','vale.giacinti@gmail.com','Valentina','Giacinti','1989/05/04','F','Attivo',10);


CREATE TABLE `foto_film` (
`ID` int(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL,
`descrizione` text
)ENGINE = InnoDB;

INSERT INTO `foto_film` (`path`, `descrizione`) VALUES 
('../img/img_film/1647963180.jpg', 'immagine di copertina del film ^Baby Boss^ 2'),
('../img/img_film/1647963239.jpg', 'immagine di copertina del film ^Clifford^'),
('../img/img_film/1647963469.jpg', 'immagine di copertina del film ^Masquerade^'),
('../img/img_film/1648496841.jpg', 'immagine di copertina del film ^Venom^'),
('../img/img_film/1648496955.png', 'immagine di copertina del film ^Venom^, la furia di ^Carnage^'),
('../img/img_film/chiamamicoltuonome.jpg', 'immagine di copertina del film Chiamami col tuo nome'),
('../img/img_film/elvis.png', 'immagine di copertina del film Elvis'),
('../img/img_film/JurassicPark.jpg', 'immagine di copertina del film ^Jurassic World^: il dominio'),
('../img/img_film/lepaginedellanostravita.jpg', 'immagine di copertina del film Le pagine della nostra vita'),
('../img/img_film/luca.jpg', 'immagine di copertina del film Luca'),
('../img/img_film/moonfall.png', 'immagine di copertina del film ^Moonfall^'),
('../img/img_film/morbius.png', 'immagine di copertina del film ^Morbius^'),
('../img/img_film/oceania.jpg', 'immagine di copertina del film Oceania'),
('../img/img_film/releone1.jpg', 'immagine di copertina del film Il re leone'),
('../img/img_film/releone2.png', 'immagine di copertina del film Il re leone 2: il regno di Simba'),
('../img/img_film/sonic2.png', 'immagine di copertina del film ^Sonic^ 2: il film'),
('../img/img_film/uncharted.png', 'immagine di copertina del film Uncharted'),
('../img/img_film/animalifant1.png', 'immagine di copertina del film Animali Fantastici e dove trovarli'),
('../img/img_film/animalifant2.png', 'immagine di copertina del film Animali Fantastici: i crimindi di ^GrindelWald^'),
('../img/img_film/animalifant.png', 'immagine di copertina del film Animali Fantastici: i segreti di Silente');



CREATE TABLE `film` (
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`titolo` varchar(64) NOT NULL,
`copertina` int(10) NOT NULL,
`trama` text NOT NULL,
`durata` time NOT NULL,
`data_uscita` date NOT NULL,
`prezzo_acquisto` decimal(5,2) NOT NULL,
`prezzo_noleggio` decimal(5,2) NOT NULL,
FOREIGN KEY (`copertina`) REFERENCES `foto_film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `film` (`titolo`, `copertina`, `trama`, `durata`, `data_uscita`, `prezzo_acquisto`, `prezzo_noleggio`) VALUES
('^Baby Boss^ 2', '1', '^Baby Boss^ 2 è ambientato diversi anni dopo il primo film, quando Tim e Ted sono ormai adulti. Racconta la storia della famiglia di Tim, ormai <abbr title="Chief Executive Officer">CEO</abbr> di un Fondo speculativo e sposato con Carol, da cui ha avuto due figlie, il genio Tabitha di 7 anni e la piccola Tina, ultima arrivata. Vivono in periferia e sembrano una comune famiglia, ma in verità Tabhita frequenta il Centro Acorn per Bambini Dotati ed è la migliore del corso. La bambina segue da tutta la vita un modello, suo zio Ted, un uomo sagace e molto intelligente. Suo padre, però, è preoccupato per lei e crede che Tabitha non stia vivendo a pieno la sua infanzia, perché troppo impegnata a comportarsi come una bambina geniale.
Ma accade qualcosa di inaspettato in famiglia: la piccola Tina rivela di essere un agente segreto del ^BabyCorp^ in incognito. È stata inviata in missione per scoprire se la scuola di Tabitha e soprattutto il suo fondatore, il <abbr>Dr.</abbr> ^Edwin Armstrong^, nascondano qualche oscuro segreto. La famiglia dei Templeton si ritrova ancora una volta riunita e pronta a scoprire quali sono le priorità di un nucleo familiare.', '01:30:00', '2021/10/07', '12.99', '3.99'),

('Clifford', '2', "Clifford: Il Grande Cane Rosso racconta la storia di un cucciolo di cane di colore rosso, che viene regalato a una bambina, ^Emily Elizabeth^, per il suo compleanno da un uomo anziano molto eccentrico. Quando la bimba chiede quanto diventerà grande il cucciolo, l'uomo le risponde che tutto dipende da quanto affetto lei gli darà. ^Emily^, però, non immagina che sia letteralmente così e il giorno dopo, quando si sveglia, si rende conto che Clifford, questo il nome del cucciolo, è cresciuto a tal punto da essere diventato un cane fuori misura.
Ora Clifford è ancora un cucciolo, ma è altro più di 3 metri e con le sue enormi dimensioni getta il piccolo appartamento di ^New York^, in cui vive ^Emily^, nel completo caos. A occuparsi di questo gigantesco problema sono la sua padroncina e suo zio ^Casey^, che dovranno risolvere la situazione prima che la madre di ^Emily^ torni a casa. Per ^Emily^ e ^Casey^ inizia così una grande avventura - in tutti i sensi - in giro per la Grande Mela insieme al gigantesco cucciolo rosso.", '01:30:00', '2021/12/02', '12.99', '3.99'),

('^Masquerade^', '3', "^Masquerade^ racconta la storia di ^Adrien^, un giovane ballerino molto affascinante, che è stato costretto a lasciare il mondo della danza a causa di un incidente che ha mandato all'aria la sua carriera. Ora la sua vita si è ridotta a l'oziare nella casa in Costa Azurra, dove vive in compagnia e mantenuto da ^Martha^, un'attempata ex attrice.
La sua esistenza così monotona cambia completamente, però, quando incontra ^Margot^, una ragazza molto bella che vive di piccole truffe. Per ^Adrien^ è subito colpo di fulmine. I due iniziano a frequentarsi e con la sua presenza ^Martha^ rompe il ciclo ozioso di ^Adrien^, con il quale inizia a trascorrere le giornate. I due giovani si raccontano e fantasticano, soprattutto su una vita migliore di quella che conducono, ma proprio questi loro pensieri li portano a mettere in atto un piano diabolico: una truffa ai danni di un ricco imprenditore.", '01:30:00', '2021/07/30', '12.99', '3.99'),

('^Venom^', '4', "^Venom^, il protettore letale, uno dei personaggi Marvel più enigmatici, complessi e tosti.
Il giornalista investigativo ^Eddie Brock^ nel tentativo di rianimare la sua carriera, inizia ad indagare su uno scandalo che coinvolge la ^Life Foundation^, una sofisticata organizzazione senza scrupoli formata da un gruppo survivalista. E' così che entra in contatto con un'entità aliena con la quale si fonde ottenendo superpoteri. Il rapporto tra ^Brock^ e il simbionte è quello di un 'ibrido' con i due personaggi che condividono lo stesso corpo e che si vedono costretti da lavorare insieme.", '01:30:00', '2018/10/04', '12.99', '3.99'),

('^Venom^, La furia di ^Carnage^', '5', "Dopo aver trovato un corpo ospite nel giornalista investigativo ^Eddie Brock^, il simbionte alieno dovrà affrontare un nuovo nemico, ^Carnage^ alter ego del ^serial killer Cletus Kasady^. Nella scena ^post credits^ del primo film, infatti, ^Eddie Brock^ in sella alla sua moto raggiunge la prigione di ^San Quentin^ per intervistare l'omicida. Durante il loro breve dialogo, ^Kasady^ preannuncia che riuscirà a uscire dalla prigione e a compire una nuova carneficina. Il ^killer^, infatti, riesce a evadere dal carcere, ospitando un simbionte alieno, il rosso ^Carnage^, che seminerà il terrore in città. Solo ^Brock^ e il suo ^Venom^ possono fermarlo", '01:30:00', '2021/10/14', '12.99', '3.99'),

('Chiamami col tuo nome', '6', "Elio, un diciassettenne sensibile e istruito, è l'unico figlio della famiglia italoamericana ^Perlman^. Oliver è invece un accademico che arriva per aiutare il padre di Elio, un professore famoso specializzato in cultura greca, con il suo lavoro. Il ventiquattrenne è pieno di vita, spontaneo, affascinante e conquista chiunque lo conosca. L'incontro tra i due giovani darà vita a un'estate indimenticabile sulla riviera italiana.", '02:11:00', '2017/12/07', '12.99', '3.99'),

('Elvis', '7', "Quest'opera cinematografica completa esplora la vita e la musica di Elvis attraverso la lente della relazione complicata con il suo enigmatico ^manager^, il Colonnello ^Tom Parker^. Il film, narrato da ^Parker^, analizza gli oltre 20 anni di complessa collaborazione tra i due, dalla scalata al successo di ^Presley^ fino al suo incredibile ruolo di ^star^, nel contesto dell'evoluzione culturale e della perdita dell'innocenza degli Stati Uniti d'America. Agli elementi centrali di questo viaggio si aggiunge anche una delle persone più importanti nella vita di Elvis, ^Priscilla Presley^.", '02:39:00', '2022/07/12', '12.99', '3.99'),

('^Jurassic World^: il dominio', '8', "Termina l'incredibile viaggio iniziato con ^Jurassic Park^ mentre due generazioni si uniscono e combattono per il futuro nel film ^Jurassic World^ - Il Dominio.", '02:11:00', '2022/08/01', '12.99', '3.99'),

('Le pagine della nostra vita', '9', "La giovane debuttante ^Allie Hamilton^ e un ragazzo del luogo, ^Noah Calhoun^, trascorrono insieme un'estate spensierata e s'innamorano. Quando l'estate però finisce, la guerra e la vita separano la giovane coppia. Oggi, un uomo anziano si reca ogni giorno in una casa di riposo per leggere il suo diario ad una donna la cui memoria sta svanendo. L'uomo, nel raccontarle la storia di due giovani innamorati che hanno tutta la vita davanti a loro, fa rivivere alla sua amata ^Allie^ una vecchia passione che non è mai morta, un legame indistruttibile tra due persone comuni che è stato reso straordinario grazie alla forza e alla bellezza del vero amore.", '02:03:00', '2004/10/15', '12.99', '3.99'),

('Luca', '10', "Ambientato in una splendida località costiera della riviera italiana, è la storia di formazione di un ragazzino che vive un'estate indimenticabile tra gelati, pasta e lunghissimi giri in Vespa. Luca condivide le sue avventure col suo nuovo amico Alberto, ma il divertimento è minacciato da un segreto custodito negli abissi: i due sono infatti dei mostri marini provenienti dal mondo sottomarino.", '01:35:00', '2021/06/08', '12.99', '3.99'),

('^Moonfall^', '11', 'In ^Moonfall^ vedremo la Luna che, spinta una forza misteriosa, viene sbalzata fuori dalla sua orbita per dirigersi in rotta di collisione con la Terra. A poche settimane dall’impatto fatale che annienterà il mondo, l’ex astronauta e dirigente <abbr title="National Aeronautics and Space Administration">NASA</abbr> ^Jocinda Fowler^ ha un’idea per salvare il pianeta. Tuttavia, nessuno le crede, a parte ^Brian Harper^, un uomo che appartiene al suo passato, e un simpatico complottista di nome ^K.C. Houseman^. I tre improbabili eroi si lanceranno in una disperata missione spaziale lasciandosi alle spalle, forse per sempre, i loro affetti più cari, per cercare di scoprire un incredibile segreto.', '02:11:00', '2022/03/02', '12.99', '3.99'),

('^Morbius^', '12', "Uno dei più coinvolgenti e conflittuali personaggi Marvel, ^Michael Morbius^, arriva sul grande schermo, interpretato dal premio Oscar ^Jared Leto^, capace di trasformarlo in un enigmatico antieroe. Gravemente malato con una rara malattia ematica e determinato a salvare coloro che soffrono della sua stessa sorte, il Dottor ^Morbius^ prova un disperato azzardo. Una forza oscura interiore, che all'inizio appare come un deciso successo, lo trasforma da guaritore a predatore.", '01:44:00', '2022/01/18', '12.99', '3.99'),

('Oceania', '13', "L'epica avventura di una coraggiosa adolescente che parte per un'audace missione: dimostrarsi esperta navigatrice e portare a termine la missione dei suoi antenati. Durante il viaggio Vaiana incontra Maui, una volta potente semidio, e insieme traversano l'oceano in un viaggio avventuroso e divertente, incontrando enormi creature marine, straordinari mondi sommersi e situazioni impossibili. Durante il viaggio Vaiana scopre l'unica cosa che ha sempre cercato: la sua identità.", '01:47:00', '2016/05/23', '12.99', '3.99'),

('Il re leone', '14', "Partite per una avventura straordinaria con Simba, un cucciolo di leone che non vede l'ora di diventare Re e cerca il suo destino nel grande cerchio della vita.", '01:28:00', '1994/02/25', '12.99', '3.99'),

('Il re leone 2: il regno di Simba', '15', "^Sequel^ de Il re leone.", '01:22:00', '1998/04/04', '12.99', '3.99'),

('^Sonic^ 2: il film', '16', " Dopo essersi stabilito a ^Green Hills^, ^Sonic^ vuole dimostrare di avere la stoffa del vero eroe. La prova arriva quando torna il dottor ^Robotnik^, stavolta con un nuovo aiutante, ^Knuckles^, alla ricerca di uno smeraldo che ha il potere di distruggere intere civiltà. ^Sonic^ si unisce al suo amico, ^Tails^, e insieme si imbarcano in un viaggio in giro per il globo in cerca dello smeraldo, prima che finisca nelle mani sbagliate.", '02:02:00', '2022/06/07', '12.99', '3.99'),

('^Uncharted^', '17', "Il ladro di strada ^Nathan Drake^ viene reclutato dall'esperto cacciatore di tesori ^Victor Sullivan^ per recuperare una fortuna persa da Ferdinando Magellano 500 anni fa. Quella che inizia come una rapina per il duo diventa una corsa all'impazzata per raggiungere la meta prima dello spietato Moncada, che crede che lui e la sua famiglia ne siano gli eredi legittimi. Se ^Nate^ e ^Sully^ riusciranno a decifrare gli indizi e a risolvere uno dei misteri più antichi del mondo, potranno trovare un tesoro da 5 miliardi di dollari e forse anche il fratello di ^Nate^.", '01:55:00', '2022/01/14', '12.99', '3.99'),

('Animali Fantastici e dove trovarli', '18', "Animali fantastici e dove trovarli inizia nel 1926 con ^Newt Scamander^ che ha appena terminato un viaggio in giro per il mondo per cercare e documentare una straordinaria gamma di creature magiche. Arrivato a ^New York^ per una breve pausa, pensa che tutto stia andando per il verso giusto, se non fosse per un babbano di nome ^Jacob^, una valigetta lasciata nel posto sbagliato, e per la fuga di alcuni degli Animali Fantastici di ^Newt^, che potrebbero causare molti problemi sia nel mondo magico che in quello babbano.", '02:12:00', '2016/12/07', '12.99', '3.99'),

('Animali Fantastici: i crimini di ^Grindelwald^', '19', "Alla fine del primo film, il potente Mago Oscuro ^Gellert Grindelwald^ viene catturato con l’aiuto di ^Newt Scamander^. Tuttavia, come aveva minacciato, ^Grindelwald^ riesce a fuggire e inizia a radunare i suoi seguaci, la maggior parte dei quali sono ignari delle sue vere intenzioni: riunire dei maghi purosangue per governare su tutti gli esseri non-magici. Tentando di contrastare i piani di ^Grindelwald^, Albus Silente recluta il suo ex studente ^Newt Scamander^, che accetta di aiutarlo, inconsapevole dei pericoli che si sarebbero prospettati.", '02:12:00', '2018/09/05', '12.99', '3.99'),

('Animali Fantastici: i segreti di Silente', '20', "Il professor Albus Silente sa che il potente mago oscuro ^Gellert Grindelwald^ si sta muovendo per prendere il controllo del mondo magico. Incapace di fermarlo da solo, incarica il magizoologo ^Newt Scamander^ di guidare un'intrepida squadra di maghi, streghe e un coraggioso fornaio babbano in una pericolosa missione. Qui incontreranno animali fantastici vecchi e nuovi e si scontreranno con la crescente legione di seguaci di ^Grindelwald^. Ma con la posta in gioco così alta, per quanto tempo Silente potrà restare in disparte?", '02:22:00', '2022/01/08', '12.99', '3.99');


CREATE TABLE `acquisto`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_acquisto` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `acquisto` (`ID_film`, `ID_utente`, `data_acquisto`) VALUES
('2', '1', '2022/05/03 21:00:38'),
('15', '1', '2022/07/05 23:15:22'),
('7', '1', '2022/08/16 17:10:48'),
('13', '1', '2022/01/27 11:35:18'),
('4', '2', '2022/06/07 16:24:15'),
('16', '2', '2022/06/23 21:07:12'),
('1', '3', '2021/11/08 20:13:47'),
('6', '3', '2021/08/17 18:37:28'),
('13', '3', '2022/01/09 15:55:11'),
('8', '5', '2022/08/12 21:10:13'),
('18', '5', '2022/07/09 21:21:15'),
('19', '5', '2022/07/09 21:22:43'),
('20', '5', '2022/07/09 21:23:58'),
('3', '6', '2021/11/17 19:06:32'),
('5', '7', '2022/05/09 23:40:43'),
('6', '7', '2021/12/23 20:52:17'),
('14', '7', '2022/01/03 15:30:11'),
('15', '7', '2022/01/03 17:20:16'),
('9', '8', '2022/07/13 20:32:52'),
('6', '8', '2022/06/05 20:48:57'),
('19', '9', '2021/09/17 17:25:44'),
('20', '9', '2022/08/16 22:34:58'),
('8', '9', '2022/08/17 23:43:13'),
('7', '10', '2022/08/18 09:32:44'),
('16', '10', '2022/07/09 21:00:11');


CREATE TABLE `noleggio`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`scadenza_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`,`data_noleggio`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `noleggio` (`ID_film`, `ID_utente`, `data_noleggio`, `scadenza_noleggio`) VALUES
('6', '1', '2021/12/23 20:52:17', '2021/12/30 20:52:17'),
('4', '1', '2022/06/07 16:24:15','2022/06/14 16:24:15' ),
('16', '1', '2022/06/23 21:07:12', '2022/06/30 21:07:12'),
('14', '1', '2022/01/03 15:30:11', '2022/01/10 15:30:11'),
('2', '2', '2022/05/03 21:00:38', '2022/05/10 21:00:38'),
('15', '2', '2022/07/05 23:15:22', '2022/07/12 23:15:22'),
('16', '2', '2022/07/09 21:00:11', '2022/07/16 21:00:11'),
('7', '3', '2022/08/16 17:10:48', '2022/08/23 17:10:48'),
('13', '3', '2022/01/27 11:35:18', '2022/02/03 11:35:18'),
('6', '4', '2022/06/05 20:48:57', '2022/06/12 20:48:57'),
('19', '4', '2021/09/17 17:25:44', '2021/09/24 17:25:44'),
('7', '4', '2022/08/18 09:32:44', '2022/08/25 09:32:44'),
('1', '5', '2021/11/08 20:13:47', '2021/11/15 20:13:47'),
('6', '5', '2021/08/17 18:37:28', '2021/08/24 18:37:28'),
('8', '5', '2022/08/17 23:43:13', '2022/08/24 23:43:13'),
('20', '6', '2022/07/09 21:23:58', '2022/07/16 21:23:58'),
('12', '6', '2022/07/09 21:22:43', '2022/07/16 21:22:43'),
('13', '7', '2022/01/09 15:55:11', '2022/01/16 15:55:11'),
('8', '7', '2022/08/12 21:10:13', '2022/08/19 21:10:13'),
('18', '7', '2022/07/09 21:21:15', '2022/07/16 21:21:15'),
('5', '8', '2022/05/09 23:40:43', '2022/05/16 23:40:43'),
('20', '8', '2022/08/16 22:34:58', '2022/08/23 22:34:58'),
('3', '9', '2021/11/17 19:06:32', '2021/11/24 19:06:32'),
('15','10',  '2022/01/03 17:20:16', '2022/01/10 17:20:16'),
('9', '10', '2022/07/13 20:32:52', '2022/07/20 20:32:52');

CREATE TABLE `genere`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`nome_genere` VARCHAR(32) NOT NULL
)ENGINE = InnoDB;

INSERT INTO `genere` (`nome_genere`) VALUES
('Azione'), 
('Drammatico'),
('Commedia'),
('Animazione'),
('^Horror^'),
('Storico'),
('^Thriller^'),
('Romantico'),
('^Fantasy^'),
('Biografico');


CREATE TABLE `appartenenza`(
`ID_film` INT(10),
`ID_genere` INT(10),
PRIMARY KEY (`ID_film`,`ID_genere`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_genere`) REFERENCES `genere`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `appartenenza` (`ID_film`, `ID_genere`) VALUES
('1', '4'),
('1', '3'),
('2', '4'),
('2', '3'),
('3', '1'),
('4', '1'),
('4', '7'),
('5', '1'),
('5', '7'),
('6', '2'),
('6', '8'),
('7', '10'),
('8', '1'),
('8', '9'),
('9', '2'),
('9', '8'),
('10', '3'),
('10', '4'),
('11', '4'),
('11', '3'),
('12', '1'),
('12', '7'),
('12', '9'),
('13', '3'),
('13', '4'),
('14', '4'),
('15', '4'),
('16', '4'),
('16', '1'),
('17', '1'),
('17', '3'),
('18', '9'),
('18', '1'),
('18', '3'),
('19', '9'),
('19', '1'),
('19', '3'),
('20', '1'),
('20', '3'),
('20', '9');

CREATE TABLE `recensione`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`ID_film` INT(10),
`ID_utente` INT(10),
`testo` text NOT NULL,
`data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`valutazione` enum('1','2','3','4','5') NOT NULL,
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `recensione` (`ID_film`, `ID_utente`, `testo`, `valutazione`) VALUES
('1', '1', 'Molto divertente', '4'),
('11', '1', 'Interessante', '3'),
('15', '1', 'Uno dei migliori del genere', '5'),
('17', '2', 'Regista super', '4'),
('18', '3', 'Bello, ma mi aspettavo di più', '4'),
('19', '3', 'Meglio del primo', '5'),
('20', '3', 'Nè bello nè brutto', '3'),
('2', '4', 'Per famiglie', '4'),
('12', '4', 'Wow, interpretazione top', '4'),
('7', '4', 'Cast bravissimo', '5'),
('6', '5', 'Che bello!', '5'),
('12', '6', 'Leto fantastico', '5'),
('4', '7', 'Bello, come tutti quelli della Marvel', '4'),
('5', '7', 'Ottimo, mi è piaciuto', '4'),
('8', '8', 'Mi aspettavo di meglio', '3'),
('6', '9', 'Wow!', '5'),
('8', '9', 'Niente di che', '3'),
('4', '10', 'La Marvel non delude', '5');


CREATE TABLE `segnalazione`(
`ID_utente` INT(10),
`ID_recensione` INT(10),
PRIMARY KEY (`ID_utente`,`ID_recensione`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_recensione`) REFERENCES `recensione`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `segnalazione` (`ID_utente`, `ID_recensione`) VALUES
('2', '16');

CREATE TABLE `utile`(
`ID_utente` INT(10),
`ID_recensione` INT(10),
PRIMARY KEY (`ID_utente`,`ID_recensione`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_recensione`) REFERENCES `recensione`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO `utile` (`ID_utente`, `ID_recensione`) VALUES
('1', '18'),
('1', '16'),
('2', '3');

CREATE VIEW `nvoti` AS SELECT `recensione`.`ID_film` AS `ID_film`,count(*) AS `n_voti` FROM`recensione` GROUP BY`recensione`.`ID_film`;

CREATE VIEW `n_acquisti` AS SELECT `acquisto`.`ID_film` AS `ID`, count(*) AS `N_acquisti` from `acquisto` GROUP BY `acquisto`.`ID_film`;

CREATE VIEW `n_noleggi` AS SELECT `noleggio`.`ID_film` AS `ID`, count(*) AS `N_noleggi` from `noleggio` GROUP BY `noleggio`.`ID_film`;

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
