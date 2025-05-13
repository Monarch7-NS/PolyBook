<?php
// Script pour rejoindre un groupe de lecture
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour rejoindre un groupe.', 'error');
}

// Vérifier si l'ID du groupe est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('groupes_lecture.php', 'Groupe non spécifié.', 'error');
}

$group_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Vérifier si le groupe existe
$group_check_sql = "SELECT id FROM reading_group WHERE id = $group_id";
$group_check_result = $conn->query($group_check_sql);

if ($group_check_result->num_rows == 0) {
    redirect('groupes_lecture.php', 'Groupe non trouvé.', 'error');
}

// Vérifier si l'utilisateur est déjà membre du groupe
$member_check_sql = "SELECT id FROM reading_group_member WHERE reading_group_id = $group_id AND user_id = $user_id";
$member_check_result = $conn->query($member_check_sql);

if ($member_check_result->num_rows > 0) {
    redirect('groupe_lecture.php?id=' . $group_id, 'Vous êtes déjà membre de ce groupe.', 'info');
}

// Ajouter l'utilisateur au groupe
$insert_sql = "INSERT INTO reading_group_member (reading_group_id, user_id, joined_at) 
              VALUES ($group_id, $user_id, NOW())";

if ($conn->query($insert_sql)) {
    redirect('groupe_lecture.php?id=' . $group_id, 'Vous avez rejoint le groupe avec succès !', 'success');
} else {
    redirect('groupes_lecture.php', "Erreur lors de l'ajout au groupe : " . $conn->error, 'error');
}
?>