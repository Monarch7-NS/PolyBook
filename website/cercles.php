<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cercles d'amis - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'utilisateur est connecté
    if (!estConnecte()) {
        redirect('connexion_form.php', 'Vous devez être connecté pour accéder à cette page.', 'error');
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Créer un nouveau cercle
    if (isset($_POST['create_circle'])) {
        $circle_name = $conn->real_escape_string($_POST['circle_name']);
        
        $sql = "INSERT INTO circle (created_at, name, owner_user_id) 
                VALUES (NOW(), '$circle_name', $user_id)";
        
        if ($conn->query($sql)) {
            $circle_id = $conn->insert_id;
            
            // Ajouter le créateur du cercle comme membre
            $member_sql = "INSERT INTO circle_member (created_at, circle_id, user_id) 
                          VALUES (NOW(), $circle_id, $user_id)";
            $conn->query($member_sql);
            
            redirect('cercle.php?id=' . $circle_id, 'Cercle créé avec succès !', 'success');
        } else {
            $error = "Erreur lors de la création du cercle : " . $conn->error;
        }
    }
    
    // Récupérer les cercles dont l'utilisateur est membre
    $circles_sql = "SELECT c.*, u.username as owner_name, COUNT(cm.id) as member_count 
                   FROM circle c 
                   JOIN circle_member cm ON c.id = cm.circle_id 
                   JOIN user u ON c.owner_user_id = u.id 
                   WHERE cm.user_id = $user_id 
                   GROUP BY c.id";
    $circles_result = $conn->query($circles_sql);
    ?>

    <main class="container">
        <h1>Mes cercles d'amis</h1>
        
        <div class="circles-header">
            <p class="intro">
                Les cercles vous permettent de partager vos lectures et de découvrir les avis de vos amis dans des groupes personnalisés.
            </p>
            <button id="create-circle-btn" class="btn primary">
                <i class="fas fa-plus"></i> Créer un nouveau cercle
            </button>
        </div>
        
        <!-- Modal pour créer un cercle -->
        <div id="create-circle-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Créer un nouveau cercle</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="circle_name">Nom du cercle :</label>
                        <input type="text" id="circle_name" name="circle_name" required>
                    </div>
                    <button type="submit" name="create_circle" class="btn primary">Créer</button>
                </form>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="notification error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($circles_result->num_rows > 0): ?>
            <div class="circles-grid">
                <?php while ($circle = $circles_result->fetch_assoc()): ?>
                    <div class="circle-card">
                        <div class="circle-header">
                            <h3><a href="cercle.php?id=<?php echo $circle['id']; ?>"><?php echo securiser($circle['name']); ?></a></h3>
                            <?php if ($circle['owner_user_id'] == $user_id): ?>
                                <span class="owner-badge">Créateur</span>
                            <?php endif; ?>
                        </div>
                        <div class="circle-info">
                            <p><i class="fas fa-user"></i> Créé par <?php echo securiser($circle['owner_name']); ?></p>
                            <p><i class="fas fa-users"></i> <?php echo $circle['member_count']; ?> membre(s)</p>
                            <p><i class="fas fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($circle['created_at'])); ?></p>
                        </div>
                        <div class="circle-actions">
                            <a href="cercle.php?id=<?php echo $circle['id']; ?>" class="btn primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <?php if ($circle['owner_user_id'] == $user_id): ?>
                                <a href="gerer_cercle.php?id=<?php echo $circle['id']; ?>" class="btn secondary">
                                    <i class="fas fa-cog"></i> Gérer
                                </a>
                            <?php else: ?>
                                <a href="quitter_cercle.php?id=<?php echo $circle['id']; ?>" class="btn secondary" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir quitter ce cercle ?');">
                                    <i class="fas fa-sign-out-alt"></i> Quitter
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h2>Vous n'avez pas encore de cercles</h2>
                <p>Créez un cercle pour partager vos lectures avec vos amis ou demandez à rejoindre un cercle existant.</p>
            </div>
        <?php endif; ?>
        
        <!-- Invitations reçues -->
        <?php
        $invitations_sql = "SELECT ci.*, c.name as circle_name, u.username as inviter_name 
                           FROM circle_invitation ci 
                           JOIN circle c ON ci.circle_id = c.id 
                           JOIN user u ON ci.inviter_user_id = u.id 
                           WHERE ci.invited_user_id = $user_id AND ci.status = 'pending'";
        $invitations_result = $conn->query($invitations_sql);
        
        if ($invitations_result->num_rows > 0):
        ?>
            <div class="invitations-section">
                <h2>Invitations reçues</h2>
                <div class="invitations-list">
                    <?php while ($invitation = $invitations_result->fetch_assoc()): ?>
                        <div class="invitation-card">
                            <div class="invitation-info">
                                <p>
                                    <strong><?php echo securiser($invitation['inviter_name']); ?></strong> 
                                    vous invite à rejoindre le cercle 
                                    <strong><?php echo securiser($invitation['circle_name']); ?></strong>
                                </p>
                                <p class="invitation-date">
                                    Invité le <?php echo date('d/m/Y', strtotime($invitation['created_at'])); ?>
                                </p>
                            </div>
                            <div class="invitation-actions">
                                <a href="repondre_invitation.php?id=<?php echo $invitation['id']; ?>&action=accept" class="btn primary">
                                    <i class="fas fa-check"></i> Accepter
                                </a>
                                <a href="repondre_invitation.php?id=<?php echo $invitation['id']; ?>&action=decline" class="btn secondary">
                                    <i class="fas fa-times"></i> Refuser
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal de création de cercle
    const modal = document.getElementById("create-circle-modal");
    const btn = document.getElementById("create-circle-btn");
    const span = document.getElementsByClassName("close")[0];
    
    btn.onclick = function() {
        modal.style.display = "block";
    }
    
    span.onclick = function() {
        modal.style.display = "none";
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>