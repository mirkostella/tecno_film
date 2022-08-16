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

DROP VIEW IF EXISTS appartenenzaNoDoppioni;
DROP VIEW IF EXISTS nvoti;
DROP VIEW IF EXISTS n_acquisti;
DROP VIEW IF EXISTS n_noleggi;


CREATE TABLE `admin`(
  `ID` int(11) PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL
);

INSERT INTO admin (`email`,`password`) VALUES ('admin','admin');

CREATE TABLE `foto_utente`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL)ENGINE = InnoDB;

INSERT INTO `foto_utente` (`path`) VALUES ('../img/Utenti/profilo.jpg');

CREATE TABLE `utente` (
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`username` varchar(16) NOT NULL,
`password` varchar(32) NOT NULL,
`email` varchar(64) NOT NULL,
`nome` varchar(32) NOT NULL,
`cognome` varchar(32) NOT NULL,
`data_nascita` date NOT NULL,
`sesso` enum('M','F') NOT NULL,
`stato` enum('Attivo','Avvisato','Bloccato') NOT NULL,
`ID_foto` INT(10) NOT NULL,
FOREIGN KEY (`ID_foto`) REFERENCES `foto_utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `utente` (`username`,`password`,`email`,`nome`,`cognome`,`data_nascita`,`sesso`,`stato`,`ID_foto`) VALUES ('user','user','user@user','utente','generico','1998/04/12',
'M','Attivo',1);


CREATE TABLE `foto_film` (
`ID` int(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL,
`descrizione` text
)ENGINE = InnoDB;

INSERT INTO `foto_film`(`path`) VALUES 
('../img/img_film/1647963180.jpg'),
('../img/img_film/1647963239.jpg'),
('../img/img_film/1647963469.jpg'),
('../img/img_film/1648496841.jpg'),
('../img/img_film/1648496955.png');


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

INSERT INTO `film`(`titolo`, `copertina`, `trama`, `durata`, `data_uscita`, `prezzo_acquisto`, `prezzo_noleggio`) VALUES

('Baby Boss 2', '1', "Baby Boss 2, diretto da Tom McGrath, è ambiente diversi anni dopo il primo film, quando Tim e Ted sono ormai adulti. Racconta la storia della famiglia di Tim, ormai CEO di un Fondo speculativo e sposato con Carol, da cui ha avuto due figlie, il genio Tabitha di 7 anni e la piccola Tina, l'ultima arrivata. Vivono in periferia e sembrano una comune famiglia, ma in verità Tabhita frequenta il Centro Acorn per Bambini Dotati ed è la migliore del corso. La bambina segue da tutta la vita un modello, suo zio Ted, un uomo sagace e molto intelligente. Suo padre, però, è preoccupato per lei e crede che Tabitha non stia vivendo a pieno la sua infanzia, perché troppo impegnata a comportarsi come una bambina geniale.
Ma accade qualcosa di inaspettato in famiglia: la piccola Tina rivela di essere un'agente segreto del BabyCorp in incognito. È stata inviata in missione per scoprire se la scuola di Tabitha e soprattutto il suo fondatore, il Dr. Edwin Armstrong, nascondano qualche oscuro segreto. La famiglia dei Templeton si ritrova ancora una volta riunita e pronta a scoprire quali sono le priorità all'interno di un nucleo familiare.", '01:30:00', '2021/10/07', '12.99', '3.99'),

('Clifford', '2', "Clifford: Il Grande Cane Rosso, diretto da Walt Becker, racconta la storia di un cucciolo di cane di colore rosso, che viene regalato a una bambina, Emily Elizabeth, per il suo compleanno da un uomo anziano molto eccentrico. Quando la bimba chiede quanto diventerà grande il cucciolo, l'uomo le risponde che tutto dipende da quanto affetto lei gli darà. Emily, però, non immagina che sia letteralmente così e il giorno dopo, quando si sveglia, si rende conto che Clifford, questo il nome del cucciolo, è cresciuto a tal punto da essere diventato un cane fuori misura.
Ora Clifford è ancora un cucciolo, ma è altro più di 3 metri e con le sue enormi dimensioni getta il piccolo appartamento di New York, in cui vive Emily, nel completo caos. A occuparsi di questo gigantesco problema sono la sua padroncina e suo zio Casey, che dovranno risolvere la situazione prima che la madre di Emily torni a casa. Per Emily e Casey inizia così una grande avventura - in tutti i sensi - in giro per la Grande Mela insieme al gigantesco cucciolo rosso.", '01:30:00', '2021/12/02', '12.99', '3.99'),

('Masquerade', '3', "Masquerade, film diretto da Nicola Bedos, racconta la storia di Adrien, un giovane ballerino molto affascinante, che è stato costretto a lasciare il mondo della danza a causa di un incidente che ha mandato all'aria la sua carriera. Ora la sua vita si è ridotta a l'oziare nella casa in Costa Azurra, dove vive in compagnia e mantenuto da Martha, un'attempata ex attrice.
La sua esistenza così monotona cambia completamente, però, quando incontra Margot, una ragazza molto bella che vive di piccole truffe. Per Adrien è subito colpo di fulmine. I due iniziano a frequentarsi e con la sua presenza Martha rompe il ciclo ozioso di Adrien, con il quale inizia a trascorrere le giornate. I due giovani si raccontano e fantasticano, soprattutto su una vita migliore di quella che conducono, ma proprio questi loro pensieri li portano a mettere in atto un piano diabolico: una truffa ai danni di un ricco imprenditore.", '01:30:00', '2021/07/30', '12.99', '3.99'),

('Venom', '4', "Venom, il protettore letale, uno dei personaggi Marvel più enigmatici, complessi e tosti.
Nel film diretto da Ruben Fleischer, il giornalista investigativo Eddie Brock nel tentativo di rianimare la sua carriera, inizia ad indagare su uno scandalo che coinvolge la Life Foundation, una sofisticata organizzazione senza scrupoli formata da un gruppo survivalista. E' così che entra in contatto con un'entità aliena con la quale si fonde ottenendo superpoteri. Il rapporto tra Brock e il simbionte è quello di un 'ibrido' con i due personaggi che condividono lo stesso corpo e che si vedono costretti da lavorare insieme.", '01:30:00', '2018/10/04', '12.99', '3.99'),

('Venom, La furia di Carnage', '5', "Dopo aver trovato un corpo ospite nel giornalista investigativo Eddie Brock, il simbionte alieno dovrà affrontare un nuovo nemico, Carnage alter ego del serial killer Cletus Kasady. Nella scena post credits del primo film, infatti, Eddie Brock in sella alla sua moto raggiunge la prigione di San Quentin per intervistare l'omicida. Durante il loro breve dialogo, Kasady preannuncia che riuscirà a uscire dalla prigione e a compire una nuova carneficina. Il killer, infatti, riesce a evadere dal carcere, ospitando un simbionte alieno, il rosso Carnage, che seminerà il terrore in città. Solo Brock e il suo Venom possono fermarlo", '01:30:00', '2021/10/14', '12.99', '3.99');

CREATE TABLE `acquisto`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_acquisto` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

CREATE TABLE `noleggio`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`scadenza_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`,`data_noleggio`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

CREATE TABLE `genere`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`nome_genere` VARCHAR(32) NOT NULL
)ENGINE = InnoDB;

