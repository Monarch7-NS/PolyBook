<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste de lecture - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'ID de la liste est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        redirect('listes.php', 'Liste non spécifiée.', 'error');
    }
    
    $list_id = intval($_GET['id']);
    
    // Récupérer les détails de la liste
    $list_sql = "SELECT rl.*, u.username 
                FROM reading_list rl 
                JOIN user u ON rl.user_id = u.id 
                WHERE rl.id = $list_id";
    $list_result = $conn->query($list_sql);
    
    if ($list_result->num_rows == 0) {
        redirect('listes.php', 'Liste non trouvée.', 'error');
    }
    
    $list = $list_result->fetch_assoc();
    $is_owner = estConnecte() && $list['user_id'] == $_SESSION['user_id'];
    
    // Vérifier si la liste est privée et l'utilisateur n'est pas le propriétaire
    if (!$list['is_public'] && !$is_owner) {
        redirect('listes.php', 'Cette liste est privée.', 'error');
    }
    
    // Ajouter un livre à la liste
    if ($is_owner && isset($_POST['add_book'])) {
        $book_id = intval($_POST['book_id']);
        
        // Vérifier si le livre existe et est approuvé
        $book_check_sql = "SELECT id FROM book WHERE id = $book_id AND is_approved = TRUE";
        $book_check_result = $conn->query($book_check_sql);
        
        if ($book_check_result->num_rows > 0) {
            // Vérifier si le livre est déjà dans la liste
            $book_in_list_sql = "SELECT id FROM reading_list_book 
                               WHERE reading_list_id = $list_id AND book_id = $book_id";
            $book_in_list_result = $conn->query($book_in_list_sql);
            
            if ($book_in_list_result->num_rows == 0) {
                $add_book_sql = "INSERT INTO reading_list_book (reading_list_id, book_id, added_at) 
                               VALUES ($list_id, $book_id, NOW())";
                
                if ($conn->query($add_book_sql)) {
                    $message = "Livre ajouté à la liste avec succès !";
                    $message_type = "success";
                } else {
                    $message = "Erreur lors de l'ajout du livre : " . $conn->error;
                    $message_type = "error";
                }
            } else {
                $message = "Ce livre est déjà dans la liste.";
                $message_type = "info";
            }
        } else {
            $message = "Livre non trouvé ou non approuvé.";
            $message_type = "error";
        }
    }
    
    // Retirer un livre de la liste
    if ($is_owner && isset($_GET['remove'])) {
        $book_id = intval($_GET['remove']);
        
        $remove_book_sql = "DELETE FROM reading_list_book 
                           WHERE reading_list_id = $list_id AND book_id = $book_id";
        
        if ($conn->query($remove_book_sql)) {
            $message = "Livre retiré de la liste avec succès.";
            $message_type = "success";
        } else {
            $message = "Erreur lors du retrait du livre : " . $conn->error;
            $message_type = "error";
        }
    }
    
    // Récupérer les livres de la liste
    $books_sql = "SELECT b.*, rlb.added_at 
                 FROM reading_list_book rlb 
                 JOIN book b ON rlb.book_id = b.id 
                 WHERE rlb.reading_list_id = $list_id 
                 ORDER BY rlb.added_at DESC";
    $books_result = $conn->query($books_sql);
    ?>

    <main class="container">
        <div class="reading-list-details">
            <div class="reading-list-header">
                <h1><?php echo securiser($list['name']); ?></h1>
                <div class="reading-list-meta">
                    <p><i class="fas fa-user"></i> Créée par <?php echo securiser($list['username']); ?></p>
                    <p><i class="fas fa-calendar"></i> Créée le <?php echo date('d/m/Y', strtotime($list['created_at'])); ?></p>
                    <p class="visibility">
                        <i class="fas fa-<?php echo $list['is_public'] ? 'globe' : 'lock'; ?>"></i>
                        Liste <?php echo $list['is_public'] ? 'publique' : 'privée'; ?>
                    </p>
                </div>
                
                <?php if ($is_owner): ?>
                <div class="reading-list-actions">
                    <a href="modifier_liste.php?id=<?php echo $list_id; ?>" class="btn secondary">
                        <i class="fas fa-edit"></i> Modifier la liste
                    </a>
                    <a href="supprimer_liste.php?id=<?php echo $list_id; ?>" class="btn error" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette liste ?');">
                        <i class="fas fa-trash"></i> Supprimer la liste
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="notification <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($is_owner): ?>
            <div class="add-book-section">
                <h3>Ajouter un livre à la liste</h3>
                <form method="POST" action="" class="add-book-form">
                    <select name="book_id" required>
                        <option value="">Sélectionner un livre...</option>
                        <?php
                        // Récupérer les livres qui ne sont pas déjà dans la liste
                        $available_books_sql = "SELECT b.id, b.title, b.author 
                                              FROM book b 
                                              WHERE b.is_approved = TRUE 
                                              AND b.id NOT IN (
                                                  SELECT book_id FROM reading_list_book WHERE reading_list_id = $list_id
                                              )
                                              ORDER BY b.title";
                        $available_books_result = $conn->query($available_books_sql);
                        
                        while ($book = $available_books_result->fetch_assoc()) {
                            echo '<option value="' . $book['id'] . '">' . securiser($book['title']) . ' par ' . securiser($book['author']) . '</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" name="add_book" class="btn primary">Ajouter</button>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if ($books_result->num_rows > 0): ?>
                <h3>Livres dans cette liste (<?php echo $books_result->num_rows; ?>)</h3>
                <div class="books-in-list">
                    <?php while ($book = $books_result->fetch_assoc()): ?>
                        <div class="book-list-item">
                            <div class="book-list-cover">
                                <img src="https://covers.openlibrary.org/b/isbn/<?php echo $book['isbn']; ?>-M.jpg" alt="<?php echo securiser($book['title']); ?>">
                            </div>
                            <div class="book-list-info">
                                <h3><a href="livre.php?id=<?php echo $book['id']; ?>"><?php echo securiser($book['title']); ?></a></h3>
                                <p class="book-list-author">par <?php echo securiser($book['author']); ?> (<?php echo $book['publication_year']; ?>)</p>
                                <p class="book-list-description"><?php echo substr(securiser($book['description']), 0, 200); ?>...</p>
                            </div>
                            <span class="book-list-date">Ajouté le <?php echo date('d/m/Y', strtotime($book['added_at'])); ?></span>
                            
                            <?php if ($is_owner): ?>
                            <div class="book-list-actions">
                                <a href="liste.php?id=<?php echo $list_id; ?>&remove=<?php echo $book['id']; ?>" class="btn error" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir retirer ce livre de la liste ?');">
                                    <i class="fas fa-times"></i> Retirer
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-books">
                    <p>Aucun livre dans cette liste pour le moment.</p>
                    <?php if ($is_owner): ?>
                        <p>Utilisez le formulaire ci-dessus pour ajouter des livres à votre liste.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>