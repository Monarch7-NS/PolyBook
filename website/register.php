<?php
require_once 'includes/db.php';

$error = '';
$success = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    // Validation des données
    if ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        // Vérifier si le nom d'utilisateur existe déjà
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé';
        } else {
            // Vérifier si l'email existe déjà
            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                $error = 'Cette adresse email est déjà utilisée';
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insérer le nouvel utilisateur
                $query = "INSERT INTO users (username, email, password, full_name) VALUES ('$username', '$email', '$hashed_password', '$full_name')";
                
                if (mysqli_query($conn, $query)) {
                    $success = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
                } else {
                    $error = 'Une erreur est survenue: ' . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Ma Bibliothèque en Ligne</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="form-section">
        <div class="container">
            <h2>Inscription</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Nom complet</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
            
            <p class="form-footer">Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>