INSERT INTO genere (`nome_genere`) VALUES
('Azione'),
('Drammatico'),
('Commedia'),
('Animazione'),
('Horror'),
('Storico'),
('Thriller'),
('Romantico'),
('Fantasy'),
('Biografico');


CREATE TABLE `appartenenza`(
`ID_film` INT(10),
`ID_genere` INT(10),
PRIMARY KEY (`ID_film`,`ID_genere`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_genere`) REFERENCES `genere`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `appartenenza`(`ID_film`, `ID_genere`) VALUES
('1', '4'),
('1', '3'),
('2', '4'),
('2', '3'),
('3', '1'),
('4', '1'),
('4', '7'),
('5', '1'),
('5', '7');

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

INSERT INTO `recensione`(`ID_film`, `ID_utente`, `testo`, `valutazione`) VALUES
('1', '1', 'Bellissimo', '4'),
('2', '1', 'Interessante', '3'),
('3', '1', 'Cast bravissimo', '5'),
('1', '1', 'Regista super', '4'),
('5', '1', 'Fotografia pazzesca', '4'),
('3', '1', 'Poteva essere migliore', '3');


CREATE TABLE `segnalazione`(
`ID_utente` INT(10),
`ID_recensione` INT(10),
PRIMARY KEY (`ID_utente`,`ID_recensione`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_recensione`) REFERENCES `recensione`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

CREATE TABLE `utile`(
`ID_utente` INT(10),
`ID_recensione` INT(10),
PRIMARY KEY (`ID_utente`,`ID_recensione`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_recensione`) REFERENCES `recensione`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE VIEW `appartenenzaNoDoppioni` AS SELECT `ID_film`, `ID_genere` FROM `appartenenza` JOIN `genere` ON (appartenenza.ID_genere=genere.ID) GROUP BY `ID_film`; 

CREATE VIEW `nvoti` AS SELECT `recensione`.`ID_film` AS `ID_film`,count(*) AS `n_voti` FROM`recensione` GROUP BY`recensione`.`ID_film`;

CREATE VIEW `n_acquisti` AS SELECT `acquisto`.`ID_film` AS `ID`, count(*) AS `N_acquisti` from `acquisto` GROUP BY `acquisto`.`ID_film`;

CREATE VIEW `n_noleggi` AS SELECT `noleggio`.`ID_film` AS `ID`, count(*) AS `N_noleggi` from `noleggio` GROUP BY `noleggio`.`ID_film`;

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
