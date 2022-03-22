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
DROP TABLE IF EXISTS segnalazione_foto_utente;

CREATE TABLE `admin`(
  `ID` int(11) PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL
);

INSERT INTO admin (`email`,`password`) VALUES ('admin','admin');

CREATE TABLE `foto_utente`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL,
`descrizione` text
)ENGINE = InnoDB;

INSERT INTO `foto_utente` (`path`,`descrizione`) VALUES ('../img/Utenti','immagine profilo di default');

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

CREATE TABLE `segnalazione_foto_utente`(
`ID_utente` INT(10),
`ID_segnalante` INT(10),
PRIMARY KEY (`ID_utente`,`ID_segnalante`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_segnalante`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
)ENGINE = InnoDB;

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
('Comico'),
('Animato'),
('Horror'),
('Storico');


CREATE TABLE `appartenenza`(
`ID_film` INT(10),
`ID_genere` INT(10),
PRIMARY KEY (`ID_film`,`ID_genere`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_genere`) REFERENCES `genere`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

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

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
