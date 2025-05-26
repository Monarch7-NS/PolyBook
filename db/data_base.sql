-- PolyBook Database Schema avec AUTO_INCREMENT
-- Ce script met à jour la base de données pour ajouter AUTO_INCREMENT aux clés primaires

-- Supprimer la base de données existante et la recréer
DROP DATABASE IF EXISTS polybook;
CREATE DATABASE polybook;
USE polybook;

-- TABLE UTILISATEUR
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    username TEXT,
    email TEXT,
    password TEXT,
    role TEXT,
    grade_id INT,
    resume_id INT
);

-- TABLE GRADE
CREATE TABLE grade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    name TEXT
);

-- TABLE RESUME
CREATE TABLE resume (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id INT,
    content TEXT,
    activity_score DECIMAL
);

-- TABLE BOOK
CREATE TABLE book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    title TEXT,
    author TEXT,
    isbn TEXT,
    publication_year INT,
    description TEXT,
    language TEXT,
    added_by_user_id INT,
    is_approved BOOLEAN DEFAULT FALSE
);

-- TABLE REVIEW
CREATE TABLE review (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id INT,
    book_id INT,
    rating DECIMAL,
    content TEXT,
    contains_spoiler BOOLEAN DEFAULT FALSE,
    tags TEXT
);

-- TABLE COMMENT
CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id INT,
    review_id INT,
    book_id INT,
    content TEXT,
    comment_id INT
);

-- TABLE GENRE
CREATE TABLE genre (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    name TEXT
);

-- TABLE USER-GENRE
CREATE TABLE user_genre (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id INT,
    genre_id INT
);

-- TABLE FRIENDSHIP
CREATE TABLE friendship (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id1 INT,
    user_id2 INT,
    status TEXT
);

-- TABLE CIRCLE
CREATE TABLE circle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    name TEXT,
    owner_user_id INT
);

-- TABLE CIRCLE-MEMBER
CREATE TABLE circle_member (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    circle_id INT,
    user_id INT
);

-- TABLE FAVORITE BOOK
CREATE TABLE favorite_book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    user_id INT,
    book_id INT
);

-- TABLE REPORT
CREATE TABLE report (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    reported_by_user_id INT,
    reported_user_id INT,
    reported_review_id INT,
    reported_comment_id INT,
    reason TEXT,
    status TEXT
);

-- TABLE READING LIST
CREATE TABLE reading_list (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    name TEXT,
    user_id INT,
    is_public BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- TABLE READING LIST BOOK
CREATE TABLE reading_list_book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reading_list_id INT,
    book_id INT,
    added_at DATE,
    FOREIGN KEY (reading_list_id) REFERENCES reading_list(id),
    FOREIGN KEY (book_id) REFERENCES book(id)
);

-- TABLE READING GROUP
CREATE TABLE reading_group (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    name TEXT,
    description TEXT,
    creator_user_id INT
);

-- TABLE READING GROUP MEMBER
CREATE TABLE reading_group_member (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reading_group_id INT,
    user_id INT,
    joined_at DATE
);

-- TABLE READING GROUP BOOK
CREATE TABLE reading_group_book (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reading_group_id INT,
    book_id INT,
    start_date DATE,
    end_date DATE
);

-- TABLE BOOK GENRE
CREATE TABLE book_genre (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT,
    genre_id INT,
    FOREIGN KEY (book_id) REFERENCES book(id),
    FOREIGN KEY (genre_id) REFERENCES genre(id)
);

-- TABLE CIRCLE INVITATION (ajout manquant)
CREATE TABLE circle_invitation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATE,
    circle_id INT,
    inviter_user_id INT,
    invited_user_id INT,
    status VARCHAR(20) DEFAULT 'pending',
    FOREIGN KEY (circle_id) REFERENCES circle(id),
    FOREIGN KEY (inviter_user_id) REFERENCES user(id),
    FOREIGN KEY (invited_user_id) REFERENCES user(id)
);

