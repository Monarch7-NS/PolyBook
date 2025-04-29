<?php
// Informations de connexion à la base de données
$host = 'localhost';
$username = 'root';
$password = ''; // Généralement vide pour XAMPP par défaut
$database = 'library_db';

// Établir la connexion
$conn = mysqli_connect($host, $username, $password, $database);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}

// Définir l'encodage UTF-8
mysqli_set_charset($conn, "utf8");

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>