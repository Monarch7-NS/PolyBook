<?php
// Script pour supprimer un avis
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour supprimer un avis.', 'error');
}

// Vérifier si l'ID de l'avis est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('profil.php', 'Avis non spécifié.', 'error');
}

$review_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si l'avis existe et appartient à l'utilisateur connecté
$review_check_sql = "SELECT r.id, r.book_id FROM review r WHERE r.id = $review_id AND r.user_id = $user_id";
$review_check_result = $conn->query($review_check_sql);

if ($review_check_result->num_rows == 0) {
    redirect('profil.php', "Cet avis n'existe pas ou ne vous appartient pas.", 'error');
}

$review = $review_check_result->fetch_assoc();
$book_id = $review['book_id'];

// Supprimer d'abord les commentaires liés à l'avis
$delete_comments_sql = "DELETE FROM comment WHERE review_id = $review_id";
$conn->query($delete_comments_sql);

// Supprimer l'avis
$delete_review_sql = "DELETE FROM review WHERE id = $review_id";

if ($conn->query($delete_review_sql)) {
    redirect('livre.php?id=' . $book_id, 'Avis supprimé avec succès.', 'success');
} else {
    redirect('livre.php?id=' . $book_id, "Erreur lors de la suppression de l'avis : " . $conn->error, 'error');
}
?>