-- TABLE DISCUSSION (ajout pour les groupes de lecture)
CREATE TABLE discussion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME,
    reading_group_id INT,
    user_id INT,
    title VARCHAR(255),
    content TEXT,
    FOREIGN KEY (reading_group_id) REFERENCES reading_group(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- TABLE DISCUSSION REPLY
CREATE TABLE discussion_reply (
    id INT PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME,
    discussion_id INT,
    user_id INT,
    content TEXT,
    FOREIGN KEY (discussion_id) REFERENCES discussion(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

-- Insertion des données initiales

-- GRADES
INSERT INTO grade (created_at, name) VALUES 
('2024-01-01', 'Étudiant'),
('2024-01-01', 'Professeur');

-- USERS (avec des mots de passe sécurisés pour la production)
INSERT INTO user (created_at, username, email, password, role, grade_id, resume_id) VALUES 
('2025-01-01', 'alice', 'alice@example.com', 'hashed_pwd_1', 'user', 1, 1),
('2025-01-02', 'bob', 'bob@example.com', 'hashed_pwd_2', 'admin', 2, 2),
('2025-01-03', 'charlie', 'charlie@example.com', 'hashed_pwd_3', 'user', 1, 3),
('2025-01-04', 'diana', 'diana@example.com', 'hashed_pwd_4', 'user', 2, 4),
('2025-01-05', 'eve', 'eve@example.com', 'hashed_pwd_5', 'user', 1, 5);

-- RESUMES
INSERT INTO resume (created_at, user_id, content, activity_score) VALUES 
('2025-01-01', 1, 'Passionnée de lecture, j\'adore découvrir de nouveaux auteurs.', 88.5),
('2025-01-02', 2, 'Professeur de lettres modernes, spécialiste de la littérature contemporaine.', 92.0),
('2025-01-03', 3, 'Étudiant en informatique avec une passion pour la science-fiction.', 75.0),
('2025-01-04', 4, 'Professeure de philosophie, intéressée par les essais et la littérature classique.', 85.5),
('2025-01-05', 5, 'Amatrice de romans policiers et de thrillers psychologiques.', 80.0);

-- BOOKS (avec plus de livres)
INSERT INTO book (created_at, title, author, isbn, publication_year, description, language, added_by_user_id, is_approved) VALUES 
('2025-02-01', '1984', 'George Orwell', '9780451524935', 1949, 'Dans un monde totalitaire, Winston Smith lutte contre Big Brother et le Parti qui contrôle tout, y compris le passé et la pensée.', 'français', 1, TRUE),
('2025-02-03', 'Le Petit Prince', 'Antoine de Saint-Exupéry', '9780156013987', 1943, 'Un conte poétique et philosophique racontant la rencontre entre un aviateur et un petit prince venu d\'une autre planète.', 'français', 2, TRUE),
('2025-02-05', 'Harry Potter à l\'école des sorciers', 'J.K. Rowling', '9782070584628', 1997, 'Harry découvre qu\'il est un sorcier et entre à Poudlard, l\'école de sorcellerie.', 'français', 1, TRUE),
('2025-02-07', 'Le Seigneur des Anneaux', 'J.R.R. Tolkien', '9782266286268', 1954, 'L\'épopée de Frodon et de la Communauté de l\'Anneau pour détruire l\'Anneau Unique.', 'français', 3, TRUE),
('2025-02-09', 'Dune', 'Frank Herbert', '9782266320481', 1965, 'Sur la planète désertique Arrakis, Paul Atréides doit accomplir son destin.', 'français', 3, TRUE),
('2025-02-11', 'Les Misérables', 'Victor Hugo', '9782253096339', 1862, 'L\'histoire de Jean Valjean et de sa rédemption dans la France du XIXe siècle.', 'français', 2, TRUE),
('2025-02-13', 'L\'Étranger', 'Albert Camus', '9782070360024', 1942, 'Meursault, un homme indifférent, commet un meurtre sous le soleil d\'Alger.', 'français', 4, TRUE),
('2025-02-15', 'Da Vinci Code', 'Dan Brown', '9782709624930', 2003, 'Robert Langdon doit résoudre une série d\'énigmes liées aux œuvres de Léonard de Vinci.', 'français', 5, TRUE),
('2025-02-17', 'Millennium : Les hommes qui n\'aimaient pas les femmes', 'Stieg Larsson', '9782742777785', 2005, 'Le journaliste Mikael Blomkvist et la hackeuse Lisbeth Salander enquêtent sur une disparition.', 'français', 5, TRUE),
('2025-02-19', 'Sapiens', 'Yuval Noah Harari', '9782226257017', 2011, 'Une brève histoire de l\'humanité, de l\'âge de pierre à l\'ère de la Silicon Valley.', 'français', 4, TRUE);

-- REVIEWS avec plus d'avis
INSERT INTO review (created_at, user_id, book_id, rating, content, contains_spoiler, tags) VALUES 
('2025-03-01', 1, 1, 4.5, 'Un roman bouleversant sur les dangers du totalitarisme. La surveillance permanente décrite par Orwell est d\'une actualité troublante.', FALSE, 'dystopie,société,politique'),
('2025-03-02', 2, 2, 5.0, 'Un chef-d\'œuvre intemporel ! Chaque relecture révèle de nouvelles dimensions philosophiques.', FALSE, 'philosophie,poésie,classique'),
('2025-03-03', 3, 5, 5.0, 'Dune est le sommet de la science-fiction. L\'univers créé par Herbert est d\'une richesse incroyable.', FALSE, 'science-fiction,épique,écologie'),
('2025-03-04', 1, 3, 4.0, 'Une excellente introduction à l\'univers de Harry Potter. On s\'attache rapidement aux personnages.', FALSE, 'fantastique,jeunesse,magie'),
('2025-03-05', 4, 7, 5.0, 'L\'Étranger reste une œuvre fondamentale pour comprendre l\'existentialisme. L\'écriture de Camus est d\'une précision chirurgicale.', FALSE, 'philosophie,existentialisme,classique'),
('2025-03-06', 5, 8, 3.5, 'Un thriller haletant mais parfois un peu tiré par les cheveux. Reste très divertissant.', TRUE, 'thriller,mystère,histoire'),
('2025-03-07', 5, 9, 4.5, 'Excellente intrigue nordique. Les personnages de Blomkvist et Salander sont fascinants.', FALSE, 'polar,thriller,nordique'),
('2025-03-08', 2, 6, 5.0, 'Un monument de la littérature française. Hugo dépeint magistralement la misère et la grandeur humaine.', FALSE, 'classique,historique,social'),
('2025-03-09', 3, 4, 4.5, 'Une épopée fantastique grandiose. La Terre du Milieu est un univers d\'une richesse incomparable.', FALSE, 'fantastique,épique,aventure'),
('2025-03-10', 4, 10, 4.0, 'Une analyse fascinante de l\'évolution humaine. Harari réussit à vulgariser sans simplifier.', FALSE, 'histoire,science,société');

-- GENRES supplémentaires
INSERT INTO genre (created_at, name) VALUES
('2024-01-01', 'Science-fiction'),
('2024-01-01', 'Classique'),
('2024-01-01', 'Fantastique'),
('2024-01-01', 'Mystère'),
('2024-01-01', 'Romance'),
('2024-01-01', 'Thriller'),
('2024-01-01', 'Horreur'),
('2024-01-01', 'Fiction historique'),
('2024-01-01', 'Non-fiction'),
('2024-01-01', 'Biographie'),
('2024-01-01', 'Développement personnel'),
('2024-01-01', 'Littérature jeunesse'),
('2024-01-01', 'Dystopie'),
('2024-01-01', 'Aventure'),
('2024-01-01', 'Poésie'),
('2024-01-01', 'Roman graphique'),
('2024-01-01', 'Livre de cuisine'),
('2024-01-01', 'Science'),
('2024-01-01', 'Philosophie'),
('2024-01-01', 'Religion'),
('2024-01-01', 'Voyage'),
('2024-01-01', 'Humour'),
('2024-01-01', 'Essai'),
('2024-01-01', 'Drame'),
('2024-01-01', 'Policier'),
('2024-01-01', 'Technologie'),
('2024-01-01', 'Économie'),
('2024-01-01', 'Art'),
('2024-01-01', 'Autobiographie'),
('2024-01-01', 'Santé et bien-être'),
('2024-01-01', 'Spiritualité'),
('2024-01-01', 'Écologie'),
('2024-01-01', 'Psychologie'),
('2024-01-01', 'Sociologie'),
('2024-01-01', 'Science politique'),
('2024-01-01', 'Anthropologie'),
('2024-01-01', 'Management'),
('2024-01-01', 'Développement web'),
('2024-01-01', 'Informatique'),
('2024-01-01', 'Mathématiques'),
('2024-01-01', 'Physique'),
('2024-01-01', 'Chimie'),
('2024-01-01', 'Astronomie'),
('2024-01-01', 'Cuisine du monde'),
('2024-01-01', 'Sport'),
('2024-01-01', 'Mode'),
('2024-01-01', 'Photographie'),
('2024-01-01', 'Cinéma'),
('2024-01-01', 'Musique');

-- BOOK GENRES
INSERT INTO book_genre (book_id, genre_id) VALUES 
(1, 13), (1, 1), (1, 35),  -- 1984: Dystopie, Science-fiction, Science politique
(2, 2), (2, 15), (2, 19),  -- Petit Prince: Classique, Poésie, Philosophie
(3, 3), (3, 12), (3, 14),  -- Harry Potter: Fantastique, Jeunesse, Aventure
(4, 3), (4, 14), (4, 2),   -- LOTR: Fantastique, Aventure, Classique
(5, 1), (5, 14), (5, 32),  -- Dune: Science-fiction, Aventure, Écologie
(6, 2), (6, 8), (6, 24),   -- Misérables: Classique, Historique, Drame
(7, 2), (7, 19), (7, 23),  -- L'Étranger: Classique, Philosophie, Essai
(8, 6), (8, 4), (8, 8),    -- Da Vinci Code: Thriller, Mystère, Historique
(9, 6), (9, 25), (9, 4),   -- Millennium: Thriller, Policier, Mystère
(10, 9), (10, 18), (10, 36); -- Sapiens: Non-fiction, Science, Anthropologie

-- USER GENRES
INSERT INTO user_genre (created_at, user_id, genre_id) VALUES 
('2025-04-01', 1, 1), ('2025-04-01', 1, 13), ('2025-04-01', 1, 3),
('2025-04-01', 2, 2), ('2025-04-01', 2, 19), ('2025-04-01', 2, 23),
('2025-04-01', 3, 1), ('2025-04-01', 3, 3), ('2025-04-01', 3, 14),
('2025-04-01', 4, 19), ('2025-04-01', 4, 2), ('2025-04-01', 4, 23),
('2025-04-01', 5, 6), ('2025-04-01', 5, 25), ('2025-04-01', 5, 4);

-- FRIENDSHIPS
INSERT INTO friendship (created_at, user_id1, user_id2, status) VALUES 
('2025-04-01', 1, 2, 'accepted'),
('2025-04-02', 1, 3, 'accepted'),
('2025-04-03', 2, 4, 'accepted'),
('2025-04-04', 3, 5, 'pending'),
('2025-04-05', 4, 5, 'accepted');

-- CIRCLES
INSERT INTO circle (created_at, name, owner_user_id) VALUES 
('2025-04-01', 'Cercle Littéraire Classique', 1),
('2025-04-02', 'Club Science-Fiction', 3),
('2025-04-03', 'Philosophes en herbe', 4);

-- CIRCLE MEMBERS
INSERT INTO circle_member (created_at, circle_id, user_id) VALUES 
('2025-04-01', 1, 1), ('2025-04-01', 1, 2), ('2025-04-01', 1, 4),
('2025-04-02', 2, 3), ('2025-04-02', 2, 1), ('2025-04-02', 2, 5),
('2025-04-03', 3, 4), ('2025-04-03', 3, 2);

-- FAVORITE BOOKS
INSERT INTO favorite_book (created_at, user_id, book_id) VALUES 
('2025-04-01', 1, 2), ('2025-04-01', 1, 1), ('2025-04-01', 1, 3),
('2025-04-01', 2, 2), ('2025-04-01', 2, 6),
('2025-04-01', 3, 5), ('2025-04-01', 3, 4),
('2025-04-01', 4, 7), ('2025-04-01', 4, 10),
('2025-04-01', 5, 8), ('2025-04-01', 5, 9);

-- COMMENTS
INSERT INTO comment (created_at, user_id, review_id, book_id, content, comment_id) VALUES
('2025-04-02', 2, 1, 1, 'Entièrement d\'accord avec ta critique. Ce roman est plus pertinent que jamais.', NULL),
('2025-04-03', 3, 1, 1, 'J\'ajouterais que la novlangue décrite par Orwell préfigure étrangement notre époque de communication simplifiée.', NULL),
('2025-04-04', 1, 5, 7, 'Excellente analyse ! L\'absurde camusien est effectivement magistralement illustré.', NULL),
('2025-04-05', 5, 3, 5, 'Dune est effectivement un chef-d\'œuvre. As-tu lu les suites ?', NULL);

-- READING LISTS
INSERT INTO reading_list (created_at, name, user_id, is_public) VALUES 
('2025-04-01', 'À lire absolument', 1, TRUE),
('2025-04-01', 'Mes classiques favoris', 2, TRUE),
('2025-04-02', 'Science-Fiction épique', 3, TRUE),
('2025-04-03', 'Lectures philosophiques', 4, FALSE),
('2025-04-04', 'Thrillers à suspense', 5, TRUE);

-- READING LIST BOOKS
INSERT INTO reading_list_book (reading_list_id, book_id, added_at) VALUES 
(1, 2, '2025-04-01'), (1, 5, '2025-04-02'), (1, 10, '2025-04-03'),
(2, 2, '2025-04-01'), (2, 6, '2025-04-01'), (2, 7, '2025-04-02'),
(3, 4, '2025-04-02'), (3, 5, '2025-04-02'), (3, 1, '2025-04-03'),
(4, 7, '2025-04-03'), (4, 10, '2025-04-03'), (4, 2, '2025-04-04'),
(5, 8, '2025-04-04'), (5, 9, '2025-04-04');

-- READING GROUPS
INSERT INTO reading_group (created_at, name, description, creator_user_id) VALUES 
('2025-04-01', 'Club Orwell', 'Lecture et discussion de 1984 et des œuvres d\'Orwell', 1),
('2025-04-02', 'Voyageurs de Dune', 'Exploration de l\'univers de Frank Herbert', 3),
('2025-04-03', 'Cercle Hugo', 'Découverte des classiques de Victor Hugo', 2);

-- READING GROUP MEMBERS
INSERT INTO reading_group_member (reading_group_id, user_id, joined_at) VALUES 
(1, 1, '2025-04-01'), (1, 2, '2025-04-02'), (1, 4, '2025-04-03'),
(2, 3, '2025-04-02'), (2, 1, '2025-04-03'), (2, 5, '2025-04-04'),
(3, 2, '2025-04-03'), (3, 4, '2025-04-04');

-- READING GROUP BOOKS
INSERT INTO reading_group_book (reading_group_id, book_id, start_date, end_date) VALUES 
(1, 1, '2025-04-01', '2025-04-30'),
(2, 5, '2025-05-01', '2025-05-31'),
(3, 6, '2025-04-15', '2025-05-15');

-- REPORTS (exemples)
INSERT INTO report (created_at, reported_by_user_id, reported_user_id, reported_review_id, reported_comment_id, reason, status) VALUES 
('2025-04-03', 2, 1, NULL, NULL, 'Comportement inapproprié dans les discussions', 'pending');

-- CIRCLE INVITATIONS
INSERT INTO circle_invitation (created_at, circle_id, inviter_user_id, invited_user_id, status) VALUES
('2025-04-05', 1, 1, 5, 'pending'),
('2025-04-06', 2, 3, 2, 'accepted');

-- Procédure pour assigner des genres aléatoires aux livres (optionnel)
DELIMITER //

CREATE PROCEDURE AssignRandomGenresToBooks()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE bookId INT;
    DECLARE genreCount INT;
    DECLARE genreId INT;
    DECLARE alreadyExists INT;

    -- Curseur pour parcourir tous les livres qui n'ont pas encore de genres
    DECLARE bookCursor CURSOR FOR 
        SELECT b.id FROM book b 
        WHERE NOT EXISTS (SELECT 1 FROM book_genre bg WHERE bg.book_id = b.id);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN bookCursor;

    read_loop: LOOP
        FETCH bookCursor INTO bookId;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Générer un nombre aléatoire de genres à assigner (1 à 3)
        SET genreCount = FLOOR(1 + RAND() * 3);

        -- Boucle pour assigner le nombre de genres aléatoires
        WHILE genreCount > 0 DO
            -- Sélectionner un genre aléatoire
            SET genreId = FLOOR(1 + RAND() * 49);
            
            -- Vérifier si cette combinaison existe déjà
            SELECT COUNT(*) INTO alreadyExists FROM book_genre WHERE book_id = bookId AND genre_id = genreId;
            
            -- Insérer seulement si la combinaison n'existe pas
            IF alreadyExists = 0 THEN
                INSERT INTO book_genre (book_id, genre_id) VALUES (bookId, genreId);
            END IF;

            SET genreCount = genreCount - 1;
        END WHILE;
    END LOOP;

    CLOSE bookCursor;
END //

DELIMITER ;