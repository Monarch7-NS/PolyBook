<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PolyBook - Votre bibliothèque partagée</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Intégration de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php'; 
    
    // Afficher les messages de notification s'il y en a
    if (isset($_SESSION['message'])) {
        echo '<div class="notification ' . $_SESSION['message_type'] . '">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <div class="hero">
        <div class="container">
            <h1>Bienvenue sur PolyBook</h1>
            <p>Partagez vos lectures, découvrez de nouveaux livres et rejoignez une communauté de passionnés</p>
            <?php if (!estConnecte()): ?>
                <div class="cta-buttons">
                    <a href="connexion_form.php" class="btn primary">Se connecter</a>
                    <a href="inscription_form.php" class="btn secondary">S'inscrire</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <main class="container">
        <section class="featured-books">
            <h2>Livres à découvrir</h2>
            <div class="books-grid">
                <?php
                // Récupérer les livres approuvés avec leur note moyenne
                $sql = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
                        FROM book b 
                        LEFT JOIN review r ON b.id = r.book_id 
                        WHERE b.is_approved = TRUE 
                        GROUP BY b.id 
                        ORDER BY avg_rating DESC, review_count DESC 
                        LIMIT 6";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $rating = number_format($row['avg_rating'], 1);
                        echo '<div class="book-card">';
                        echo '<a href="livre.php?id=' . $row['id'] . '">';
                        echo '<div class="book-cover">';
                        // Utiliser la couverture basée sur l'ISBN (service Open Library)
                        echo '<img src="https://covers.openlibrary.org/b/isbn/' . $row['isbn'] . '-M.jpg" alt="' . securiser($row['title']) . '">';
                        echo '</div>';
                        echo '<div class="book-info">';
                        echo '<h3>' . securiser($row['title']) . '</h3>';
                        echo '<p class="author">par ' . securiser($row['author']) . '</p>';
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
                    echo '<p>Aucun livre disponible pour le moment.</p>';
                }
                ?>
            </div>
            <div class="center">
                <a href="catalogue.php" class="btn primary">Voir tous les livres</a>
            </div>
        </section>

        <section class="reading-groups">
            <h2>Groupes de lecture actifs</h2>
            <div class="groups-grid">
                <?php
                // Récupérer les groupes de lecture avec leurs livres actuels
                $sql = "SELECT rg.*, b.title as current_book_title, COUNT(rgm.id) as member_count 
                        FROM reading_group rg 
                        LEFT JOIN reading_group_book rgb ON rg.id = rgb.reading_group_id AND CURRENT_DATE BETWEEN rgb.start_date AND rgb.end_date 
                        LEFT JOIN book b ON rgb.book_id = b.id 
                        LEFT JOIN reading_group_member rgm ON rg.id = rgm.reading_group_id 
                        GROUP BY rg.id 
                        ORDER BY member_count DESC 
                        LIMIT 3";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="group-card">';
                        echo '<a href="groupe_lecture.php?id=' . $row['id'] . '">';
                        echo '<h3>' . securiser($row['name']) . '</h3>';
                        echo '<p>' . securiser($row['description']) . '</p>';
                        if ($row['current_book_title']) {
                            echo '<p class="current-read">Lecture en cours : ' . securiser($row['current_book_title']) . '</p>';
                        }
                        echo '<p class="member-count">' . $row['member_count'] . ' membres</p>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun groupe de lecture actif pour le moment.</p>';
                }
                ?>
            </div>
            <div class="center">
                <a href="groupes_lecture.php" class="btn secondary">Tous les groupes</a>
            </div>
        </section>

        <?php if (estConnecte()): ?>
        <section class="user-circles">
            <h2>Vos cercles d'amis</h2>
            <div class="circles-grid">
                <?php
                // Récupérer les cercles de l'utilisateur connecté
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT c.* 
                        FROM circle c 
                        JOIN circle_member cm ON c.id = cm.circle_id 
                        WHERE cm.user_id = $user_id 
                        ORDER BY c.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="circle-card">';
                        echo '<a href="cercle.php?id=' . $row['id'] . '">';
                        echo '<h3>' . securiser($row['name']) . '</h3>';
                        
                        // Compter les membres du cercle
                        $circle_id = $row['id'];
                        $member_sql = "SELECT COUNT(*) as count FROM circle_member WHERE circle_id = $circle_id";
                        $member_result = $conn->query($member_sql);
                        $member_count = $member_result->fetch_assoc()['count'];
                        
                        echo '<p>' . $member_count . ' membres</p>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Vous n\'appartenez à aucun cercle pour le moment.</p>';
                    echo '<p><a href="cercles.php">Créer ou rejoindre un cercle</a></p>';
                }
                ?>
            </div>
            <div class="center">
                <a href="cercles.php" class="btn secondary">Gérer vos cercles</a>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="scripts.js"></script>
</body>
</html>