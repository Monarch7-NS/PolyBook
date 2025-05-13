<?php
// Paramètres de connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "root";
$basededonnees = "polybook";

// Établir la connexion
$conn = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Configuration UTF-8
$conn->set_charset("utf8mb4");

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction utile pour rediriger avec un message
function redirect($url, $message = "", $type = "info") {
    if (!empty($message)) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Fonction pour vérifier si l'utilisateur est connecté
function estConnecte() {
    return isset($_SESSION['user_id']);
}

// Fonction pour protéger contre les injections XSS
function securiser($donnee) {
    return htmlspecialchars($donnee, ENT_QUOTES, 'UTF-8');
}
?>