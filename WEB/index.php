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
        /* Style pour la barre de filtre */
        #filtre {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        #filtre h2 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
        }
        #filtre label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        #filtre select,
        #filtre input[type="text"],
        #filtre button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        #filtre button {
            background-color: #444;
            color: #fff;
            cursor: pointer;
            border: none;
        }
        #filtre button:hover {
            background-color: #333;
        }
        #library {
            display: flex;
            flex-wrap: wrap; /* Permet de passer à la ligne si nécessaire */
            gap: 20px; /* Espacement entre les livres */
            justify-content: center; /* Centre les livres horizontalement */
        }

        .book {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            width: 200px; /* Largeur fixe pour chaque livre */
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .book img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .book p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
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

                    echo "<div id=\"filtre\"> 
                            <h2>Filtrer les livres</h2>
                            <form action=\"index.php?page=library\" method=\"get\">
                                <label for=\"genre\">Genre :</label>
                                <select id=\"genre\" name=\"genre\">
                                    <option value=\"\">Tous</option>
                                    <option value=\"fiction\">Fiction</option>
                                    <option value=\"non-fiction\">Non-Fiction</option>
                                    <option value=\"fantasy\">Fantasy</option>
                                    <option value=\"science\">Science</option>
                                </select>

                                <label for=\"author\">Auteur :</label>
                                <input type=\"text\" id=\"author\" name=\"author\" placeholder=\"Rechercher par auteur...\">

                                <button type=\"submit\">Appliquer le filtre</button>
                            </form>
                        </div>";

                    
                     echo "<section id=\"library\">";
                    // Afficher les livres dans un tableau
                    foreach ($books as $book) {
                        

                        echo "
                              <div class=\"book\">
                              <img src=\"placeholder.jpg\" alt=\"Image du livre\" />
                              <p>Nom du livre : {$book['title']}</p>
                              <p>Auteur : {$book['author']}</p>
                              <p>Prix : {$book['price']}</p>
                                <p>Note : {$book['rating']}</p>
                              </div>
                              ";
                    }

                    echo "</section>";
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