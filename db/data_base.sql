-- PolyBook Database Schema
-- This SQL script creates the database schema for the PolyBook application.
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
    id INT PRIMARY KEY,
    created_at DATE,
    name TEXT
);

-- TABLE RESUME
CREATE TABLE resume (
    id INT PRIMARY KEY,
    created_at DATE,
    user_id INT,
    content TEXT,
    activity_score DECIMAL
);

-- TABLE BOOK (ajout language + is_approved)
CREATE TABLE book (
    id INT PRIMARY KEY,
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

-- TABLE REVIEW (tags + spoilers)
CREATE TABLE review (
    id INT PRIMARY KEY,
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
    id INT PRIMARY KEY,
    created_at DATE,
    user_id INT,
    review_id INT,
    book_id INT,
    content TEXT,
    comment_id INT
);

-- TABLE GENRE
CREATE TABLE genre (
    id INT PRIMARY KEY,
    created_at DATE,
    name TEXT
);

-- TABLE USER-GENRE
CREATE TABLE user_genre (
    id INT PRIMARY KEY,
    created_at DATE,
    user_id INT,
    genre_id INT
);

-- TABLE FRIENDSHIP
CREATE TABLE friendship (
    id INT PRIMARY KEY,
    created_at DATE,
    user_id1 INT,
    user_id2 INT,
    status TEXT
);

-- TABLE CIRCLE
CREATE TABLE circle (
    id INT PRIMARY KEY,
    created_at DATE,
    name TEXT,
    owner_user_id INT
);

-- TABLE CIRCLE-MEMBER
CREATE TABLE circle_member (
    id INT PRIMARY KEY,
    created_at DATE,
    circle_id INT,
    user_id INT
);

-- TABLE FAVORITE BOOK
CREATE TABLE favorite_book (
    id INT PRIMARY KEY,
    created_at DATE,
    user_id INT,
    book_id INT
);

-- TABLE REPORT
CREATE TABLE report (
    id INT PRIMARY KEY,
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
    id INT PRIMARY KEY,
    created_at DATE,
    name TEXT,
    description TEXT,
    creator_user_id INT
);

-- TABLE READING GROUP MEMBER
CREATE TABLE reading_group_member (
    id INT PRIMARY KEY,
    reading_group_id INT,
    user_id INT,
    joined_at DATE
);

-- TABLE READING GROUP BOOK
CREATE TABLE reading_group_book (
    id INT PRIMARY KEY,
    reading_group_id INT,
    book_id INT,
    start_date DATE,
    end_date DATE
);

CREATE TABLE book_genre (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT,
    genre_id INT,
    FOREIGN KEY (book_id) REFERENCES book(id),
    FOREIGN KEY (genre_id) REFERENCES genre(id)
);

-- GRADES
INSERT INTO grade VALUES (1, '2024-01-01', 'Étudiant');
INSERT INTO grade VALUES (2, '2024-01-01', 'Professeur');

-- USERS
INSERT INTO user VALUES (1, '2025-01-01', 'alice', 'alice@example.com', 'hashed_pwd_1', 'user', 1, 1);
INSERT INTO user VALUES (2, '2025-01-02', 'bob', 'bob@example.com', 'hashed_pwd_2', 'admin', 2, 2);

-- RESUMES
INSERT INTO resume VALUES (1, '2025-01-01', 1, 'Passionnée de lecture.', 88.5);
INSERT INTO resume VALUES (2, '2025-01-02', 2, 'Prof de lettres modernes.', 92.0);

-- BOOKS
INSERT INTO book VALUES 
(1, '2025-02-01', '1984', 'George Orwell', '9780451524935', 1949, 'Dystopie sur une société totalitaire.', 'français', 1, TRUE),
(2, '2025-02-03', 'Le Petit Prince', 'Antoine de Saint-Exupéry', '9780156013987', 1943, 'Conte poétique et philosophique.', 'français', 2, TRUE);

-- REVIEWS
INSERT INTO review VALUES 
(1, '2025-03-01', 1, 1, 4.5, 'Un roman bouleversant.', FALSE, 'dystopie,société'),
(2, '2025-03-02', 2, 2, 5.0, 'À lire absolument !', FALSE, 'philosophie,poésie');

-- READING LISTS
INSERT INTO reading_list VALUES 
(1, '2025-04-01', 'À lire', 1, FALSE),
(2, '2025-04-01', 'Favoris', 2, TRUE);

-- READING LIST BOOKS
INSERT INTO reading_list_book VALUES 
(1, 1, 2, '2025-04-02'),
(2, 2, 1, '2025-04-02');

-- GENRES
INSERT INTO genre VALUES 
(1, '2024-01-01', 'Science-fiction'),
(2, '2024-01-01', 'Classique');

-- USER GENRES
INSERT INTO user_genre VALUES 
(1, '2025-04-01', 1, 1),
(2, '2025-04-01', 2, 2);

-- FRIENDSHIP
INSERT INTO friendship VALUES (1, '2025-04-01', 1, 2, 'accepted');

-- CIRCLE
INSERT INTO circle VALUES (1, '2025-04-01', 'Cercle Littéraire', 1);

-- CIRCLE MEMBER
INSERT INTO circle_member VALUES 
(1, '2025-04-01', 1, 1),
(2, '2025-04-01', 1, 2);

-- FAVORITE BOOK
INSERT INTO favorite_book VALUES 
(1, '2025-04-01', 1, 2),
(2, '2025-04-01', 2, 1);

-- COMMENTS
INSERT INTO comment VALUES
(1, '2025-04-02', 2, 1, 1, 'Entièrement d\'accord avec ta critique.', NULL);

-- READING GROUP
INSERT INTO reading_group VALUES 
(1, '2025-04-01', 'Club Orwell', 'Lecture de 1984 en avril.', 1);

-- READING GROUP MEMBER
INSERT INTO reading_group_member VALUES 
(1, 1, 1, '2025-04-01'),
(2, 1, 2, '2025-04-02');

-- READING GROUP BOOK
INSERT INTO reading_group_book VALUES 
(1, 1, 1, '2025-04-01', '2025-04-30');

-- REPORT
INSERT INTO report VALUES 
(1, '2025-04-03', 2, 1, NULL, NULL, 'Comportement inapproprié', 'en cours');

INSERT INTO book_genre (book_id, genre_id) 
VALUES (1, 1);

INSERT INTO book_genre (book_id, genre_id) 
VALUES (2, 2);

INSERT INTO genre (id, name) VALUES
(1, 'Science-fiction'),
(2, 'Classique'),
(3, 'Fantastique'),
(4, 'Mystère'),
(5, 'Romance'),
(6, 'Thriller'),
(7, 'Horreur'),
(8, 'Fiction historique'),
(9, 'Non-fiction'),
(10, 'Biographie'),
(11, 'Développement personnel'),
(12, 'Littérature jeunesse'),
(13, 'Dystopie'),
(14, 'Aventure'),
(15, 'Poésie'),
(16, 'Roman graphique'),
(17, 'Livre de cuisine'),
(18, 'Science'),
(19, 'Philosophie'),
(20, 'Religion'),
(21, 'Voyage'),
(22, 'Humour'),
(23, 'Essai'),
(24, 'Drame'),
(25, 'Policier'),
(26, 'Technologie'),
(27, 'Économie'),
(28, 'Art'),
(29, 'Autobiographie'),
(30, 'Santé et bien-être'),
(31, 'Spiritualité'),
(32, 'Écologie'),
(33, 'Psychologie'),
(34, 'Sociologie'),
(35, 'Science politique'),
(36, 'Anthropologie'),
(37, 'Management'),
(38, 'Développement web'),
(39, 'Informatique'),
(40, 'Mathématiques'),
(41, 'Physique'),
(42, 'Chimie'),
(43, 'Astronomie'),
(44, 'Cuisine du monde'),
(45, 'Sport'),
(46, 'Mode'),
(47, 'Photographie'),
(48, 'Cinéma'),
(49, 'Musique');
