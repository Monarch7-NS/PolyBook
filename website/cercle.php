<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cercle - PolyBook</title>
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
    
    // Vérifier si l'ID du cercle est fourni
    if (!isset($_GET['id']) || $_GET['id'] === '') {
        redirect('cercles.php', 'Cercle non spécifié.', 'error');
    }
    
    $circle_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Vérifier si l'utilisateur est membre du cercle
    $member_check_sql = "SELECT * FROM circle_member WHERE circle_id = $circle_id AND user_id = $user_id";
    $member_check_result = $conn->query($member_check_sql);
    
    if ($member_check_result->num_rows == 0) {
        redirect('cercles.php', 'Vous n\'êtes pas membre de ce cercle.', 'error');
    }
    
    // Récupérer les détails du cercle
    $circle_sql = "SELECT c.*, u.username as owner_name 
                  FROM circle c 
                  JOIN user u ON c.owner_user_id = u.id 
                  WHERE c.id = $circle_id";
    $circle_result = $conn->query($circle_sql);
    
    if ($circle_result->num_rows == 0) {
        redirect('cercles.php', 'Cercle non trouvé.', 'error');
    }
    
    $circle = $circle_result->fetch_assoc();
    $is_owner = ($circle['owner_user_id'] == $user_id);
    
    // Traiter l'invitation d'un ami
    if ($is_owner && isset($_POST['invite_friend'])) {
        $friend_id = intval($_POST['friend_id']);
        
        // Vérifier si l'ami est déjà membre du cercle
        $check_sql = "SELECT * FROM circle_member WHERE circle_id = $circle_id AND user_id = $friend_id";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows == 0) {
            // Vérifier si une invitation est déjà en attente
            $invitation_check_sql = "SELECT * FROM circle_invitation 
                                   WHERE circle_id = $circle_id AND invited_user_id = $friend_id AND status = 'pending'";
            $invitation_check_result = $conn->query($invitation_check_sql);
            
            if ($invitation_check_result->num_rows == 0) {
                $invite_sql = "INSERT INTO circle_invitation (created_at, circle_id, inviter_user_id, invited_user_id, status) 
                              VALUES (NOW(), $circle_id, $user_id, $friend_id, 'pending')";
                $conn->query($invite_sql);
                
                $message = "Invitation envoyée avec succès !";
            } else {
                $message = "Une invitation est déjà en attente pour cet ami.";
            }
        } else {
            $message = "Cette personne est déjà membre du cercle.";
        }
        
        redirect("cercle.php?id=$circle_id", $message, 'info');
    }
    
    // Récupérer les membres du cercle
    $members_sql = "SELECT u.id, u.username, u.email, g.name as grade_name, 
                    (u.id = c.owner_user_id) as is_owner 
                    FROM circle_member cm 
                    JOIN user u ON cm.user_id = u.id 
                    JOIN circle c ON cm.circle_id = c.id 
                    LEFT JOIN grade g ON u.grade_id = g.id 
                    WHERE cm.circle_id = $circle_id 
                    ORDER BY is_owner DESC, u.username";
    $members_result = $conn->query($members_sql);
    
    // Récupérer les avis récents des membres du cercle
    $reviews_sql = "SELECT r.*, u.username, b.title as book_title, b.id as book_id 
                   FROM review r 
                   JOIN user u ON r.user_id = u.id 
                   JOIN book b ON r.book_id = b.id 
                   JOIN circle_member cm ON r.user_id = cm.user_id 
                   WHERE cm.circle_id = $circle_id 
                   ORDER BY r.created_at DESC 
                   LIMIT 10";
    $reviews_result = $conn->query($reviews_sql);
    
    // Récupérer les livres populaires dans le cercle
    $popular_books_sql = "SELECT b.*, COUNT(DISTINCT fb.id) as favorite_count, AVG(r.rating) as avg_rating 
                         FROM book b 
                         LEFT JOIN favorite_book fb ON b.id = fb.book_id 
                         LEFT JOIN circle_member cm ON fb.user_id = cm.user_id 
                         LEFT JOIN review r ON b.id = r.book_id 
                         WHERE cm.circle_id = $circle_id 
                         GROUP BY b.id 
                         ORDER BY favorite_count DESC, avg_rating DESC 
                         LIMIT 5";
    $popular_books_result = $conn->query($popular_books_sql);
    ?>

    <main class="container">
        <div class="circle-details">
            <div class="circle-header">
                <h1><?php echo securiser($circle['name']); ?></h1>
                <div class="circle-meta">
                    <p><i class="fas fa-user"></i> Créé par <?php echo securiser($circle['owner_name']); ?></p>
                    <p><i class="fas fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($circle['created_at'])); ?></p>
                </div>
                
                <?php if ($is_owner): ?>
                <div class="circle-actions">
                    <a href="gerer_cercle.php?id=<?php echo $circle_id; ?>" class="btn secondary">
                        <i class="fas fa-cog"></i> Gérer le cercle
                    </a>
                </div>
                <?php else: ?>
                <div class="circle-actions">
                    <a href="quitter_cercle.php?id=<?php echo $circle_id; ?>" class="btn secondary" 
                       onclick="return confirm('Êtes-vous sûr de vouloir quitter ce cercle ?');">
                        <i class="fas fa-sign-out-alt"></i> Quitter le cercle
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="circle-content">
                <div class="circle-sidebar">
                    <div class="circle-members">
                        <h3>Membres (<?php echo $members_result->num_rows; ?>)</h3>
                        <ul class="members-list">
                            <?php while ($member = $members_result->fetch_assoc()): ?>
                                <li class="member-item">
                                    <a href="profil.php?id=<?php echo $member['id']; ?>" class="member-link">
                                        <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($member['email']))); ?>?s=40&d=identicon" alt="Avatar" class="member-avatar">
                                        <div class="member-info">
                                            <span class="member-name"><?php echo securiser($member['username']); ?></span>
                                            <span class="member-grade"><?php echo securiser($member['grade_name']); ?></span>
                                        </div>
                                        <?php if ($member['is_owner']): ?>
                                            <span class="owner-badge-small">Créateur</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        
                        <?php if ($is_owner): ?>
                        <button id="invite-member-btn" class="btn primary">
                            <i class="fas fa-user-plus"></i> Inviter un ami
                        </button>
                        
                        <!-- Modal pour inviter un ami -->
                        <div id="invite-modal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2>Inviter un ami</h2>
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="friend_id">Choisir un ami :</label>
                                        <select id="friend_id" name="friend_id" required>
                                            <?php
                                            // Récupérer les amis qui ne sont pas encore dans le cercle
                                            $friends_sql = "SELECT u.id, u.username 
                                                         FROM user u 
                                                         JOIN friendship f ON (u.id = f.user_id1 OR u.id = f.user_id2) 
                                                         WHERE f.status = 'accepted' 
                                                         AND ((f.user_id1 = $user_id AND u.id = f.user_id2) 
                                                              OR (f.user_id2 = $user_id AND u.id = f.user_id1)) 
                                                         AND u.id NOT IN (
                                                             SELECT user_id FROM circle_member WHERE circle_id = $circle_id
                                                         )";
                                            $friends_result = $conn->query($friends_sql);
                                            
                                            if ($friends_result->num_rows > 0) {
                                                while ($friend = $friends_result->fetch_assoc()) {
                                                    echo '<option value="' . $friend['id'] . '">' . securiser($friend['username']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="" disabled>Aucun ami disponible à inviter</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="invite_friend" class="btn primary">Envoyer l'invitation</button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="popular-books">
                        <h3>Livres populaires du cercle</h3>
                        <?php if ($popular_books_result->num_rows > 0): ?>
                            <div class="books-list">
                                <?php while ($book = $popular_books_result->fetch_assoc()): ?>
                                    <div class="book-item">
                                        <a href="livre.php?id=<?php echo $book['id']; ?>" class="book-link">
                                            <div class="book-cover-small">
                                                <img src="https://covers.openlibrary.org/b/isbn/<?php echo $book['isbn']; ?>-S.jpg" alt="<?php echo securiser($book['title']); ?>">
                                            </div>
                                            <div class="book-info-small">
                                                <h4><?php echo securiser($book['title']); ?></h4>
                                                <p class="book-author"><?php echo securiser($book['author']); ?></p>
                                                <p class="favorite-count">
                                                    <i class="fas fa-heart"></i> <?php echo $book['favorite_count']; ?> membre(s)
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p>Aucun livre favori dans ce cercle pour le moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="circle-main">
                    <div class="circle-feed">
                        <h3>Activité récente</h3>
                        <?php if ($reviews_result->num_rows > 0): ?>
                            <div class="feed-list">
                                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                                    <div class="feed-item">
                                        <div class="feed-header">
                                            <a href="profil.php?id=<?php echo $review['user_id']; ?>" class="user-link">
                                                <?php echo securiser($review['username']); ?>
                                            </a>
                                            <span class="feed-action">a publié un avis sur</span>
                                            <a href="livre.php?id=<?php echo $review['book_id']; ?>" class="book-link">
                                                <?php echo securiser($review['book_title']); ?>
                                            </a>
                                        </div>
                                        
                                        <div class="feed-content">
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
                                                <p class="review-text hidden">
                                                    <?php 
                                                    $text = substr($review['content'], 0, 300);
                                                    if (strlen($review['content']) > 300) $text .= '...';
                                                    echo nl2br(securiser($text)); 
                                                    ?>
                                                </p>
                                            <?php else: ?>
                                                <p class="review-text">
                                                    <?php 
                                                    $text = substr($review['content'], 0, 300);
                                                    if (strlen($review['content']) > 300) $text .= '...';
                                                    echo nl2br(securiser($text)); 
                                                    ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <div class="feed-footer">
                                                <span class="feed-date"><?php echo date('d/m/Y à H:i', strtotime($review['created_at'])); ?></span>
                                                <a href="livre.php?id=<?php echo $review['book_id']; ?>#review-<?php echo $review['id']; ?>" class="read-more">
                                                    Lire l'avis complet
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p>Aucune activité récente dans ce cercle.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal d'invitation
    const modal = document.getElementById("invite-modal");
    const btn = document.getElementById("invite-member-btn");
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
            const feedItem = this.closest('.feed-item');
            feedItem.querySelector('.review-text').classList.remove('hidden');
            feedItem.querySelector('.spoiler-alert').classList.add('hidden');
        });
    });
    </script>
</body>
</html>