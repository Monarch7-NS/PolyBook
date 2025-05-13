<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'utilisateur est connecté
    if (!estConnecte()) {
        redirect('connexion_form.php', 'Vous devez être connecté pour accéder à cette page.', 'error');
    }
    
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
    
    // Récupérer les informations de l'utilisateur
    $sql = "SELECT u.*, g.name as grade_name, r.content as resume_content, r.activity_score 
            FROM user u 
            LEFT JOIN grade g ON u.grade_id = g.id 
            LEFT JOIN resume r ON u.resume_id = r.id 
            WHERE u.id = $user_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        redirect('index.php', 'Utilisateur non trouvé.', 'error');
    }
    
    $user = $result->fetch_assoc();
    $is_own_profile = ($user_id == $_SESSION['user_id']);
    
    // Mettre à jour le profil si formulaire soumis
    if ($is_own_profile && isset($_POST['update_profile'])) {
        $resume_content = $conn->real_escape_string($_POST['resume_content']);
        
        // Mettre à jour le résumé
        $update_resume_sql = "UPDATE resume SET content = '$resume_content' WHERE id = {$user['resume_id']}";
        $conn->query($update_resume_sql);
        
        // Mise à jour des genres préférés
        if (isset($_POST['genres']) && is_array($_POST['genres'])) {
            // Supprimer les genres actuels
            $delete_genres_sql = "DELETE FROM user_genre WHERE user_id = $user_id";
            $conn->query($delete_genres_sql);
            
            // Ajouter les nouveaux genres
            foreach ($_POST['genres'] as $genre_id) {
                $genre_id = intval($genre_id);
                $insert_genre_sql = "INSERT INTO user_genre (created_at, user_id, genre_id) VALUES (NOW(), $user_id, $genre_id)";
                $conn->query($insert_genre_sql);
            }
        }
        
        redirect("profil.php", "Profil mis à jour avec succès !", 'success');
    }
    
    // Récupérer les genres préférés de l'utilisateur
    $user_genres_sql = "SELECT g.id, g.name 
                       FROM genre g 
                       JOIN user_genre ug ON g.id = ug.genre_id 
                       WHERE ug.user_id = $user_id";
    $user_genres_result = $conn->query($user_genres_sql);
    $user_genres = [];
    while ($genre = $user_genres_result->fetch_assoc()) {
        $user_genres[$genre['id']] = $genre['name'];
    }
    
    // Récupérer les livres favoris
    $favorites_sql = "SELECT b.*, AVG(r.rating) as avg_rating 
                     FROM book b 
                     JOIN favorite_book fb ON b.id = fb.book_id 
                     LEFT JOIN review r ON b.id = r.book_id 
                     WHERE fb.user_id = $user_id 
                     GROUP BY b.id 
                     ORDER BY fb.created_at DESC 
                     LIMIT 5";
    $favorites_result = $conn->query($favorites_sql);
    
    // Récupérer les avis récents
    $reviews_sql = "SELECT r.*, b.title as book_title 
                   FROM review r 
                   JOIN book b ON r.book_id = b.id 
                   WHERE r.user_id = $user_id 
                   ORDER BY r.created_at DESC 
                   LIMIT 5";
    $reviews_result = $conn->query($reviews_sql);
    
    // Récupérer les listes de lecture
    $lists_sql = "SELECT rl.*, COUNT(rlb.id) as book_count 
                 FROM reading_list rl 
                 LEFT JOIN reading_list_book rlb ON rl.id = rlb.reading_list_id 
                 WHERE rl.user_id = $user_id 
                 GROUP BY rl.id";
    $lists_result = $conn->query($lists_sql);
    
    // Vérifier l'état d'amitié si ce n'est pas son propre profil
    $friendship_status = null;
    if (!$is_own_profile) {
        $current_user_id = $_SESSION['user_id'];
        $friendship_sql = "SELECT status 
                          FROM friendship 
                          WHERE (user_id1 = $current_user_id AND user_id2 = $user_id) 
                          OR (user_id1 = $user_id AND user_id2 = $current_user_id)";
        $friendship_result = $conn->query($friendship_sql);
        
        if ($friendship_result->num_rows > 0) {
            $friendship_status = $friendship_result->fetch_assoc()['status'];
        }
    }
    
    // Traiter les demandes d'amitié
    if (!$is_own_profile && isset($_POST['friendship_action'])) {
        $current_user_id = $_SESSION['user_id'];
        $action = $_POST['friendship_action'];
        
        if ($action == 'send_request') {
            $insert_sql = "INSERT INTO friendship (created_at, user_id1, user_id2, status) 
                           VALUES (NOW(), $current_user_id, $user_id, 'pending')";
            $conn->query($insert_sql);
            $friendship_status = 'pending';
        } 
        elseif ($action == 'accept_request') {
            $update_sql = "UPDATE friendship 
                          SET status = 'accepted' 
                          WHERE user_id1 = $user_id AND user_id2 = $current_user_id";
            $conn->query($update_sql);
            $friendship_status = 'accepted';
        } 
        elseif ($action == 'cancel_request' || $action == 'remove_friend') {
            $delete_sql = "DELETE FROM friendship 
                          WHERE (user_id1 = $current_user_id AND user_id2 = $user_id) 
                          OR (user_id1 = $user_id AND user_id2 = $current_user_id)";
            $conn->query($delete_sql);
            $friendship_status = null;
        }
        
        redirect("profil.php?id=$user_id", "Action d'amitié effectuée.", 'success');
    }
    ?>

    <main class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <!-- Utiliser Gravatar ou un avatar par défaut -->
                <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user['email']))); ?>?s=200&d=identicon" alt="Avatar">
            </div>
            
            <div class="profile-info">
                <h1><?php echo securiser($user['username']); ?></h1>
                <p class="user-grade"><?php echo securiser($user['grade_name']); ?></p>
                
                <div class="user-stats">
                    <?php
                    // Compter les avis
                    $reviews_count_sql = "SELECT COUNT(*) as count FROM review WHERE user_id = $user_id";
                    $reviews_count_result = $conn->query($reviews_count_sql);
                    $reviews_count = $reviews_count_result->fetch_assoc()['count'];
                    
                    // Compter les livres favoris
                    $favorites_count_sql = "SELECT COUNT(*) as count FROM favorite_book WHERE user_id = $user_id";
                    $favorites_count_result = $conn->query($favorites_count_sql);
                    $favorites_count = $favorites_count_result->fetch_assoc()['count'];
                    
                    // Compter les amis
                    $friends_count_sql = "SELECT COUNT(*) as count 
                                         FROM friendship 
                                         WHERE ((user_id1 = $user_id OR user_id2 = $user_id)) 
                                         AND status = 'accepted'";
                    $friends_count_result = $conn->query($friends_count_sql);
                    $friends_count = $friends_count_result->fetch_assoc()['count'];
                    ?>
                    
                    <div class="stat">
                        <span class="stat-value"><?php echo $reviews_count; ?></span>
                        <span class="stat-label">Avis</span>
                    </div>
                    
                    <div class="stat">
                        <span class="stat-value"><?php echo $favorites_count; ?></span>
                        <span class="stat-label">Favoris</span>
                    </div>
                    
                    <div class="stat">
                        <span class="stat-value"><?php echo $friends_count; ?></span>
                        <span class="stat-label">Amis</span>
                    </div>
                    
                    <div class="stat">
                        <span class="stat-value"><?php echo number_format($user['activity_score'], 1); ?></span>
                        <span class="stat-label">Score d'activité</span>
                    </div>
                </div>
                
                <?php if (!$is_own_profile): ?>
                <div class="friendship-actions">
                    <form method="POST" action="">
                        <?php if ($friendship_status === null): ?>
                            <button type="submit" name="friendship_action" value="send_request" class="btn primary">
                                <i class="fas fa-user-plus"></i> Ajouter en ami
                            </button>
                        <?php elseif ($friendship_status === 'pending'): ?>
                            <?php 
                            // Vérifier qui a envoyé la demande
                            $request_sql = "SELECT * FROM friendship 
                                           WHERE user_id1 = $user_id AND user_id2 = {$_SESSION['user_id']} 
                                           AND status = 'pending'";
                            $request_result = $conn->query($request_sql);
                            $is_receiver = ($request_result->num_rows > 0);
                            
                            if ($is_receiver): 
                            ?>
                                <button type="submit" name="friendship_action" value="accept_request" class="btn primary">
                                    <i class="fas fa-check"></i> Accepter la demande
                                </button>
                            <?php else: ?>
                                <button type="submit" name="friendship_action" value="cancel_request" class="btn secondary">
                                    <i class="fas fa-times"></i> Annuler la demande
                                </button>
                            <?php endif; ?>
                        <?php elseif ($friendship_status === 'accepted'): ?>
                            <button type="submit" name="friendship_action" value="remove_friend" class="btn secondary">
                                <i class="fas fa-user-minus"></i> Retirer de ma liste d'amis
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-content">
            <div class="profile-sidebar">
                <div class="profile-bio">
                    <h3>À propos</h3>
                    <p><?php echo nl2br(securiser($user['resume_content'])); ?></p>
                    
                    <?php if (!empty($user_genres)): ?>
                    <h3>Genres préférés</h3>
                    <div class="favorite-genres">
                        <?php foreach ($user_genres as $genre): ?>
                            <span class="genre-tag"><?php echo securiser($genre); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($is_own_profile): ?>
                    <button id="edit-profile-btn" class="btn secondary">
                        <i class="fas fa-edit"></i> Modifier mon profil
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="reading-lists">
                    <h3>Listes de lecture</h3>
                    <?php if ($lists_result->num_rows > 0): ?>
                        <ul class="lists">
                            <?php while ($list = $lists_result->fetch_assoc()): ?>
                                <?php if ($list['is_public'] || $is_own_profile): ?>
                                <li>
                                    <a href="liste.php?id=<?php echo $list['id']; ?>">
                                        <?php echo securiser($list['name']); ?>
                                        <span class="book-count"><?php echo $list['book_count']; ?> livre(s)</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucune liste de lecture disponible.</p>
                    <?php endif; ?>
                    
                    <?php if ($is_own_profile): ?>
                    <a href="listes.php" class="btn secondary">
                        <i class="fas fa-list"></i> Gérer mes listes
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-main">
                <div class="favorite-books">
                    <h3>Livres favoris</h3>
                    <?php if ($favorites_result->num_rows > 0): ?>
                        <div class="books-grid small">
                            <?php while ($book = $favorites_result->fetch_assoc()): ?>
                                <div class="book-card small">
                                    <a href="livre.php?id=<?php echo $book['id']; ?>">
                                        <div class="book-cover">
                                            <img src="https://covers.openlibrary.org/b/isbn/<?php echo $book['isbn']; ?>-M.jpg" alt="<?php echo securiser($book['title']); ?>">
                                        </div>
                                        <div class="book-info">
                                            <h4><?php echo securiser($book['title']); ?></h4>
                                            <p class="author"><?php echo securiser($book['author']); ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <?php if ($favorites_count > 5): ?>
                            <a href="favoris.php<?php echo $is_own_profile ? '' : '?id='.$user_id; ?>" class="view-all">Voir tous les favoris</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Aucun livre favori pour le moment.</p>
                    <?php endif; ?>
                </div>
                
                <div class="recent-reviews">
                    <h3>Avis récents</h3>
                    <?php if ($reviews_result->num_rows > 0): ?>
                        <div class="reviews-list">
                            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <a href="livre.php?id=<?php echo $review['book_id']; ?>" class="book-link">
                                            <?php echo securiser($review['book_title']); ?>
                                        </a>
                                        <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                                    </div>
                                    
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa<?php echo ($i <= $review['rating']) ? 's' : 'r'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    
                                    <p class="review-excerpt">
                                        <?php 
                                        $excerpt = substr($review['content'], 0, 150);
                                        if (strlen($review['content']) > 150) $excerpt .= '...';
                                        echo securiser($excerpt); 
                                        ?>
                                    </p>
                                    
                                    <a href="livre.php?id=<?php echo $review['book_id']; ?>#review-<?php echo $review['id']; ?>" class="read-more">
                                        Lire l'avis complet
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <?php if ($reviews_count > 5): ?>
                            <a href="avis.php<?php echo $is_own_profile ? '' : '?id='.$user_id; ?>" class="view-all">Voir tous les avis</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Aucun avis publié pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($is_own_profile): ?>
        <!-- Modal pour modifier le profil -->
        <div id="edit-profile-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Modifier mon profil</h2>
                <form method="POST" action="" class="profile-form">
                    <div class="form-group">
                        <label for="resume_content">À propos de moi :</label>
                        <textarea id="resume_content" name="resume_content" rows="5"><?php echo securiser($user['resume_content']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Genres préférés :</label>
                        <div class="genres-selection">
                            <?php 
                            $all_genres_sql = "SELECT * FROM genre ORDER BY name";
                            $all_genres_result = $conn->query($all_genres_sql);
                            
                            while ($genre = $all_genres_result->fetch_assoc()): 
                                $is_selected = array_key_exists($genre['id'], $user_genres);
                            ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="genres[]" value="<?php echo $genre['id']; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                    <?php echo securiser($genre['name']); ?>
                                </label>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn primary">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal de modification du profil
    const modal = document.getElementById("edit-profile-modal");
    const btn = document.getElementById("edit-profile-btn");
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
    </script>
</body>
</html>