<!-- books.php -->
<?php
require_once 'includes/db.php';

// Récupérer toutes les catégories pour le filtre
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Préparer la requête pour les livres
$query = "SELECT books.*, categories.name as category_name FROM books 
          LEFT JOIN categories ON books.category_id = categories.id";

// Filtrer par catégorie si demandé
if (isset($_GET['category']) && $_GET['category'] > 0) {
    $category_id = mysqli_real_escape_string($conn, $_GET['category']);
    $query .= " WHERE books.category_id = $category_id";
}

// Filtrer par recherche si demandé
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND (books.title LIKE '%$search%' OR books.author LIKE '%$search%')";
    } else {
        $query .= " WHERE books.title LIKE '%$search%' OR books.author LIKE '%$search%'";
    }
}

// Ordonner les résultats
$query .= " ORDER BY books.title ASC";

// Exécuter la requête
$books_result = mysqli_query($conn, $query);

$page_title = 'Bibliothèque de Livres';
?>

<?php include 'includes/header.php'; ?>

<section class="books-section">
    <div class="container">
        <h2>Bibliothèque de Livres</h2>
        
        <div class="filters">
            <form action="books.php" method="GET">
                <div class="filter-group">
                    <label for="category">Filtrer par catégorie:</label>
                    <select name="category" id="category">
                        <option value="0">Toutes les catégories</option>
                        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Rechercher:</label>
                    <input type="text" name="search" id="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn">Appliquer les filtres</button>
            </form>
        </div>
        
        <div class="book-grid">
            <?php if (mysqli_num_rows($books_result) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($books_result)): ?>
                    <div class="book-card">
                        <?php if (!empty($book['image'])): ?>
                            <img src="images/books/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                        <?php else: ?>
                            <img src="images/no-cover.jpg" alt="Pas de couverture">
                        <?php endif; ?>
                        <h3><?php echo $book['title']; ?></h3>
                        <p class="author">par <?php echo $book['author']; ?></p>
                        <?php if (!empty($book['category_name'])): ?>
                            <p class="category">Catégorie: <?php echo $book['category_name']; ?></p>
                        <?php endif; ?>
                        <a href="book-details.php?id=<?php echo $book['id']; ?>" class="btn">Voir les détails</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">Aucun livre trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- book-details.php -->
<?php
require_once 'includes/db.php';

// Vérifier si l'ID du livre est spécifié
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: books.php');
    exit();
}

$book_id = mysqli_real_escape_string($conn, $_GET['id']);

// Récupérer les détails du livre
$query = "SELECT books.*, categories.name as category_name FROM books 
          LEFT JOIN categories ON books.category_id = categories.id 
          WHERE books.id = $book_id";
$result = mysqli_query($conn, $query);

// Vérifier si le livre existe
if (mysqli_num_rows($result) == 0) {
    header('Location: books.php');
    exit();
}

$book = mysqli_fetch_assoc($result);

// Récupérer les critiques du livre
$reviews_query = "SELECT r.*, u.username FROM reviews r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.book_id = $book_id 
                 ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_query);

// Vérifier si l'utilisateur a déjà emprunté ce livre
$has_borrowed = false;
$can_borrow = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $borrow_check_query = "SELECT * FROM borrowings 
                          WHERE user_id = $user_id AND book_id = $book_id AND (status = 'borrowed' OR status = 'overdue')";
    $borrow_check_result = mysqli_query($conn, $borrow_check_query);
    $has_borrowed = mysqli_num_rows($borrow_check_result) > 0;
    
    // Vérifier si le livre est disponible pour emprunt
    $book_availability_query = "SELECT COUNT(*) as borrow_count FROM borrowings 
                               WHERE book_id = $book_id AND (status = 'borrowed' OR status = 'overdue')";
    $book_availability_result = mysqli_query($conn, $book_availability_query);
    $book_availability = mysqli_fetch_assoc($book_availability_result);
    
    // Supposons qu'il y a un exemplaire par livre
    $can_borrow = $book_availability['borrow_count'] == 0;
}

$page_title = $book['title'];
?>

<?php include 'includes/header.php'; ?>

<section class="book-details">
    <div class="container">
        <div class="book-info">
            <div class="book-image">
                <?php if (!empty($book['image'])): ?>
                    <img src="images/books/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                <?php else: ?>
                    <img src="images/no-cover.jpg" alt="Pas de couverture">
                <?php endif; ?>
            </div>
            
            <div class="book-content">
                <h2><?php echo $book['title']; ?></h2>
                <p class="author">par <?php echo $book['author']; ?></p>
                
                <?php if (!empty($book['category_name'])): ?>
                    <p class="category"><strong>Catégorie:</strong> <?php echo $book['category_name']; ?></p>
                <?php endif; ?>
                
                <?php if (!empty($book['publication_year'])): ?>
                    <p><strong>Année de publication:</strong> <?php echo $book['publication_year']; ?></p>
                <?php endif; ?>
                
                <?php if (!empty($book['isbn'])): ?>
                    <p><strong>ISBN:</strong> <?php echo $book['isbn']; ?></p>
                <?php endif; ?>
                
                <div class="book-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br($book['description']); ?></p>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="book-actions">
                        <?php if ($has_borrowed): ?>
                            <p class="already-borrowed">Vous avez déjà emprunté ce livre.</p>
                        <?php elseif ($can_borrow): ?>
                            <a href="borrow.php?book_id=<?php echo $book_id; ?>" class="btn btn-primary">Emprunter ce livre</a>
                        <?php else: ?>
                            <p class="not-available">Ce livre n'est pas disponible actuellement.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="login-prompt">Pour emprunter ce livre, veuillez <a href="login.php">vous connecter</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="book-reviews">
            <h3>Critiques et avis</h3>
            
            <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                <div class="reviews-list">
                    <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer"><?php echo $review['username']; ?></span>
                                <span class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </span>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <div class="review-content">
                                <?php echo nl2br($review['comment']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Aucune critique pour ce livre. Soyez le premier à donner votre avis !</p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="add-review">
                    <h4>Ajouter une critique</h4>
                    <form action="add-review.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                        
                        <div class="form-group">
                            <label for="rating">Note:</label>
                            <select name="rating" id="rating" required>
                                <option value="5">5 étoiles - Excellent</option>
                                <option value="4">4 étoiles - Très bien</option>
                                <option value="3">3 étoiles - Bien</option>
                                <option value="2">2 étoiles - Moyen</option>
                                <option value="1">1 étoile - Décevant</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="comment">Commentaire:</label>
                            <textarea name="comment" id="comment" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Soumettre ma critique</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="login-prompt">Pour ajouter une critique, veuillez <a href="login.php">vous connecter</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>