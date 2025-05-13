<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - PolyBook</title>
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
    
    // Déterminer l'utilisateur dont on affiche les favoris
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
    $is_own_profile = ($user_id == $_SESSION['user_id']);
    
    // Récupérer les informations de l'utilisateur
    $user_sql = "SELECT username FROM user WHERE id = $user_id";
    $user_result = $conn->query($user_sql);
    
    if ($user_result->num_rows == 0) {
        redirect('index.php', 'Utilisateur non trouvé.', 'error');
    }
    
    $user = $user_result->fetch_assoc();
    $username = securiser($user['username']);
    
    // Si ce n'est pas le profil de l'utilisateur connecté, vérifier si on peut voir les favoris (amitié)
    if (!$is_own_profile) {
        $current_user_id = $_SESSION['user_id'];
        $friendship_sql = "SELECT status 
                          FROM friendship 
                          WHERE ((user_id1 = $current_user_id AND user_id2 = $user_id) 
                                OR (user_id1 = $user_id AND user_id2 = $current_user_id)) 
                          AND status = 'accepted'";
        $friendship_result = $conn->query($friendship_sql);
        
        if ($friendship_result->num_rows == 0) {
            redirect('profil.php?id=' . $user_id, "Vous devez être ami avec cet utilisateur pour voir ses favoris.", 'error');
        }
    }
    
    // Récupérer les livres favoris
    $favorites_sql = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
                     FROM book b 
                     JOIN favorite_book fb ON b.id = fb.book_id 
                     LEFT JOIN review r ON b.id = r.book_id 
                     WHERE fb.user_id = $user_id 
                     GROUP BY b.id 
                     ORDER BY fb.created_at DESC";
    $favorites_result = $conn->query($favorites_sql);
    ?>

    <main class="container">
        <h1><?php echo $is_own_profile ? 'Mes livres favoris' : 'Livres favoris de ' . $username; ?></h1>
        
        <?php if ($favorites_result->num_rows > 0): ?>
            <div class="books-grid">
                <?php while ($book = $favorites_result->fetch_assoc()): ?>
                    <?php $rating = number_format($book['avg_rating'], 1); ?>
                    <div class="book-card">
                        <a href="livre.php?id=<?php echo $book['id']; ?>">
                            <div class="book-cover">
                                <img src="https://covers.openlibrary.org/b/isbn/<?php echo $book['isbn']; ?>-M.jpg" alt="<?php echo securiser($book['title']); ?>">
                            </div>
                            <div class="book-info">
                                <h3><?php echo securiser($book['title']); ?></h3>
                                <p class="author">par <?php echo securiser($book['author']); ?></p>
                                <p class="year">(<?php echo $book['publication_year']; ?>)</p>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $rating): ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif ($i - 0.5 <= $rating): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span><?php echo $rating; ?> (<?php echo $book['review_count']; ?> avis)</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h2><?php echo $is_own_profile ? 'Vous n\'avez pas encore de favoris' : $username . ' n\'a pas encore de favoris'; ?></h2>
                <?php if ($is_own_profile): ?>
                    <p>Marquez des livres comme favoris en cliquant sur l'icône "Ajouter aux favoris" sur la page d'un livre.</p>
                    <a href="catalogue.php" class="btn primary">Parcourir le catalogue</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>