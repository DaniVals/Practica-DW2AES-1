DROP DATABASE IF EXISTS projectdwes_1term;
CREATE DATABASE projectdwes_1term;
USE projectdwes_1term;

CREATE TABLE Priority (
	idPr INT(1) PRIMARY KEY,
    name VARCHAR(20) UNIQUE
);
INSERT INTO Priority VALUES
	(1,"very high"),
	(2,"high"),
	(3,"standard"),
	(4,"low");

CREATE TABLE State (
    idState INT(1) PRIMARY KEY,
    name VARCHAR(20) UNIQUE
);
INSERT INTO State VALUES
	(3, 'closed'),
	(2, 'in progress'),
	(1, 'solved');

CREATE TABLE Rol (
	idRol INT(1) PRIMARY KEY,
	name VARCHAR(20) UNIQUE
);
INSERT INTO Rol VALUES
	(1, "technician"),
	(2, "employee");

CREATE TABLE AppUser (
	idUser INT(10) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    passwd VARCHAR(16),
    name VARCHAR(255),
    lastname VARCHAR(255),
    rol INT(1),
	CONSTRAINT FOREIGN KEY (rol) REFERENCES Rol (idRol)
);
INSERT INTO AppUser (email,passwd,name,lastname,rol) VALUES
('alexmm@empresa.com', 'alexmm', 'Álex', 'Mayo Martín', 2),
('danivs@soporte.empresa.com', 'danivs', 'Dani', 'Vals Simón', 1),
('ivanag@soporte.empresa.com', 'ivanag', 'Iván', 'Arroyo González', 1),
('daniss@empresa.com', 'daniss', 'Daniel', 'Sierra Solís', 2);

CREATE TABLE Ticket (
	idTicket INT(10) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255),
    priority INT(1),
    subject VARCHAR(255),
    state INT(1),
    messBody VARCHAR(255),
    CONSTRAINT FOREIGN KEY(email) REFERENCES AppUser(email),
    CONSTRAINT FOREIGN KEY(priority) REFERENCES Priority(idPr),
    CONSTRAINT FOREIGN KEY(state) REFERENCES State(idState)
);
INSERT INTO Ticket(email,priority,subject,state,messBody) VALUES
('alexmm@empresa.com',3,'No funciona el internet',1,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no se me conecta a Internet'),
('alexmm@empresa.com',4,'No funciona el bluetooth',3,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no puedo enviar cosas por Bluetooth'),
('daniss@empresa.com',2,'No enciende el ordenador',2,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no enciende'),
('alexmm@empresa.com',3,'No abre VSC',1,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no abre Visual Studio Code'),
('daniss@empresa.com',1,'Brecha de seguridad',2,
'Abrí un link de un correo spam y no puedo acceder a ningún archivo porque están cifrados y no tengo la contraseña');

CREATE TABLE Answer (
    idAnswer INT(100) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    messBody VARCHAR(255),
    CONSTRAINT FOREIGN KEY(email) REFERENCES AppUser(email)
);