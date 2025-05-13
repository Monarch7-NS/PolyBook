<?php
// Script pour supprimer un commentaire
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour supprimer un commentaire.', 'error');
}

// Vérifier si l'ID du commentaire est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php', 'Commentaire non spécifié.', 'error');
}

$comment_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si le commentaire existe et appartient à l'utilisateur connecté
$comment_check_sql = "SELECT c.id, c.review_id FROM comment c WHERE c.id = $comment_id AND c.user_id = $user_id";
$comment_check_result = $conn->query($comment_check_sql);

if ($comment_check_result->num_rows == 0) {
    redirect('index.php', "Ce commentaire n'existe pas ou ne vous appartient pas.", 'error');
}

$comment = $comment_check_result->fetch_assoc();
$review_id = $comment['review_id'];

// Supprimer le commentaire
$delete_comment_sql = "DELETE FROM comment WHERE id = $comment_id";

if ($conn->query($delete_comment_sql)) {
    redirect('commentaires.php?review=' . $review_id, 'Commentaire supprimé avec succès.', 'success');
} else {
    redirect('commentaires.php?review=' . $review_id, "Erreur lors de la suppression du commentaire : " . $conn->error, 'error');
}
?>