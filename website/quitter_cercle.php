<?php
// Script pour quitter un cercle d'amis
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour quitter un cercle.', 'error');
}

// Vérifier si l'ID du cercle est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('cercles.php', 'Cercle non spécifié.', 'error');
}

$circle_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si le cercle existe
$circle_check_sql = "SELECT owner_user_id FROM circle WHERE id = $circle_id";
$circle_check_result = $conn->query($circle_check_sql);

if ($circle_check_result->num_rows == 0) {
    redirect('cercles.php', 'Cercle non trouvé.', 'error');
}

$circle = $circle_check_result->fetch_assoc();

// Vérifier si l'utilisateur est le propriétaire du cercle
if ($circle['owner_user_id'] == $user_id) {
    redirect('cercle.php?id=' . $circle_id, 'En tant que propriétaire, vous ne pouvez pas quitter le cercle. Vous pouvez le supprimer ou transférer sa propriété.', 'error');
}

// Vérifier si l'utilisateur est membre du cercle
$member_check_sql = "SELECT id FROM circle_member WHERE circle_id = $circle_id AND user_id = $user_id";
$member_check_result = $conn->query($member_check_sql);

if ($member_check_result->num_rows == 0) {
    redirect('cercles.php', "Vous n'êtes pas membre de ce cercle.", 'error');
}

// Retirer l'utilisateur du cercle
$delete_sql = "DELETE FROM circle_member WHERE circle_id = $circle_id AND user_id = $user_id";

if ($conn->query($delete_sql)) {
    redirect('cercles.php', 'Vous avez quitté le cercle avec succès.', 'success');
} else {
    redirect('cercle.php?id=' . $circle_id, "Erreur lors du retrait du cercle : " . $conn->error, 'error');
}
?>