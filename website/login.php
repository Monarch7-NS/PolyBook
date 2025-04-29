<?php
require_once 'includes/db.php';

$error = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Vérifier les identifiants
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Vérifier le mot de passe
        if (password_verify($password, $user['password'])) {
            // Connexion réussie, créer une session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Rediriger vers la page d'accueil
            header('Location: index.php');
            exit();
        } else {
            $error = 'Mot de passe incorrect';
        }
    } else {
        $error = 'Nom d\'utilisateur non trouvé';
    }
}
?>


<?php include 'includes/header.php'; ?>

    
    <section class="form-section">
        <div class="container">
            <h2>Connexion</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
            
            <p class="form-footer">Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
