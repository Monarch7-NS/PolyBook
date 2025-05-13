<?php
// Page de déconnexion
require_once 'connexion.php';

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
redirect('index.php', 'Vous avez été déconnecté avec succès.', 'success');
?>