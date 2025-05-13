<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Avis - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Déterminer l'utilisateur dont on affiche les avis
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : (estConnecte() ? $_SESSION['user_id'] : 0);
    
    // Si aucun utilisateur n'est spécifié et qu'on n'est pas connecté
    if ($user_id === 0) {
        redirect('connexion_form.php', 'Vous devez être connecté pour accéder à cette page.', 'error');
    }
    
    // Récupérer les informations de l'utilisateur
    $user_sql = "SELECT username FROM user WHERE id = $user_id";
    $user_result = $conn->query($user_sql);
    
    if ($user_result->num_rows == 0) {
        redirect('index.php', 'Utilisateur non trouvé.', 'error');
    }
    
    $user = $user_result->fetch_assoc();
    $username = securiser($user['username']);
    $is_own_profile = estConnecte() && ($user_id == $_SESSION['user_id']);
    
    // Pagination
    $avis_par_page = 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = max(1, $page); // S'assurer que la page est au moins 1
    $offset = ($page - 1) * $avis_par_page;
    
    // Récupérer les avis
    $avis_sql = "SELECT r.*, b.title as book_title, b.author as book_author, b.id as book_id, b.isbn, 
                COUNT(c.id) as comment_count 
                FROM review r 
                JOIN book b ON r.book_id = b.id 
                LEFT JOIN comment c ON r.id = c.review_id 
                WHERE r.user_id = $user_id 
                GROUP BY r.id 
                ORDER BY r.created_at DESC 
                LIMIT $offset, $avis_par_page";
    $avis_result = $conn->query($avis_sql);
    
    // Calculer le nombre total d'avis pour la pagination
    $count_sql = "SELECT COUNT(*) as total FROM review WHERE user_id = $user_id";
    $count_result = $conn->query($count_sql);
    $total_avis = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_avis / $avis_par_page);
    ?>

    <main class="container">
        <h1><?php echo $is_own_profile ? 'Mes avis' : 'Avis de ' . $username; ?></h1>
        
        <?php if ($avis_result->num_rows > 0): ?>
            <div class="reviews-full-list">
                <?php while ($review = $avis_result->fetch_assoc()): ?>
                    <div class="review-full-card<?php echo $review['contains_spoiler'] ? ' spoiler-warning' : ''; ?>">
                        <div class="review-book">
                            <div class="review-book-cover">
                                <a href="livre.php?id=<?php echo $review['book_id']; ?>">
                                    <img src="https://covers.openlibrary.org/b/isbn/<?php echo $review['isbn']; ?>-M.jpg" alt="<?php echo securiser($review['book_title']); ?>">
                                </a>
                            </div>
                            <div class="review-book-info">
                                <h2><a href="livre.php?id=<?php echo $review['book_id']; ?>"><?php echo securiser($review['book_title']); ?></a></h2>
                                <p class="book-author">par <?php echo securiser($review['book_author']); ?></p>
                            </div>
                        </div>
                        
                        <div class="review-content-full">
                            <div class="review-header">
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?php echo ($i <= $review['rating']) ? 's' : 'r'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            
                            <?php if ($review['contains_spoiler']): ?>
                                <div class="spoiler-alert">
                                    <i class="fas fa-exclamation-triangle"></i> Attention : contient des spoilers
                                    <button class="show-spoiler">Afficher quand même</button>
                                </div>
                                <div class="review-text hidden">
                                    <?php echo nl2br(securiser($review['content'])); ?>
                                </div>
                            <?php else: ?>
                                <div class="review-text">
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
                            
                            <div class="review-actions">
                                <a href="livre.php?id=<?php echo $review['book_id']; ?>#review-<?php echo $review['id']; ?>" class="comments-link">
                                    <i class="fas fa-comment"></i> <?php echo $review['comment_count']; ?> commentaire<?php echo ($review['comment_count'] > 1) ? 's' : ''; ?>
                                </a>
                                
                                <?php if ($is_own_profile): ?>
                                <div class="review-owner-actions">
                                    <a href="livre.php?id=<?php echo $review['book_id']; ?>#write-review" class="btn-edit">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="supprimer_avis.php?id=<?php echo $review['id']; ?>" class="btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-button">&laquo; Précédent</a>
                        <?php endif; ?>
                        
                        <?php
                        // Afficher les pages
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                            $active = ($i == $page) ? 'active' : '';
                        ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-button <?php echo $active; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-button">Suivant &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-star"></i>
                <h2><?php echo $is_own_profile ? 'Vous n\'avez pas encore publié d\'avis' : $username . ' n\'a pas encore publié d\'avis'; ?></h2>
                <?php if ($is_own_profile): ?>
                    <p>Partagez votre opinion en rédigeant des avis sur les livres que vous avez lus.</p>
                    <a href="catalogue.php" class="btn primary">Parcourir le catalogue</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour afficher/masquer les spoilers
    const spoilerButtons = document.querySelectorAll('.show-spoiler');
    spoilerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reviewCard = this.closest('.review-full-card');
            reviewCard.querySelector('.review-text').classList.remove('hidden');
            reviewCard.querySelector('.spoiler-alert').classList.add('hidden');
        });
    });
    </script>
</body>
</html>