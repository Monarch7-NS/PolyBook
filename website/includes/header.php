<!-- header.php -->
<?php require_once 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Ma Bibliothèque en Ligne</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Ma Bibliothèque en Ligne</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="books.php">Livres</a></li>
                    <li><a href="categories.php">Catégories</a></li>
                    <li><a href="about.php">À propos</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="register.php">Inscription</a></li>
                    <?php else: ?>
                        <li><a href="dashboard.php">Mon Compte</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
