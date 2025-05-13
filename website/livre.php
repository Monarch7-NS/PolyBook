<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du livre - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'ID du livre est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        redirect('catalogue.php', 'Livre non spécifié.', 'error');
    }
    
    $book_id = intval($_GET['id']);
    
    // Récupérer les détails du livre
    $sql = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
            FROM book b 
            LEFT JOIN review r ON b.id = r.book_id 
            WHERE b.id = $book_id AND b.is_approved = TRUE 
            GROUP BY b.id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        redirect('catalogue.php', 'Livre non trouvé.', 'error');
    }
    
    $book = $result->fetch_assoc();
    $rating = is_null($book['avg_rating']) ? 0 : number_format($book['avg_rating'], 1);
    
    // Récupérer les genres du livre
    $genres_sql = "SELECT g.name 
                  FROM genre g 
                  JOIN book_genre bg ON g.id = bg.genre_id 
                  WHERE bg.book_id = $book_id";
    $genres_result = $conn->query($genres_sql);
    $genres = [];
    while ($genre = $genres_result->fetch_assoc()) {
        $genres[] = $genre['name'];
    }
    
    // Traiter le formulaire d'ajout d'avis si soumis
    if (estConnecte() && isset($_POST['submit_review'])) {
        $user_id = $_SESSION['user_id'];
        $review_rating = intval($_POST['rating']);
        $review_content = $conn->real_escape_string($_POST['content']);
        $contains_spoiler = isset($_POST['contains_spoiler']) ? 1 : 0;
        $tags = isset($_POST['tags']) ? $conn->real_escape_string($_POST['tags']) : '';
        
        // Vérifier si l'utilisateur a déjà posté un avis pour ce livre
        $check_sql = "SELECT id FROM review WHERE user_id = $user_id AND book_id = $book_id";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            // Mise à jour de l'avis existant
            $review_id = $check_result->fetch_assoc()['id'];
            $update_sql = "UPDATE review 
                          SET rating = $review_rating, 
                              content = '$review_content', 
                              contains_spoiler = $contains_spoiler, 
                              tags = '$tags' 
                          WHERE id = $review_id";
            $conn->query($update_sql);
            $message = "Votre avis a été mis à jour !";
        } else {
            // Ajout d'un nouvel avis
            $insert_sql = "INSERT INTO review (created_at, user_id, book_id, rating, content, contains_spoiler, tags) 
                          VALUES (NOW(), $user_id, $book_id, $review_rating, '$review_content', $contains_spoiler, '$tags')";
            $conn->query($insert_sql);
            $message = "Votre avis a été publié !";
        }
        
        // Rediriger pour éviter la soumission multiple du formulaire
        redirect("livre.php?id=$book_id", $message, 'success');
    }
    
    // Traiter l'ajout à une liste de lecture
    if (estConnecte() && isset($_POST['add_to_list'])) {
        $user_id = $_SESSION['user_id'];
        $list_id = intval($_POST['reading_list']);
        
        // Vérifier si le livre est déjà dans la liste
        $check_sql = "SELECT id FROM reading_list_book 
                     WHERE reading_list_id = $list_id AND book_id = $book_id";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows == 0) {
            $insert_sql = "INSERT INTO reading_list_book (reading_list_id, book_id, added_at) 
                          VALUES ($list_id, $book_id, NOW())";
            $conn->query($insert_sql);
            $message = "Livre ajouté à votre liste !";
        } else {
            $message = "Ce livre est déjà dans cette liste.";
        }
        
        redirect("livre.php?id=$book_id", $message, 'info');
    }
    
    // Traiter l'ajout aux favoris
    if (estConnecte() && isset($_POST['add_favorite'])) {
        $user_id = $_SESSION['user_id'];
        
        // Vérifier si le livre est déjà dans les favoris
        $check_sql = "SELECT id FROM favorite_book 
                     WHERE user_id = $user_id AND book_id = $book_id";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows == 0) {
            $insert_sql = "INSERT INTO favorite_book (created_at, user_id, book_id) 
                          VALUES (NOW(), $user_id, $book_id)";
            $conn->query($insert_sql);
            $message = "Livre ajouté à vos favoris !";
        } else {
            // Supprimer des favoris
            $delete_sql = "DELETE FROM favorite_book 
                          WHERE user_id = $user_id AND book_id = $book_id";
            $conn->query($delete_sql);
            $message = "Livre retiré de vos favoris.";
        }
        
        redirect("livre.php?id=$book_id", $message, 'info');
    }
    
    // Vérifier si le livre est dans les favoris de l'utilisateur
    $is_favorite = false;
    if (estConnecte()) {
        $user_id = $_SESSION['user_id'];
        $favorite_sql = "SELECT id FROM favorite_book 
                         WHERE user_id = $user_id AND book_id = $book_id";
        $favorite_result = $conn->query($favorite_sql);
        $is_favorite = ($favorite_result->num_rows > 0);
    }
    ?>

    <main class="container">
        <div class="book-details">
            <div class="book-header">
                <div class="book-cover-large">
                    <img src="https://covers.openlibrary.org/b/isbn/<?php echo $book['isbn']; ?>-L.jpg" alt="<?php echo securiser($book['title']); ?>">
                </div>
                
                <div class="book-info-large">
                    <h1><?php echo securiser($book['title']); ?></h1>
                    <h2>par <?php echo securiser($book['author']); ?></h2>
                    
                    <div class="book-meta">
                        <p><strong>Année de publication:</strong> <?php echo $book['publication_year']; ?></p>
                        <p><strong>ISBN:</strong> <?php echo securiser($book['isbn']); ?></p>
                        <p><strong>Langue:</strong> <?php echo securiser($book['language']); ?></p>
                        
                        <?php if (!empty($genres)): ?>
                        <p><strong>Genres:</strong> <?php echo securiser(implode(', ', $genres)); ?></p>
                        <?php endif; ?>
                        
                        <div class="rating-large">
                            <strong>Note moyenne:</strong>
                            <div class="stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                                <span><?php echo $rating; ?> (<?php echo $book['review_count']; ?> avis)</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (estConnecte()): ?>
                    <div class="book-actions">
                        <form method="POST" action="">
                            <button type="submit" name="add_favorite" class="btn <?php echo $is_favorite ? 'secondary' : 'primary'; ?>">
                                <i class="fa<?php echo $is_favorite ? 's' : 'r'; ?> fa-heart"></i> 
                                <?php echo $is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>
                            </button>
                        </form>
                        
                        <button id="add-to-list-btn" class="btn primary">
                            <i class="fas fa-list"></i> Ajouter à une liste
                        </button>
                        
                        <div id="list-modal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h3>Ajouter à une liste de lecture</h3>
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="reading_list">Choisir une liste :</label>
                                        <select id="reading_list" name="reading_list" required>
                                            <?php
                                            $user_id = $_SESSION['user_id'];
                                            $lists_sql = "SELECT id, name FROM reading_list WHERE user_id = $user_id";
                                            $lists_result = $conn->query($lists_sql);
                                            
                                            if ($lists_result->num_rows > 0) {
                                                while ($list = $lists_result->fetch_assoc()) {
                                                    echo '<option value="' . $list['id'] . '">' . securiser($list['name']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="" disabled>Aucune liste disponible</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" name="add_to_list" class="btn primary">Ajouter</button>
                                        <a href="listes.php?action=new" class="btn secondary">Créer une nouvelle liste</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="book-description">
                <h3>Description</h3>
                <p><?php echo nl2br(securiser($book['description'])); ?></p>
            </div>
            
            <div class="book-reviews">
                <h3>Avis et commentaires</h3>
                
                <?php if (estConnecte()): ?>
                <div class="write-review">
                    <h4>Écrire un avis</h4>
                    
                    <?php
                    // Récupérer l'avis de l'utilisateur s'il existe
                    $user_id = $_SESSION['user_id'];
                    $user_review_sql = "SELECT * FROM review WHERE user_id = $user_id AND book_id = $book_id";
                    $user_review_result = $conn->query($user_review_sql);
                    $user_review = ($user_review_result->num_rows > 0) ? $user_review_result->fetch_assoc() : null;
                    ?>
                    
                    <form method="POST" action="" class="review-form">
                        <div class="form-group">
                            <label for="rating">Votre note :</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php if ($user_review && $user_review['rating'] == $i) echo 'checked'; ?> required />
                                <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Votre avis :</label>
                            <textarea id="content" name="content" rows="5" required><?php if ($user_review) echo securiser($user_review['content']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="tags">Tags (séparés par des virgules) :</label>
                            <input type="text" id="tags" name="tags" value="<?php if ($user_review) echo securiser($user_review['tags']); ?>">
                        </div>
                        
                        <div class="form-group checkbox">
                            <input type="checkbox" id="contains_spoiler" name="contains_spoiler" <?php if ($user_review && $user_review['contains_spoiler']) echo 'checked'; ?>>
                            <label for="contains_spoiler">Contient des spoilers</label>
                        </div>
                        
                        <button type="submit" name="submit_review" class="btn primary">
                            <?php echo $user_review ? 'Mettre à jour' : 'Publier'; ?> mon avis
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <p class="login-prompt">
                    <a href="connexion_form.php">Connectez-vous</a> pour écrire un avis.
                </p>
                <?php endif; ?>
                
                <div class="reviews-list">
                    <?php
                    // Récupérer les avis
                    $reviews_sql = "SELECT r.*, u.username, COUNT(c.id) as comment_count 
                                   FROM review r 
                                   JOIN user u ON r.user_id = u.id 
                                   LEFT JOIN comment c ON r.id = c.review_id 
                                   WHERE r.book_id = $book_id 
                                   GROUP BY r.id 
                                   ORDER BY r.created_at DESC";
                    $reviews_result = $conn->query($reviews_sql);
                    
                    if ($reviews_result->num_rows > 0) {
                        while ($review = $reviews_result->fetch_assoc()) {
                            echo '<div class="review-card' . ($review['contains_spoiler'] ? ' spoiler-warning' : '') . '">';
                            
                            echo '<div class="review-header">';
                            echo '<span class="reviewer">' . securiser($review['username']) . '</span>';
                            echo '<span class="review-date">' . date('d/m/Y', strtotime($review['created_at'])) . '</span>';
                            echo '</div>';
                            
                            echo '<div class="review-rating">';
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $review['rating']) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            echo '</div>';
                            
                            if ($review['contains_spoiler']) {
                                echo '<div class="spoiler-alert">';
                                echo '<i class="fas fa-exclamation-triangle"></i> Attention : contient des spoilers';
                                echo '<button class="show-spoiler">Afficher quand même</button>';
                                echo '</div>';
                            }
                            
                            echo '<div class="review-content' . ($review['contains_spoiler'] ? ' hidden' : '') . '">';
                            echo '<p>' . nl2br(securiser($review['content'])) . '</p>';
                            echo '</div>';
                            
                            if (!empty($review['tags'])) {
                                echo '<div class="review-tags">';
                                $tags = explode(',', $review['tags']);
                                foreach ($tags as $tag) {
                                    echo '<span class="tag">' . securiser(trim($tag)) . '</span>';
                                }
                                echo '</div>';
                            }
                            
                            echo '<div class="review-actions">';
                            echo '<a href="commentaires.php?review=' . $review['id'] . '" class="comments-link">';
                            echo '<i class="fas fa-comment"></i> ' . $review['comment_count'] . ' commentaire' . ($review['comment_count'] > 1 ? 's' : '');
                            echo '</a>';
                            
                            if (estConnecte()) {
                                echo '<button class="btn-comment" data-review="' . $review['id'] . '">';
                                echo '<i class="fas fa-reply"></i> Commenter';
                                echo '</button>';
                            }
                            echo '</div>';
                            
                            // Formulaire de commentaire (caché par défaut)
                            if (estConnecte()) {
                                echo '<div class="comment-form-container hidden" id="comment-form-' . $review['id'] . '">';
                                echo '<form method="POST" action="ajouter_commentaire.php" class="comment-form">';
                                echo '<input type="hidden" name="review_id" value="' . $review['id'] . '">';
                                echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                                echo '<textarea name="content" rows="3" placeholder="Votre commentaire..." required></textarea>';
                                echo '<button type="submit" class="btn primary">Publier</button>';
                                echo '</form>';
                                echo '</div>';
                            }
                            
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-reviews">Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal d'ajout à une liste
    const modal = document.getElementById("list-modal");
    const btn = document.getElementById("add-to-list-btn");
    const span = document.getElementsByClassName("close")[0];
    
    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }
    
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Script pour afficher/masquer les spoilers
    const spoilerButtons = document.querySelectorAll('.show-spoiler');
    spoilerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reviewCard = this.closest('.review-card');
            reviewCard.querySelector('.review-content').classList.remove('hidden');
            reviewCard.querySelector('.spoiler-alert').classList.add('hidden');
        });
    });
    
    // Script pour afficher/masquer les formulaires de commentaire
    const commentButtons = document.querySelectorAll('.btn-comment');
    commentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review');
            const form = document.getElementById('comment-form-' + reviewId);
            form.classList.toggle('hidden');
        });
    });
    </script>
</body>
</html>