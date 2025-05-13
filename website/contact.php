<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    $success = false;
    
    // Traiter le formulaire de contact
    if (isset($_POST['send_message'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $subject = $conn->real_escape_string($_POST['subject']);
        $message = $conn->real_escape_string($_POST['message']);
        
        // En production, vous enverriez un e-mail ici
        // Pour l'exemple, nous allons simplement simuler une réussite
        $success = true;
    }
    ?>

    <main class="container">
        <div class="contact-page">
            <h1>Contactez-nous</h1>
            
            <?php if ($success): ?>
                <div class="notification success">
                    <p>Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.</p>
                </div>
            <?php else: ?>
                <div class="contact-content">
                    <div class="contact-info">
                        <h2>Informations de contact</h2>
                        <p>Vous avez des questions ou des suggestions ? N'hésitez pas à nous contacter !</p>
                        
                        <div class="contact-details">
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>contact@polybook.fr</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Polytech Annecy-Chambéry</span>
                            </div>
                        </div>
                        
                        <div class="contact-social">
                            <h3>Suivez-nous</h3>
                            <div class="social-links">
                                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-form-container">
                        <h2>Envoyez-nous un message</h2>
                        <form method="POST" action="" class="contact-form">
                            <div class="form-group">
                                <label for="name">Nom complet :</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Adresse email :</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Sujet :</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message :</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" name="send_message" class="btn primary">Envoyer le message</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>