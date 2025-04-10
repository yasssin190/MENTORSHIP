-- Creazione del database
CREATE DATABASE IF NOT EXISTS db_mentori;
USE db_mentori;

-- Tabella mentori
CREATE TABLE IF NOT EXISTS mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    skills TEXT NOT NULL,
    availability ENUM('part-time', 'tempo pieno', 'solo nei fine settimana', 'disponibile in modo flessibile') NOT NULL,
    cv VARCHAR(255) NOT NULL, -- Qui si può salvare il percorso del file caricato
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella utenti (per registrare eventuali utenti della piattaforma)
CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella attivita (per gestire le sessioni, incontri o altre attività)
CREATE TABLE IF NOT EXISTS attivita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255) NOT NULL,
    descrizione TEXT,
    data_evento DATE,
    mentore_id INT,
    utente_id INT,
    FOREIGN KEY (mentore_id) REFERENCES mentori(id) ON DELETE SET NULL,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE SET NULL
);
