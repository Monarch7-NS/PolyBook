<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Rediriger si déjà connecté
    if (estConnecte()) {
        redirect('index.php', 'Vous êtes déjà connecté.');
    }
    
    $error = '';
    
    // Traiter le formulaire de connexion
    if (isset($_POST['login'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        
        $sql = "SELECT * FROM user WHERE username = '$username'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Vérification simplifiée du mot de passe (en production, utilisez password_hash/password_verify)
            if ($user['password'] === 'hashed_' . $password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                redirect('index.php', 'Connexion réussie !', 'success');
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Nom d'utilisateur inconnu.";
        }
    }
    ?>

    <main class="container auth-container">
        <div class="auth-form-container">
            <h1>Connexion</h1>
            
            <?php if (!empty($error)): ?>
                <div class="notification error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn primary">Se connecter</button>
            </form>
            
            <div class="auth-links">
                <p>Vous n'avez pas de compte ? <a href="inscription_form.php">Inscrivez-vous</a></p>
                <p><a href="recuperation_mdp.php">Mot de passe oublié ?</a></p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>