<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - PolyBook</title>
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
    
    // Traiter le formulaire d'inscription
    if (isset($_POST['register'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $grade_id = intval($_POST['grade_id']);
        
        // Vérifier si le nom d'utilisateur existe déjà
        $check_username_sql = "SELECT id FROM user WHERE username = '$username'";
        $check_username_result = $conn->query($check_username_sql);
        
        // Vérifier si l'email existe déjà
        $check_email_sql = "SELECT id FROM user WHERE email = '$email'";
        $check_email_result = $conn->query($check_email_sql);
        
        if ($check_username_result->num_rows > 0) {
            $error = "Ce nom d'utilisateur est déjà utilisé.";
        } elseif ($check_email_result->num_rows > 0) {
            $error = "Cette adresse email est déjà utilisée.";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            // Créer un résumé vide pour l'utilisateur
            $resume_sql = "INSERT INTO resume (created_at, user_id, content, activity_score) 
                          VALUES (NOW(), NULL, '', 0)";
            $conn->query($resume_sql);
            $resume_id = $conn->insert_id;
            
            // En production, utilisez password_hash pour sécuriser le mot de passe
            // Ici on utilise une simplification pour l'exemple
            $hashed_password = 'hashed_' . $password;
            
            // Insérer le nouvel utilisateur
            $insert_sql = "INSERT INTO user (created_at, username, email, password, role, grade_id, resume_id) 
                          VALUES (NOW(), '$username', '$email', '$hashed_password', 'user', $grade_id, $resume_id)";
            
            if ($conn->query($insert_sql)) {
                $user_id = $conn->insert_id;
                
                // Mettre à jour le résumé avec l'ID de l'utilisateur
                $update_resume_sql = "UPDATE resume SET user_id = $user_id WHERE id = $resume_id";
                $conn->query($update_resume_sql);
                
                // Connecter automatiquement l'utilisateur
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                
                redirect('index.php', 'Inscription réussie ! Bienvenue sur PolyBook.', 'success');
            } else {
                $error = "Erreur lors de l'inscription : " . $conn->error;
            }
        }
    }
    ?>

    <main class="container auth-container">
        <div class="auth-form-container">
            <h1>Inscription</h1>
            
            <?php if (!empty($error)): ?>
                <div class="notification error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmez le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label for="grade_id">Statut :</label>
                    <select id="grade_id" name="grade_id" required>
                        <?php
                        $grades_sql = "SELECT * FROM grade ORDER BY id";
                        $grades_result = $conn->query($grades_sql);
                        
                        while ($grade = $grades_result->fetch_assoc()) {
                            echo '<option value="' . $grade['id'] . '">' . securiser($grade['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="register" class="btn primary">S'inscrire</button>
            </form>
            
            <div class="auth-links">
                <p>Vous avez déjà un compte ? <a href="connexion_form.php">Connectez-vous</a></p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>