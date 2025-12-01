-- Table Client
CREATE TABLE Client (
    id_client SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(40) NOT NULL,
    mail VARCHAR(100) NOT NULL,
    tel CHAR(10) NOT NULL UNIQUE,
    date_naissance DATE NOT NULL,
    date_inscription TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    adresse VARCHAR(256) NOT NULL
);
-- On ajoute juste les colonnes pour la connexion
ALTER TABLE Client 
ADD COLUMN mot_de_passe VARCHAR(255);

-- (Si tu n'avais pas encore mis mail en UNIQUE, fais-le aussi)
ALTER TABLE Client 
ADD CONSTRAINT client_mail_unique UNIQUE (mail);

-- Table Maintenance
CREATE TABLE Maintenance (
    id_maintenance SERIAL PRIMARY KEY,
    type_intervention VARCHAR(50) NOT NULL,
    description TEXT,
    priorite INT NOT NULL CHECK (priorite BETWEEN 1 AND 5),
    statut VARCHAR(20) NOT NULL CHECK (statut IN ('en_cours', 'terminee', 'planifiee', 'reportee')),
    date_prevue TIMESTAMP,
    date_realisation TIMESTAMP
);


-- Table Type_Chambre
CREATE TABLE Type_Chambre (
    id_type SERIAL PRIMARY KEY,
    nom VARCHAR(30) NOT NULL,
    capacite_max INT NOT NULL CHECK (capacite_max BETWEEN 1 AND 10),
    surface_type DECIMAL(5,2) NOT NULL,
    description TEXT
);

-- Table Chambre
CREATE TABLE Chambre (
    id_chambre SERIAL PRIMARY KEY,
    statut VARCHAR(20) NOT NULL CHECK (statut IN ('libre', 'occupée', 'maintenance', 'nettoyage')),
    etage INT NOT NULL,
    prix_base DECIMAL(8,2) NOT NULL,
    superficie DECIMAL(5,2) NOT NULL,
    id_maintenance INT ,
    id_type INT NOT NULL,
    FOREIGN KEY (id_maintenance) REFERENCES Maintenance(id_maintenance),
    FOREIGN KEY (id_type) REFERENCES Type_Chambre(id_type)
);




-- Table Reservation
CREATE TABLE Reservation (
    id_reservation SERIAL PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nombre_personne INT NOT NULL,
    statut VARCHAR(20) NOT NULL CHECK (statut IN ('confirmée', 'en_attente', 'annulee')),
    id_client INT NOT NULL,
    id_chambre INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES Client(id_client),
    FOREIGN KEY (id_chambre) REFERENCES Chambre(id_chambre)
);

-- Table Service
CREATE TABLE Service (
    id_service SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(6,2) NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    categorie VARCHAR(20) NOT NULL CHECK (categorie IN ('wellness', 'restauration', 'transport', 'autre'))

);

-- Table Paiement
CREATE TABLE Paiement (
    id_paiement SERIAL PRIMARY KEY,
    mode_paiement VARCHAR(20) NOT NULL CHECK (mode_paiement IN ('carte', 'especes', 'virement', 'cheque')),
    date_paiement TIMESTAMP NOT NULL,
    numero_transaction VARCHAR(50) NOT NULL UNIQUE
);

-- Table Facture
CREATE TABLE Facture (
    id_facture SERIAL PRIMARY KEY,
    date_emission DATE NOT NULL ,
    montant_total DECIMAL(8,2) NOT NULL,
    tva DECIMAL(5,2) NOT NULL,
    statut_paiement VARCHAR(20) NOT NULL CHECK (statut_paiement IN ('payee', 'impayee', 'partiel', 'remboursee')),
    id_reservation INT NOT NULL,
    id_paiement INT,
    FOREIGN KEY (id_reservation) REFERENCES Reservation(id_reservation)
);

-- Ajout de la contrainte FK manquante dans Facture
ALTER TABLE Facture 
ADD FOREIGN KEY (id_paiement) REFERENCES Paiement(id_paiement);


-- Table Employe
CREATE TABLE Employe (
    id_employe SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(40) NOT NULL,
    poste VARCHAR(30) NOT NULL,
    salaire DECIMAL(8,2) NOT NULL,
    date_embauche DATE NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    id_maintenance INT,
    
    -- NOUVELLES COLONNES DE CONNEXION
    mail_pro VARCHAR(100) NOT NULL UNIQUE, -- Servira de LOGIN
    mot_de_passe VARCHAR(255) NOT NULL,    -- Le mot de passe crypté
    role VARCHAR(20) NOT NULL DEFAULT 'STAFF',
    
    FOREIGN KEY (id_maintenance) REFERENCES Maintenance(id_maintenance)
);

-- AJOUT: Table Consomme (dépend de: Reservation, Service)
CREATE TABLE Consomme (
    id_reservation INT NOT NULL,
    id_service INT NOT NULL,
    type_consommation VARCHAR(100),
    PRIMARY KEY (id_reservation, id_service),
    FOREIGN KEY (id_reservation) REFERENCES Reservation(id_reservation),
    FOREIGN KEY (id_service) REFERENCES Service(id_service)
);



-- =============================================
-- INSERTIONS DES DONNÉES
-- =============================================

-- Insertions Client 
INSERT INTO Client (id_client, nom, prenom, mail, tel, date_naissance, date_inscription, adresse) VALUES
(101, 'Dupont', 'Alice', 'alice.dupont@gmail.com', '0611345078', '1990-04-15', '2025-09-20 14:35:00', '5 Avenue Anatole, 75007 Paris'),
(102, 'Martin', 'Bruno', 'Bruno.martin@yahoo.fr', '0712456789', '1985-11-22', '2025-09-21 10:20:00', '12 Rue de la Paix, 69001 Lyon'),
(103, 'Bernard', 'Claire', 'Claire.bernard@outlook.fr', '0626789456', '1992-07-30', '2025-09-22 16:45:00', '8 Boulevard Victor Hugo, 13001 Marseille'),
(104, 'Petit', 'David', 'David.petit@free.fr', '0734567890', '1988-03-12', '2025-09-23 09:15:00', '15 Rue Gambetta, 33000 Bordeaux'),
(105, 'Rousseau', 'Emma', 'Emma.rousseau@laposte.net', '0645123789', '1995-09-05', '2025-09-24 11:30:00', '22 Avenue Des Champs, 31000 Toulouse'),
(106, 'Robert', 'François', 'Francois.robert@sfr.fr', '0756234901', '1982-12-18', '2025-09-25 14:00:00', '7 Place de la République, 59000 Lille'),
(107, 'Simon', 'Gabrielle', 'Gabrielle.simon@orange.fr', '0667890123', '1993-06-21', '2025-09-26 10:45:00', '30 Rue Saint-Jacques, 44000 Nantes');
-- Mise à jour des mots de passe : ajoute un mot de passe haché par défaut pour tous les clients qui n'en ont pas encore
UPDATE Client
SET mot_de_passe = '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2'
WHERE mot_de_passe IS NULL;

-- Insertions Maintenance 
INSERT INTO Maintenance (id_maintenance, type_intervention, description, priorite, statut, date_prevue, date_realisation) VALUES
(801, 'Climatisation', 'Vérification du système', 2, 'en_cours', '2025-09-28 09:00:00', NULL),
(802, 'Plomberie', 'Fuite robinet salle de bain', 1, 'terminee', '2025-09-26 10:00:00', '2025-09-26 15:30:00'),
(803, 'Electricité', 'Remplacement des ampoules', 4, 'planifiee', '2025-10-01 14:00:00', NULL),
(804, 'Peinture', 'Retouche murs', 3, 'planifiee', '2025-10-02 08:00:00', NULL),
(805, 'Plomberie', 'Débouchage canalisation', 1, 'terminee', '2025-09-25 11:00:00', '2025-09-25 16:00:00'),
(806, 'Climatisation', 'Changement filtres', 3, 'en_cours', '2025-09-28 10:00:00', NULL);

-- Insertions Type_Chambre 
INSERT INTO Type_Chambre (id_type, nom, capacite_max, surface_type, description) VALUES
(201, 'Suite', 4, 30.00, 'Suite spacieuse avec vue sur mer'),
(202, 'Double', 2, 20.00, 'Chambre double standard'),
(203, 'Simple', 1, 15.00, 'Chambre simple confortable'),
(204, 'Familiale', 5, 40.00, 'Chambre familiale avec deux lits doubles'),
(205, 'Deluxe', 2, 35.00, 'Chambre de luxe avec balcon privé');

INSERT INTO Chambre (id_chambre, statut, etage, prix_base, superficie, id_maintenance, id_type) VALUES
(201, 'libre', 2, 120.00, 25.50, 801, 201),
(202, 'occupée', 1, 90.00, 20.00, 802, 202),
(203, 'maintenance', 3, 150.00, 30.00, 803, 205),
(204, 'libre', 1, 85.00, 18.00, 804, 203),
(205, 'libre', 3, 180.00, 35.00, 805, 201),
(206, 'occupée', 1, 95.00, 22.00, 806, 202),
(207, 'nettoyage', 3, 130.00, 28.00, 801, 204),
(208, 'libre', 2, 110.00, 24.00, 802, 205);

-- Insertions Service
INSERT INTO Service (id_service, nom, description, prix_unitaire, actif, categorie) VALUES
(301, 'Massage', 'Massage relaxant d''une heure', 60.00, true, 'wellness'),
(302, 'Petit-déjeuner', 'Service en chambre', 15.00, true, 'restauration'),
(303, 'Navette aéroport', 'Transport aller-retour', 45.00, true, 'transport'),
(304, 'Spa', 'Accès spa et jacuzzi', 35.00, true, 'wellness'),
(305, 'Dîner gastronomique', 'Menu 3 plats au restaurant', 55.00, true, 'restauration'),
(306, 'Blanchisserie', 'Service pressing 24h', 20.00, true, 'autre'),
(307, 'Location vélo', 'Vélo pour la journée', 12.00, true, 'transport');

-- Insertions Reservation 
INSERT INTO Reservation (date_debut, date_fin, nombre_personne, statut, id_client, id_chambre) VALUES
('2025-10-01', '2025-10-05', 3, 'confirmée', 101, 201),
('2025-10-03', '2025-10-07', 2, 'en_attente', 102, 202),
('2025-10-10', '2025-10-15', 1, 'confirmée', 103, 204),
('2025-10-12', '2025-10-18', 4, 'confirmée', 104, 205),
('2025-10-05', '2025-10-08', 2, 'annulee', 105, 208),
('2025-10-20', '2025-10-25', 2, 'confirmée', 106, 206),
('2025-10-15', '2025-10-20', 3, 'en_attente', 107, 207);
-- Insertions Paiement 
INSERT INTO Paiement (id_paiement, date_paiement, numero_transaction, mode_paiement) VALUES
(601, '2025-09-22 15:00:00', 'TRX987654321', 'carte'),
(602, '2025-10-10 16:30:00', 'TRX123456789', 'virement'),
(603, '2025-10-12 14:20:00', 'TRX456789123', 'carte'),
(604, '2025-10-08 11:45:00', 'TRX789123456', 'cheque'),
(605, '2025-10-20 18:00:00', 'TRX321654987', 'virement');
-- Insertions Facture
INSERT INTO Facture (id_facture, date_emission, montant_total, tva, statut_paiement, id_reservation, id_paiement) VALUES
(501, '2025-09-22', 350.50, 70.00, 'payee', 1, 601),
(502, '2025-09-25', 180.00, 36.00, 'impayee', 2, NULL),
(503, '2025-10-10', 125.00, 25.00, 'payee', 3, 602),
(504, '2025-10-12', 890.00, 178.00, 'partiel', 4, 603),
(505, '2025-10-08', 50.00, 10.00, 'remboursee', 5, 604),
(506, '2025-10-20', 525.00, 105.00, 'payee', 6, 605); 

-- Insertions Employe
INSERT INTO Employe (id_employe, nom, prenom, poste, salaire, date_embauche, actif, id_maintenance, mail_pro, mot_de_passe, role) VALUES
(601, 'Martin', 'Lucas', 'Receptionniste', 1800.00, '2023-02-01', true, NULL, 'Lucas.martin@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'RECEPTION'),
(602, 'Leroy', 'Sophie', 'Femme de menage', 1600.00, '2024-01-15', true, NULL, 'Sophie.leroy@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'STAFF'),
(603, 'Dubois', 'Pierre', 'Chef cuisinier', 2500.00, '2022-06-10', true, NULL, 'Pierre.dubois@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'STAFF'),
(604, 'Moreau', 'Julie', 'Receptionniste', 1850.00, '2023-09-20', true, NULL, 'Julie.moreau@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'RECEPTION'),
(605, 'Lambert', 'Thomas', 'Technicien', 2100.00, '2021-11-05', true, 805, 'Thomas.lambert@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'MAINTENANCE'),
(606, 'Garnier', 'Marie', 'Femme de ménage', 1650.00, '2024-03-12', true, NULL, 'Marie.garnier@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'STAFF'),
(607, 'Roux', 'Antoine', 'Serveur', 1750.00, '2023-07-18', true, NULL, 'Antoine.roux@gmail.com', '$2y$10$uNwYPXN6uCQz7XrZ8Q0Q.eTgPwRG0WJY3xJvbxmuCoKfYnlbpvZf2', 'STAFF');
-- AJOUT: Insertions Consomme 
INSERT INTO Consomme (id_reservation, id_service, type_consommation) VALUES
(1, 301, 'Massage relaxant'),
(2, 302, 'Petit-déjeuner en chambre'),
(3, 303, 'Navette aéroport'),
(4, 307, 'Location vélo journée'),
(5, 304, 'Accès spa et jacuzzi'),
(6, 305, 'Menu dégustation');

