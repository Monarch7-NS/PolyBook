<?php
// Script pour traiter l'ajout d'un commentaire
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour commenter.', 'error');
}

// Vérifier si les données nécessaires sont présentes
if (!isset($_POST['review_id']) || !isset($_POST['book_id']) || !isset($_POST['content'])) {
    redirect('index.php', 'Données manquantes pour ajouter un commentaire.', 'error');
}

$user_id = $_SESSION['user_id'];
$review_id = intval($_POST['review_id']);
$book_id = intval($_POST['book_id']);
$content = $conn->real_escape_string($_POST['content']);

// Vérifier si la review existe
$review_check_sql = "SELECT id FROM review WHERE id = $review_id AND book_id = $book_id";
$review_check_result = $conn->query($review_check_sql);

if ($review_check_result->num_rows == 0) {
    redirect("livre.php?id=$book_id", "L'avis commenté n'existe pas.", 'error');
}

// Insérer le commentaire
$insert_sql = "INSERT INTO comment (created_at, user_id, review_id, book_id, content, comment_id) 
              VALUES (NOW(), $user_id, $review_id, $book_id, '$content', NULL)";

if ($conn->query($insert_sql)) {
    redirect("livre.php?id=$book_id#review-$review_id", "Commentaire ajouté avec succès.", 'success');
} else {
    redirect("livre.php?id=$book_id", "Erreur lors de l'ajout du commentaire : " . $conn->error, 'error');
}
?>