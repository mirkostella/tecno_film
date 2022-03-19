SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
SET @@session.time_zone = "+01:00";
START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;
DROP VIEW IF EXISTS filmvalutazionegenere;
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
  `ID` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY(`ID`)
);

INSERT INTO `admin` (`ID`, `email`, `password`) VALUES
(1, 'admin@tecnofilm.com', '1AdminOnly');

CREATE TABLE `foto_utente`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL,
`descrizione` text
)ENGINE = InnoDB;

INSERT INTO `foto_utente`(`path`,`descrizione`) VALUES ('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente'),
('../img/img_componenti/profilo.jpg','immagine dell utente');

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

INSERT INTO `utente` (`username`,`password`,`email`,`nome`,`cognome`,`data_nascita`,`sesso`,`ID_foto`) VALUES 
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1),
('mirkos','mgm','shfk@jdkj','mirko','stella','2013-11-03','M',1);

CREATE TABLE `foto_film` (
`ID` int(10) PRIMARY KEY AUTO_INCREMENT,
`path` text NOT NULL,
`descrizione` text
)ENGINE = InnoDB;

INSERT INTO `foto_film` (`path`,`descrizione`) VALUES 
('../img/img_film/venom.jpg','Venom é un film molto bello'),
('../img/img_film/shang_chi.jpg','Venom é un film molto bello'),
('../img/img_film/raging_fire.jpg','Venom é un film molto bello'),
('../img/img_film/boss_baby.jpg','Venom é un film molto bello'),
('../img/img_film/no_time_to_die.jpg','Venom é un film molto bello'),
('../img/img_film/ciao.jpg','Venom é un film molto bello');

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

INSERT INTO `film` (`titolo`,`copertina`,`trama`,`durata`,`data_uscita`,`prezzo_acquisto`,`prezzo_noleggio`) VALUES 
('Venom',1,'sono la trama','00:10:00','2018-12-13',12.99,3.99),
('shang_chi',2,'sono la trama','00:10:00','2018-12-18',12.99,3.99),
('raging fire',3,'sono la trama','00:10:00','2018-12-20',12.99,3.99),
('baby boss',4,'sono la trama','00:10:00','2018-12-13',12.99,3.99),
('no time to die',5,'sono la trama','00:10:00','2018-12-17',12.99,3.99),
('guerre stellari',6,'sono la trama','00:10:00','2018-12-11',12.99,3.99);

CREATE TABLE `segnalazione_foto_utente`(
`ID_utente` INT(10),
`ID_segnalante` INT(10),
PRIMARY KEY (`ID_utente`,`ID_segnalante`),
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_segnalante`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE 
)ENGINE = InnoDB;

INSERT INTO `segnalazione_foto_utente`(`ID_utente`,`ID_segnalante`)VALUES 
(1,2),
(1,3),
(1,4);

CREATE TABLE `acquisto`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_acquisto` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `acquisto` (`ID_film`,`ID_utente`) VALUES 
(1,1);

CREATE TABLE `noleggio`(
`ID_film` int(10),
`ID_utente` INT(10),
`data_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`scadenza_noleggio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`ID_film`,`ID_utente`,`data_noleggio`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_utente`) REFERENCES `utente`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `noleggio` (`ID_film`,`ID_utente`) VALUES 
(1,1);

CREATE TABLE `genere`(
`ID` INT(10) PRIMARY KEY AUTO_INCREMENT,
`nome_genere` VARCHAR(32) NOT NULL
)ENGINE = InnoDB;

INSERT INTO `genere` (`nome_genere`) VALUES 
('horror'),
('azione'),
('romantico'),
('comico');

CREATE TABLE `appartenenza`(
`ID_film` INT(10),
`ID_genere` INT(10),
PRIMARY KEY (`ID_film`,`ID_genere`),
FOREIGN KEY (`ID_film`) REFERENCES `film`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`ID_genere`) REFERENCES `genere`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE = InnoDB;

INSERT INTO `appartenenza` (`ID_film`,`ID_genere`) VALUES 
(1,1),
(2,3),
(3,1),
(4,2),
(5,1),
(6,4);

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

INSERT INTO `recensione` (`ID_film`,`ID_utente`,`testo`,`valutazione`) VALUES 
(1,1,'ciao',4),
(2,1,'ciao',2),
(3,1,'ciao',3),
(4,1,'ciao',1),
(1,2,'ciao',3),
(1,3,'ciao',5),
(1,4,'ciao',2),
(1,5,'ciao',1),
(1,6,'ciao',4),
(1,7,'ciao',3),
(5,1,'ciao',4),
(6,1,'fafafaw',2)
;

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

CREATE VIEW `filmvalutazionegenere`  AS 
SELECT `film`.`ID` AS `idFilm`, `genere`.`nome_genere` AS `nome_genere`, avg(`recensione`.`valutazione`) AS `voto`, `film`.`data_uscita` AS `data_uscita` FROM (((`film` join `appartenenza` on(`appartenenza`.`ID_film` = `film`.`ID`)) join `genere` on(`appartenenza`.`ID_genere` = `genere`.`ID`)) left join `recensione` on(`film`.`ID` = `recensione`.`ID_film`)) GROUP BY `film`.`ID` ORDER BY `film`.`data_uscita` DESC ;

CREATE VIEW `appartenenzaNoDoppioni`  
AS  
SELECT* FROM `appartenenza` GROUP BY ID_film;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;