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
    passwd VARCHAR(64),
    name VARCHAR(255),
    lastname VARCHAR(255),
    rol INT(1),
    openTickets INT(1),
	CONSTRAINT FOREIGN KEY (rol) REFERENCES Rol (idRol),
    CONSTRAINT CHECK (openTickets<=3)
);
INSERT INTO AppUser VALUES
(1, 'alexmm@empresa.com', '$2y$10$PCTCytZvlQOphkhPUPF7aOVXbS8P9HNu94bmh98o3ossYoAbfxoje', 'Álex', 'Mayo Martín', 2, 0),
(2, 'danivs@soporte.empresa.com', '$2y$10$vioJfpkWYucGMJVwvmBkzuxQ6jg300Wojqhdff0sIqFes.hIj75TW', 'Dani', 'Vals Simón', 1, 0),
(3, 'ivanag@soporte.empresa.com', '$2y$10$7D4DHXeugcpaeZrEfAnPRuj2/i0LEuCd/F0gTDbRCeB/eqcbIYfqm', 'Iván', 'Arroyo González', 1, 0),
(4, 'daniss@empresa.com', '$2y$10$LL9Z3CCF0GJs67/oQnE6heTb7CDEmcDAFt8izQ80LtxcYrot4/lXK', 'Daniel', 'Sierra Solís', 2, 2);

CREATE TABLE Ticket (
	idTicket INT(10) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255),
    priority INT(1),
    subject VARCHAR(255),
    state INT(1),
    messBody TEXT,
    attachment VARCHAR(100),
    sentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FOREIGN KEY(email) REFERENCES AppUser(email) ON DELETE CASCADE,
    CONSTRAINT FOREIGN KEY(priority) REFERENCES Priority(idPr),
    CONSTRAINT FOREIGN KEY(state) REFERENCES State(idState)
);
INSERT INTO Ticket(idTicket,email,priority,subject,state,messBody) VALUES
(1,'alexmm@empresa.com',3,'No funciona el internet',1,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no se me conecta a Internet'),
(2,'alexmm@empresa.com',4,'No funciona el bluetooth',3,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no puedo enviar cosas por Bluetooth'),
(3,'daniss@empresa.com',2,'No enciende el ordenador',2,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no enciende'),
(4,'alexmm@empresa.com',3,'No abre VSC',1,
'Ayer instalé una actualización de Windows y petó el ordenador y ya no abre Visual Studio Code'),
(5,'daniss@empresa.com',1,'Brecha de seguridad',2,
'Abrí un link de un correo spam y no puedo acceder a ningún archivo porque están cifrados y no tengo la contraseña');

CREATE TABLE Answer (
    idAnswer INT(100) PRIMARY KEY AUTO_INCREMENT,
    idTicket INT(10),
    email VARCHAR(255),
    messBody TEXT,
    attachment VARCHAR(100),
    ansDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FOREIGN KEY(email) REFERENCES AppUser(email) ON DELETE CASCADE,
    CONSTRAINT FOREIGN KEY(idTicket) REFERENCES Ticket(idTicket) ON DELETE CASCADE
);
INSERT INTO Answer(idAnswer,idTicket,email,messBody) VALUES
(1,1,'danivs@soporte.empresa.com','¿Has probado a reiniciar el wifi? ¿Y el ordenador?'),
(2,1,'ivanag@soporte.empresa.com','Elimina la actualización y espera a que la arreglen'),
(3,4,'ivanag@soporte.empresa.com','Desinstala y vuelve a instalar');

CREATE TABLE Rating (
    idTechnician INT(10) PRIMARY KEY,
    actualRating FLOAT(3,2),
    numOfRatings INT(10),
    CONSTRAINT FOREIGN KEY(idTechnician) REFERENCES AppUser(idUser) ON DELETE CASCADE,
    CONSTRAINT CHECK (actualRating<=5 AND actualRating>=0)
);

INSERT INTO Rating VALUES (2,4.5,4);
INSERT INTO Rating VALUES (3,2.3,8);
