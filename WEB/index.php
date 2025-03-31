<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PolyBook</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }
        nav {
            background: #444;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }
        main {
            padding: 20px;
        }
        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenue sur PolyBook</h1>
    </header>
    <nav>
        <a href="index.php?page=home">Accueil</a>
        <a href="index.php?page=library">Bibliothèque</a>
        <a href="index.php?page=reviews">Avis</a>
    </nav>
        <!-- filepath: c:\xampp\htdocs\demo\PolyBook\WEB\index.php -->
    <main>
        <?php
            // Charger la page en fonction du paramètre 'page'
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                if ($page === 'home') {
                    echo "
                        <h2>Accueil</h2>
                        <p>Bienvenue sur <strong>PolyBook</strong>, votre bibliothèque en ligne. 
                        Ici, vous pouvez explorer une vaste collection de livres, partager vos avis, 
                        et gérer votre propre bibliothèque personnelle.</p>
                        
                        <h3>Connexion</h3>
                        <form action='index.php?page=login' method='post'>
                            <label for='username'>Nom d'utilisateur :</label><br>
                            <input type='text' id='username' name='username' required><br><br>
                            
                            <label for='password'>Mot de passe :</label><br>
                            <input type='password' id='password' name='password' required><br><br>
                            
                            <button type='submit'>Se connecter</button>
                        </form>
                    ";
                } elseif ($page === 'library') {
                    // Simuler les données des livres
                    $books = [
                        ["title" => "Le Petit Prince", "author" => "Antoine de Saint-Exupéry", "price" => "10.99€", "rating" => "4.8"],
                        ["title" => "1984", "author" => "George Orwell", "price" => "8.99€", "rating" => "4.6"],
                        ["title" => "Harry Potter à l'école des sorciers", "author" => "J.K. Rowling", "price" => "12.99€", "rating" => "4.9"],
                        ["title" => "L'Alchimiste", "author" => "Paulo Coelho", "price" => "9.99€", "rating" => "4.7"]
                    ];

                    echo "<h2>Bibliothèque</h2>";
                    echo "<table border='1' style='width: 100%; border-collapse: collapse; text-align: left;'>";
                    echo "<tr>
                            <th>Nom du Livre</th>
                            <th>Auteur</th>
                            <th>Prix</th>
                            <th>Note</th>
                          </tr>";

                    // Afficher les livres dans un tableau
                    foreach ($books as $book) {
                        echo "<tr>
                                <td>{$book['title']}</td>
                                <td>{$book['author']}</td>
                                <td>{$book['price']}</td>
                                <td>{$book['rating']}</td>
                              </tr>";
                    }

                    echo "</table>";
                } elseif ($page === 'reviews') {
                    echo "<h2>Avis</h2><p>Partagez vos avis sur les livres.</p>";
                } elseif ($page === 'login') {
                    // Traitement de la connexion (exemple simple)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        
                        // Exemple de vérification (à remplacer par une vérification réelle avec une base de données)
                        if ($username === 'admin' && $password === 'password') {
                            echo "<h2>Connexion réussie</h2><p>Bienvenue, $username !</p>";
                        } else {
                            echo "<h2>Échec de la connexion</h2><p>Nom d'utilisateur ou mot de passe incorrect.</p>";
                        }
                    }
                } else {
                    echo "<h2>Page introuvable</h2><p>La page demandée n'existe pas.</p>";
                }
            } else {
                // Page par défaut
                echo "
                    <h2>Accueil</h2>
                    <p>Bienvenue sur <strong>PolyBook</strong>, votre bibliothèque en ligne. 
                    Ici, vous pouvez explorer une vaste collection de livres, partager vos avis, 
                    et gérer votre propre bibliothèque personnelle.</p>
                    
                    <h3>Connexion</h3>
                    <form action='index.php?page=login' method='post'>
                        <label for='username'>Nom d'utilisateur :</label><br>
                        <input type='text' id='username' name='username' required><br><br>
                        
                        <label for='password'>Mot de passe :</label><br>
                        <input type='password' id='password' name='password' required><br><br>
                        
                        <button type='submit'>Se connecter</button>
                    </form>
                ";
            }
        ?>
    </main>
    <footer>
        <p>&copy; 2025 PolyBook. Tous droits réservés.</p>
    </footer>
</body>
</html>