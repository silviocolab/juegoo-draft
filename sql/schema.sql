SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS draft7a0 CHARACTER SET utf8mb4;
USE draft7a0;
CREATE TABLE usuarios (id INT AUTO_INCREMENT PRIMARY KEY, alias VARCHAR(20) NOT NULL UNIQUE, hash VARCHAR(255) NOT NULL, rol ENUM('jugador','admin') NOT NULL DEFAULT 'jugador', creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE juegos (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(50) NOT NULL);
CREATE TABLE equipos (id INT AUTO_INCREMENT PRIMARY KEY, pais VARCHAR(30) NOT NULL, anio SMALLINT NOT NULL);
CREATE TABLE jugadores (id INT AUTO_INCREMENT PRIMARY KEY, equipo_id INT NOT NULL, nombre VARCHAR(40) NOT NULL, posicion ENUM('ARQ','DEF','MED','DEL') NOT NULL, rating TINYINT UNSIGNED NOT NULL, FOREIGN KEY (equipo_id) REFERENCES equipos(id));
CREATE TABLE partidas (id INT AUTO_INCREMENT PRIMARY KEY, usuario_id INT NOT NULL, juego_id INT NOT NULL, puntos INT NOT NULL, duracion_segundos INT NOT NULL, creada TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (usuario_id) REFERENCES usuarios(id), FOREIGN KEY (juego_id) REFERENCES juegos(id));
INSERT INTO juegos(nombre) VALUES ('Draft 7 a 0');
INSERT INTO equipos(pais,anio) VALUES ('Argentina',1986),('Brasil',1970),('Francia',1998),('Italia',1982),('España',2010),('Alemania',2014);
INSERT INTO jugadores(equipo_id,nombre,posicion,rating) VALUES
(1,'Pumpido','ARQ',82),(1,'Brown','DEF',84),(1,'Ruggeri','DEF',85),(1,'Cuciuffo','DEF',80),(1,'Olarticoechea','DEF',78),(1,'Batista','MED',82),(1,'Giusti','MED',81),(1,'Enrique','MED',80),(1,'Maradona','DEL',99),(1,'Valdano','DEL',86),(1,'Burruchaga','DEL',85),
(2,'Félix','ARQ',80),(2,'Carlos Alberto','DEF',86),(2,'Brito','DEF',80),(2,'Piazza','DEF',80),(2,'Everaldo','DEF',78),(2,'Clodoaldo','MED',84),(2,'Gérson','MED',88),(2,'Rivelino','MED',90),(2,'Jairzinho','DEL',91),(2,'Tostão','DEL',90),(2,'Pelé','DEL',98),
(3,'Barthez','ARQ',84),(3,'Thuram','DEF',87),(3,'Blanc','DEF',86),(3,'Desailly','DEF',89),(3,'Lizarazu','DEF',85),(3,'Deschamps','MED',83),(3,'Petit','MED',83),(3,'Zidane','MED',96),(3,'Djorkaeff','DEL',86),(3,'Henry','DEL',85),(3,'Dugarry','DEL',82),
(4,'Zoff','ARQ',90),(4,'Gentile','DEF',88),(4,'Scirea','DEF',92),(4,'Collovati','DEF',82),(4,'Cabrini','DEF',86),(4,'Tardelli','MED',87),(4,'Oriali','MED',82),(4,'Antognoni','MED',86),(4,'Conti','DEL',85),(4,'Rossi','DEL',93),(4,'Graziani','DEL',84),
(5,'Casillas','ARQ',92),(5,'Ramos','DEF',88),(5,'Piqué','DEF',87),(5,'Puyol','DEF',90),(5,'Capdevila','DEF',80),(5,'Busquets','MED',88),(5,'Xabi Alonso','MED',88),(5,'Xavi','MED',94),(5,'Iniesta','DEL',94),(5,'Villa','DEL',91),(5,'Pedro','DEL',84),
(6,'Neuer','ARQ',94),(6,'Lahm','DEF',90),(6,'Boateng','DEF',87),(6,'Hummels','DEF',88),(6,'Höwedes','DEF',82),(6,'Schweinsteiger','MED',89),(6,'Kroos','MED',90),(6,'Khedira','MED',84),(6,'Müller','DEL',90),(6,'Klose','DEL',86),(6,'Götze','DEL',83);
-- Para hacer admin a una cuenta ya registrada (solo desde el servidor):
-- UPDATE usuarios SET rol='admin' WHERE alias='tu_alias';
