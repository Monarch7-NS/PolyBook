<?php
// Script pour supprimer une liste de lecture
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour supprimer une liste.', 'error');
}

// Vérifier si l'ID de la liste est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('listes.php', 'Liste non spécifiée.', 'error');
}

$list_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si la liste existe et appartient à l'utilisateur connecté
$list_check_sql = "SELECT id FROM reading_list WHERE id = $list_id AND user_id = $user_id";
$list_check_result = $conn->query($list_check_sql);

if ($list_check_result->num_rows == 0) {
    redirect('listes.php', "Cette liste n'existe pas ou ne vous appartient pas.", 'error');
}

// Supprimer d'abord les livres de la liste
$delete_books_sql = "DELETE FROM reading_list_book WHERE reading_list_id = $list_id";
$conn->query($delete_books_sql);

// Supprimer la liste
$delete_list_sql = "DELETE FROM reading_list WHERE id = $list_id";

if ($conn->query($delete_list_sql)) {
    redirect('listes.php', 'Liste supprimée avec succès.', 'success');
} else {
    redirect('liste.php?id=' . $list_id, "Erreur lors de la suppression de la liste : " . $conn->error, 'error');
}
?>