<?php
// Script pour quitter un groupe de lecture
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour quitter un groupe.', 'error');
}

// Vérifier si l'ID du groupe est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('groupes_lecture.php', 'Groupe non spécifié.', 'error');
}

$group_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si le groupe existe
$group_check_sql = "SELECT creator_user_id FROM reading_group WHERE id = $group_id";
$group_check_result = $conn->query($group_check_sql);

if ($group_check_result->num_rows == 0) {
    redirect('groupes_lecture.php', 'Groupe non trouvé.', 'error');
}

$group = $group_check_result->fetch_assoc();

// Vérifier si l'utilisateur est le créateur du groupe
if ($group['creator_user_id'] == $user_id) {
    redirect('groupe_lecture.php?id=' . $group_id, 'En tant que créateur, vous ne pouvez pas quitter le groupe. Vous pouvez le supprimer ou transférer sa propriété.', 'error');
}

// Vérifier si l'utilisateur est membre du groupe
$member_check_sql = "SELECT id FROM reading_group_member WHERE reading_group_id = $group_id AND user_id = $user_id";
$member_check_result = $conn->query($member_check_sql);

if ($member_check_result->num_rows == 0) {
    redirect('groupes_lecture.php', "Vous n'êtes pas membre de ce groupe.", 'error');
}

// Retirer l'utilisateur du groupe
$delete_sql = "DELETE FROM reading_group_member WHERE reading_group_id = $group_id AND user_id = $user_id";

if ($conn->query($delete_sql)) {
    redirect('groupes_lecture.php', 'Vous avez quitté le groupe avec succès.', 'success');
} else {
    redirect('groupe_lecture.php?id=' . $group_id, "Erreur lors du retrait du groupe : " . $conn->error, 'error');
}
?>