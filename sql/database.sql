CREATE DATABASE IF NOT EXISTS barakafood;
USE barakafood;

DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS annonces;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    type ENUM('client', 'restaurant') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE annonces (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    categorie VARCHAR(50),
    image_url VARCHAR(500),
    restaurant_nom VARCHAR(100),
    wilaya VARCHAR(50),
    adresse VARCHAR(200),
    statut ENUM('disponible', 'reserve', 'termine') DEFAULT 'disponible',
    user_id INTEGER,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_statut (statut),
    INDEX idx_wilaya (wilaya),
    INDEX idx_date_annonce (date_creation),
    FULLTEXT INDEX idx_search (titre, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservations (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    user_id INTEGER,
    annonce_id INTEGER,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (annonce_id) REFERENCES annonces(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reservation (user_id, annonce_id),
    INDEX idx_user (user_id),
    INDEX idx_date_reservation (date_reservation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (nom, email, password, type) VALUES 
('Restaurant El-Djazair', 'resto@test.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4S.v5N6JK0K5vK1i', 'restaurant'),
('Boulangerie Le Matin', 'boulangerie@test.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4S.v5N6JK0K5vK1i', 'restaurant'),
('Client Test', 'client@test.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4S.v5N6JK0K5vK1i', 'client');

INSERT INTO annonces (titre, description, categorie, image_url, restaurant_nom, wilaya, adresse, user_id) VALUES 
('Couscous aux legumes', 'Delicieux couscous fait maison avec legumes frais', 'Plat Cuisine', 'https://images.unsplash.com/photo-1541529086526-db283c563270?q=80&w=500', 'Restaurant El-Djazair', 'Alger', 'Bir Mourad Rais', 1),
('Lot de pains varies', '10 pains frais du jour, baguettes et pains traditionnels', 'Boulangerie', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=500', 'Boulangerie Le Matin', 'Oran', 'Canastel', 2),
('Tajine poulet olives', 'Tajine traditionnel au poulet et olives vertes', 'Plat Cuisine', 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?q=80&w=500', 'Restaurant El-Djazair', 'Alger', 'Hussein Dey', 1),
('Gateaux traditionnels', 'Assortiment de gateaux algeriens: makroud, baklawa', 'Patisserie', 'https://images.unsplash.com/photo-1558326567-98ae2405596b?q=80&w=500', 'Boulangerie Le Matin', 'Oran', 'Centre ville', 2);
