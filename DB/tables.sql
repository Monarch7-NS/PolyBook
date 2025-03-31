-- Supprimer les tables si elles existent déjà (pour éviter les erreurs)
DROP TABLE IF EXISTS statistiques, amis, membre_cercle, cercle, utilisateur_livre, avis, livre, utilisateur;

-- Création de la table des utilisateurs
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    gmail VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    genre_prefere VARCHAR(100)
);

-- Création de la table des livres
CREATE TABLE livre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    genre VARCHAR(100),
    date_publicite DATE,
    couverture VARCHAR(255)
);

-- Création de la table des avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    livre_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (livre_id) REFERENCES livre(id) ON DELETE CASCADE
);

-- Création de la table pour gérer la relation utilisateur-livre avec le statut
CREATE TABLE utilisateur_livre (
    utilisateur_id INT,
    livre_id INT,
    statut ENUM('lu', 'a_lire', 'en_cours') NOT NULL,
    PRIMARY KEY (utilisateur_id, livre_id, statut),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (livre_id) REFERENCES livre(id) ON DELETE CASCADE
);

-- Création de la table des cercles d'amis
CREATE TABLE cercle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table pour gérer les membres des cercles
CREATE TABLE membre_cercle (
    utilisateur_id INT,
    cercle_id INT,
    PRIMARY KEY (utilisateur_id, cercle_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (cercle_id) REFERENCES cercle(id) ON DELETE CASCADE
);

-- Création de la table des relations d'amitié
CREATE TABLE amis (
    utilisateur1_id INT,
    utilisateur2_id INT,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur1_id, utilisateur2_id),
    FOREIGN KEY (utilisateur1_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur2_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Création de la table des statistiques des livres
CREATE TABLE statistiques (
    livre_id INT PRIMARY KEY,
    nombre_avis INT DEFAULT 0,
    moyenne_note FLOAT DEFAULT 0,
    FOREIGN KEY (livre_id) REFERENCES livre(id) ON DELETE CASCADE
);

-- Insertion des utilisateurs
INSERT INTO utilisateur (nom, prenom, gmail, mot_de_passe, genre_prefere) VALUES
('Doe', 'John', 'john.doe@example.com', 'password123', 'Science-fiction'),
('Smith', 'Alice', 'alice.smith@example.com', 'password456', 'Fantasy'),
('Martin', 'Paul', 'paul.martin@example.com', 'password789', 'Thriller');

-- Insertion des livres
INSERT INTO livre (title, author, description, genre, date_publicite, couverture) VALUES
('Dune', 'Frank Herbert', 'Un classique de la science-fiction', 'Science-fiction', '1965-08-01', 'dune.jpg'),
('Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'Une épopée fantasy incontournable', 'Fantasy', '1954-07-29', 'lotr.jpg'),
('Sherlock Holmes', 'Arthur Conan Doyle', 'Aventures du célèbre détective', 'Thriller', '1892-10-14', 'sherlock.jpg');

-- Insertion des avis
INSERT INTO avis (utilisateur_id, livre_id, rating, comment) VALUES
(1, 1, 5, 'Un chef-d\'œuvre intemporel !'),
(2, 2, 4, 'Un univers riche et immersif.'),
(3, 3, 5, 'Sherlock est vraiment fascinant.');

-- Insertion des statuts de lecture des utilisateurs
INSERT INTO utilisateur_livre (utilisateur_id, livre_id, statut) VALUES
(1, 1, 'lu'),
(2, 2, 'en_cours'),
(3, 3, 'a_lire');

-- Insertion d’un cercle de lecture
INSERT INTO cercle (nom) VALUES ('Club de lecture SF');

-- Ajout des membres au cercle
INSERT INTO membre_cercle (utilisateur_id, cercle_id) VALUES
(1, 1),
(2, 1);

-- Ajout d’amis
INSERT INTO amis (utilisateur1_id, utilisateur2_id) VALUES
(1, 2),
(2, 3);

-- Mise à jour des statistiques des livres
INSERT INTO statistiques (livre_id, nombre_avis, moyenne_note) VALUES
(1, 1, 5.0),
(2, 1, 4.0),
(3, 1, 5.0);

-- Vérification des données insérées
SELECT * FROM utilisateur;
SELECT * FROM livre;
SELECT * FROM avis;
SELECT * FROM utilisateur_livre;
SELECT * FROM cercle;
SELECT * FROM membre_cercle;
SELECT * FROM amis;
SELECT * FROM statistiques;
