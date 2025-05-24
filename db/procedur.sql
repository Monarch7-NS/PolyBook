--Cette procédure va insérer des enregistrements dans la table book_genre en fonction des livres et des genres

DELIMITER //

CREATE PROCEDURE AssignGenresToBooks()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE bookId INT;
    DECLARE genreCount INT;
    DECLARE genreId INT;

    -- Déclaration d'un curseur pour parcourir tous les livres
    DECLARE bookCursor CURSOR FOR SELECT id FROM book;
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
            SET genreId = FLOOR(1 + RAND() * 49); -- Assurez-vous que l'ID est dans la plage de 1 à 49

            -- Insérer dans la table book_genre
            INSERT INTO book_genre (book_id, genre_id) VALUES (bookId, genreId);

            SET genreCount = genreCount - 1;
        END WHILE;
    END LOOP;

    CLOSE bookCursor;
END //

DELIMITER ;





CALL AssignGenresToBooks();