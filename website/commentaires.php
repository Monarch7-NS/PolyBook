<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'ID de l'avis est fourni
    if (!isset($_GET['review']) || empty($_GET['review'])) {
        redirect('index.php', 'Avis non spécifié.', 'error');
    }
    
    $review_id = intval($_GET['review']);
    
    // Récupérer les détails de l'avis
    $review_sql = "SELECT r.*, u.username as reviewer_name, b.title as book_title, b.id as book_id 
                  FROM review r 
                  JOIN user u ON r.user_id = u.id 
                  JOIN book b ON r.book_id = b.id 
                  WHERE r.id = $review_id";
    $review_result = $conn->query($review_sql);
    
    if ($review_result->num_rows == 0) {
        redirect('index.php', 'Avis non trouvé.', 'error');
    }
    
    $review = $review_result->fetch_assoc();
    
    // Ajouter un commentaire
    if (estConnecte() && isset($_POST['add_comment'])) {
        $user_id = $_SESSION['user_id'];
        $content = $conn->real_escape_string($_POST['content']);
        $book_id = $review['book_id'];
        
        $insert_sql = "INSERT INTO comment (created_at, user_id, review_id, book_id, content, comment_id) 
                      VALUES (NOW(), $user_id, $review_id, $book_id, '$content', NULL)";
        
        if ($conn->query($insert_sql)) {
            $message = "Commentaire ajouté avec succès !";
            $message_type = "success";
        } else {
            $message = "Erreur lors de l'ajout du commentaire : " . $conn->error;
            $message_type = "error";
        }
    }
    
    // Récupérer les commentaires
    $comments_sql = "SELECT c.*, u.username, u.email 
                    FROM comment c 
                    JOIN user u ON c.user_id = u.id 
                    WHERE c.review_id = $review_id 
                    ORDER BY c.created_at ASC";
    $comments_result = $conn->query($comments_sql);
    ?>

    <main class="container">
        <div class="comments-page">
            <div class="page-header">
                <h1>Commentaires sur un avis</h1>
                <div class="breadcrumbs">
                    <a href="livre.php?id=<?php echo $review['book_id']; ?>">Retour au livre "<?php echo securiser($review['book_title']); ?>"</a>
                </div>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="notification <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="review-display">
                <div class="review-header">
                    <span class="reviewer">Avis de <?php echo securiser($review['reviewer_name']); ?></span>
                    <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                </div>
                
                <div class="review-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa<?php echo ($i <= $review['rating']) ? 's' : 'r'; ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                
                <?php if ($review['contains_spoiler']): ?>
                    <div class="spoiler-alert">
                        <i class="fas fa-exclamation-triangle"></i> Attention : contient des spoilers
                        <button class="show-spoiler">Afficher quand même</button>
                    </div>
                    <div class="review-content hidden">
                        <?php echo nl2br(securiser($review['content'])); ?>
                    </div>
                <?php else: ?>
                    <div class="review-content">
                        <?php echo nl2br(securiser($review['content'])); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($review['tags'])): ?>
                    <div class="review-tags">
                        <?php 
                        $tags = explode(',', $review['tags']);
                        foreach ($tags as $tag):
                            $tag = trim($tag);
                            if (!empty($tag)):
                        ?>
                            <span class="tag"><?php echo securiser($tag); ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="comments-section">
                <h2>Commentaires (<?php echo $comments_result->num_rows; ?>)</h2>
                
                <?php if (estConnecte()): ?>
                <div class="comment-form-section">
                    <h3>Ajouter un commentaire</h3>
                    <form method="POST" action="" class="comment-form">
                        <textarea name="content" rows="4" placeholder="Votre commentaire..." required></textarea>
                        <button type="submit" name="add_comment" class="btn primary">Publier</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="login-prompt">
                    <a href="connexion_form.php">Connectez-vous</a> pour ajouter un commentaire.
                </div>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php if ($comments_result->num_rows > 0): ?>
                        <?php while ($comment = $comments_result->fetch_assoc()): ?>
                            <div class="comment-card">
                                <div class="comment-header">
                                    <div class="commenter-info">
                                        <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($comment['email']))); ?>?s=40&d=identicon" alt="Avatar" class="commenter-avatar">
                                        <span class="commenter-name"><?php echo securiser($comment['username']); ?></span>
                                    </div>
                                    <span class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <div class="comment-content">
                                    <?php echo nl2br(securiser($comment['content'])); ?>
                                </div>
                                
                                <?php if (estConnecte() && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <div class="comment-actions">
                                    <a href="supprimer_commentaire.php?id=<?php echo $comment['id']; ?>" class="btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-comments">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour afficher/masquer les spoilers
    const spoilerButton = document.querySelector('.show-spoiler');
    if (spoilerButton) {
        spoilerButton.addEventListener('click', function() {
            document.querySelector('.review-content').classList.remove('hidden');
            document.querySelector('.spoiler-alert').classList.add('hidden');
        });
    }
    </script>
</body>
</html>