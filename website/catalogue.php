<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue de livres - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    ?>

    <main class="container">
        <h1>Catalogue de livres</h1>

        <div class="filter-section">
            <form action="catalogue.php" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" placeholder="Titre, auteur, ISBN..." 
                           value="<?php echo isset($_GET['search']) ? securiser($_GET['search']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre">
                        <option value="">Tous les genres</option>
                        <?php
                        $genre_sql = "SELECT * FROM genre ORDER BY name";
                        $genre_result = $conn->query($genre_sql);
                        while ($genre = $genre_result->fetch_assoc()) {
                            $selected = (isset($_GET['genre']) && $_GET['genre'] == $genre['id']) ? 'selected' : '';
                            echo '<option value="' . $genre['id'] . '" ' . $selected . '>' . securiser($genre['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="language">Langue</label>
                    <select id="language" name="language">
                        <option value="">Toutes les langues</option>
                        <?php
                        $lang_sql = "SELECT DISTINCT language FROM book WHERE language IS NOT NULL ORDER BY language";
                        $lang_result = $conn->query($lang_sql);
                        while ($language = $lang_result->fetch_assoc()) {
                            $selected = (isset($_GET['language']) && $_GET['language'] == $language['language']) ? 'selected' : '';
                            echo '<option value="' . $language['language'] . '" ' . $selected . '>' . securiser($language['language']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sort">Trier par</label>
                    <select id="sort" name="sort">
                        <option value="title_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title_asc') ? 'selected' : ''; ?>>Titre (A-Z)</option>
                        <option value="title_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title_desc') ? 'selected' : ''; ?>>Titre (Z-A)</option>
                        <option value="author_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'author_asc') ? 'selected' : ''; ?>>Auteur (A-Z)</option>
                        <option value="author_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'author_desc') ? 'selected' : ''; ?>>Auteur (Z-A)</option>
                        <option value="year_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'year_desc') ? 'selected' : ''; ?>>Année (récent)</option>
                        <option value="year_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'year_asc') ? 'selected' : ''; ?>>Année (ancien)</option>
                        <option value="rating_desc" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'rating_desc') ? 'selected' : ''; ?>>Note (meilleure)</option>
                        <option value="rating_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating_asc') ? 'selected' : ''; ?>>Note (moins bonne)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn primary">Filtrer</button>
                    <a href="catalogue.php" class="btn secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="books-grid">
            <?php
            // Construction de la requête SQL avec les filtres
            $sql = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
                    FROM book b 
                    LEFT JOIN review r ON b.id = r.book_id";
            
            // Jointure si filtre par genre
            if (isset($_GET['genre']) && !empty($_GET['genre'])) {
                $sql .= " JOIN book_genre bg ON b.id = bg.book_id";
                $where_conditions[] = "bg.genre_id = " . intval($_GET['genre']);
            }
            
            $where_conditions = ["b.is_approved = TRUE"];
            
            // Filtre de recherche
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $conn->real_escape_string($_GET['search']);
                $where_conditions[] = "(b.title LIKE '%$search%' OR b.author LIKE '%$search%' OR b.isbn LIKE '%$search%')";
            }
            
            // Filtre par langue
            if (isset($_GET['language']) && !empty($_GET['language'])) {
                $language = $conn->real_escape_string($_GET['language']);
                $where_conditions[] = "b.language = '$language'";
            }
            
            // Ajout des conditions WHERE
            if (!empty($where_conditions)) {
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }
            
            // Regroupement par livre
            $sql .= " GROUP BY b.id";
            
            // Tri
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'rating_desc';
            switch ($sort) {
                case 'title_asc':
                    $sql .= " ORDER BY b.title ASC";
                    break;
                case 'title_desc':
                    $sql .= " ORDER BY b.title DESC";
                    break;
                case 'author_asc':
                    $sql .= " ORDER BY b.author ASC";
                    break;
                case 'author_desc':
                    $sql .= " ORDER BY b.author DESC";
                    break;
                case 'year_desc':
                    $sql .= " ORDER BY b.publication_year DESC";
                    break;
                case 'year_asc':
                    $sql .= " ORDER BY b.publication_year ASC";
                    break;
                case 'rating_asc':
                    $sql .= " ORDER BY avg_rating ASC, review_count DESC";
                    break;
                case 'rating_desc':
                default:
                    $sql .= " ORDER BY avg_rating DESC, review_count DESC";
                    break;
            }
            
            // Pagination
            $results_per_page = 12;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max(1, $page); // S'assurer que la page est au moins 1
            $offset = ($page - 1) * $results_per_page;
            
            $sql .= " LIMIT $offset, $results_per_page";
            
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rating = is_null($row['avg_rating']) ? 0 : number_format($row['avg_rating'], 1);
                    echo '<div class="book-card">';
                    echo '<a href="livre.php?id=' . $row['id'] . '">';
                    echo '<div class="book-cover">';
                    echo '<img src="https://covers.openlibrary.org/b/isbn/' . $row['isbn'] . '-M.jpg" alt="' . securiser($row['title']) . '">';
                    echo '</div>';
                    echo '<div class="book-info">';
                    echo '<h3>' . securiser($row['title']) . '</h3>';
                    echo '<p class="author">par ' . securiser($row['author']) . '</p>';
                    echo '<p class="year">(' . $row['publication_year'] . ')</p>';
                    echo '<div class="rating">';
                    
                    // Afficher les étoiles
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($i - 0.5 <= $rating) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    
                    echo ' <span>' . $rating . ' (' . $row['review_count'] . ' avis)</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo '<p class="no-results">Aucun livre ne correspond à vos critères de recherche.</p>';
            }
            ?>
        </div>

        <?php
        // Pagination
        // Calculer le nombre total de livres pour la pagination
        $count_sql = "SELECT COUNT(DISTINCT b.id) as total FROM book b";
        
        // Jointure si filtre par genre
        if (isset($_GET['genre']) && !empty($_GET['genre'])) {
            $count_sql .= " JOIN book_genre bg ON b.id = bg.book_id";
        }
        
        // Ajout des conditions WHERE pour le comptage
        if (!empty($where_conditions)) {
            $count_sql .= " WHERE " . implode(" AND ", $where_conditions);
        }
        
        $count_result = $conn->query($count_sql);
        $total_books = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_books / $results_per_page);
        
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            
            // Bouton précédent
            if ($page > 1) {
                $prev_url = http_build_query(array_merge($_GET, ['page' => $page - 1]));
                echo '<a href="?'. $prev_url .'" class="pagination-button">&laquo; Précédent</a>';
            }
            
            // Pages numérotées
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = $i == $page ? 'active' : '';
                $page_url = http_build_query(array_merge($_GET, ['page' => $i]));
                echo '<a href="?'. $page_url .'" class="pagination-button '. $active .'">' . $i . '</a>';
            }
            
            // Bouton suivant
            if ($page < $total_pages) {
                $next_url = http_build_query(array_merge($_GET, ['page' => $page + 1]));
                echo '<a href="?'. $next_url .'" class="pagination-button">Suivant &raquo;</a>';
            }
            
            echo '</div>';
        }
        ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="scripts.js"></script>
</body>
</html>