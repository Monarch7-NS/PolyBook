<?php
// Script pour répondre à une invitation à un cercle
require_once 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    redirect('connexion_form.php', 'Vous devez être connecté pour répondre à une invitation.', 'error');
}

// Vérifier si les paramètres nécessaires sont fournis
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['action']) || empty($_GET['action'])) {
    redirect('cercles.php', 'Paramètres manquants.', 'error');
}

$invitation_id = intval($_GET['id']);
$action = $_GET['action']; // 'accept' ou 'decline'
$user_id = $_SESSION['user_id'];

// Vérifier si l'invitation existe et est adressée à l'utilisateur actuel
$invitation_sql = "SELECT ci.*, c.id as circle_id, c.name as circle_name 
                  FROM circle_invitation ci 
                  JOIN circle c ON ci.circle_id = c.id 
                  WHERE ci.id = $invitation_id AND ci.invited_user_id = $user_id AND ci.status = 'pending'";
$invitation_result = $conn->query($invitation_sql);

if ($invitation_result->num_rows == 0) {
    redirect('cercles.php', "L'invitation n'existe pas ou a déjà été traitée.", 'error');
}

$invitation = $invitation_result->fetch_assoc();
$circle_id = $invitation['circle_id'];
$circle_name = $invitation['circle_name'];

if ($action === 'accept') {
    // Accepter l'invitation
    
    // Mettre à jour le statut de l'invitation
    $update_sql = "UPDATE circle_invitation SET status = 'accepted' WHERE id = $invitation_id";
    $conn->query($update_sql);
    
    // Ajouter l'utilisateur au cercle
    $member_sql = "INSERT INTO circle_member (created_at, circle_id, user_id) 
                  VALUES (NOW(), $circle_id, $user_id)";
    
    if ($conn->query($member_sql)) {
        redirect('cercle.php?id=' . $circle_id, "Vous avez rejoint le cercle \"$circle_name\" !", 'success');
    } else {
        redirect('cercles.php', "Erreur lors de l'ajout au cercle : " . $conn->error, 'error');
    }
} elseif ($action === 'decline') {
    // Refuser l'invitation
    $update_sql = "UPDATE circle_invitation SET status = 'declined' WHERE id = $invitation_id";
    
    if ($conn->query($update_sql)) {
        redirect('cercles.php', "Vous avez refusé l'invitation au cercle \"$circle_name\".", 'info');
    } else {
        redirect('cercles.php', "Erreur lors du refus de l'invitation : " . $conn->error, 'error');
    }
} else {
    redirect('cercles.php', "Action non reconnue.", 'error');
}
?>