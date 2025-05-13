<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groupes de lecture - PolyBook</title>
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
    
    // Créer un nouveau groupe de lecture
    if (isset($_POST['create_group'])) {
        $group_name = $conn->real_escape_string($_POST['group_name']);
        $group_description = $conn->real_escape_string($_POST['group_description']);
        
        $sql = "INSERT INTO reading_group (created_at, name, description, creator_user_id) 
                VALUES (NOW(), '$group_name', '$group_description', $user_id)";
        
        if ($conn->query($sql)) {
            $group_id = $conn->insert_id;
            
            // Ajouter le créateur du groupe comme membre
            $member_sql = "INSERT INTO reading_group_member (reading_group_id, user_id, joined_at) 
                          VALUES ($group_id, $user_id, NOW())";
            $conn->query($member_sql);
            
            redirect('groupe_lecture.php?id=' . $group_id, 'Groupe créé avec succès !', 'success');
        } else {
            $error = "Erreur lors de la création du groupe : " . $conn->error;
        }
    }
    
    // Récupérer tous les groupes de lecture
    $all_groups_sql = "SELECT rg.*, COUNT(rgm.id) as member_count, 
                      (SELECT COUNT(*) FROM reading_group_member WHERE reading_group_id = rg.id AND user_id = $user_id) as is_member 
                      FROM reading_group rg 
                      LEFT JOIN reading_group_member rgm ON rg.id = rgm.reading_group_id 
                      GROUP BY rg.id 
                      ORDER BY is_member DESC, member_count DESC";
    $all_groups_result = $conn->query($all_groups_sql);
    
    // Récupérer les groupes dont l'utilisateur est membre
    $my_groups_sql = "SELECT rg.*, COUNT(rgm.id) as member_count, 
                     (rg.creator_user_id = $user_id) as is_creator 
                     FROM reading_group rg 
                     JOIN reading_group_member rgm ON rg.id = rgm.reading_group_id 
                     WHERE rgm.user_id = $user_id 
                     GROUP BY rg.id 
                     ORDER BY is_creator DESC, rg.created_at DESC";
    $my_groups_result = $conn->query($my_groups_sql);
    ?>

    <main class="container">
        <h1>Groupes de lecture</h1>
        
        <div class="groups-tabs">
            <button class="tab-button active" data-tab="my-groups">Mes groupes</button>
            <button class="tab-button" data-tab="all-groups">Tous les groupes</button>
            <button id="create-group-btn" class="btn primary">
                <i class="fas fa-plus"></i> Créer un groupe
            </button>
        </div>
        
        <!-- Modal pour créer un groupe -->
        <div id="create-group-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Créer un nouveau groupe de lecture</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="group_name">Nom du groupe :</label>
                        <input type="text" id="group_name" name="group_name" required>
                    </div>
                    <div class="form-group">
                        <label for="group_description">Description :</label>
                        <textarea id="group_description" name="group_description" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="create_group" class="btn primary">Créer le groupe</button>
                </form>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="notification error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="tab-content active" id="my-groups">
            <?php if ($my_groups_result->num_rows > 0): ?>
                <div class="groups-grid">
                    <?php while ($group = $my_groups_result->fetch_assoc()): ?>
                        <div class="group-card">
                            <div class="group-header">
                                <h3><a href="groupe_lecture.php?id=<?php echo $group['id']; ?>"><?php echo securiser($group['name']); ?></a></h3>
                                <?php if ($group['is_creator']): ?>
                                    <span class="creator-badge">Créateur</span>
                                <?php endif; ?>
                            </div>
                            <div class="group-info">
                                <p class="group-description"><?php echo securiser(substr($group['description'], 0, 150)); ?>...</p>
                                <p><i class="fas fa-users"></i> <?php echo $group['member_count']; ?> membre(s)</p>
                                <p><i class="fas fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($group['created_at'])); ?></p>
                                
                                <?php
                                // Récupérer le livre en cours de lecture
                                $current_book_sql = "SELECT b.title, b.id, rgb.start_date, rgb.end_date 
                                                   FROM reading_group_book rgb 
                                                   JOIN book b ON rgb.book_id = b.id 
                                                   WHERE rgb.reading_group_id = {$group['id']} 
                                                   AND CURRENT_DATE BETWEEN rgb.start_date AND rgb.end_date";
                                $current_book_result = $conn->query($current_book_sql);
                                
                                if ($current_book_result->num_rows > 0) {
                                    $current_book = $current_book_result->fetch_assoc();
                                    echo '<div class="current-book">';
                                    echo '<p><strong>Lecture en cours :</strong></p>';
                                    echo '<p><a href="livre.php?id=' . $current_book['id'] . '">' . securiser($current_book['title']) . '</a></p>';
                                    echo '<p class="book-dates">Du ' . date('d/m/Y', strtotime($current_book['start_date'])) . ' au ' . date('d/m/Y', strtotime($current_book['end_date'])) . '</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="group-actions">
                                <a href="groupe_lecture.php?id=<?php echo $group['id']; ?>" class="btn primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <?php if ($group['is_creator']): ?>
                                    <a href="gerer_groupe.php?id=<?php echo $group['id']; ?>" class="btn secondary">
                                        <i class="fas fa-cog"></i> Gérer
                                    </a>
                                <?php else: ?>
                                    <a href="quitter_groupe.php?id=<?php echo $group['id']; ?>" class="btn secondary" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir quitter ce groupe ?');">
                                        <i class="fas fa-sign-out-alt"></i> Quitter
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-reader"></i>
                    <h2>Vous n'êtes membre d'aucun groupe de lecture</h2>
                    <p>Rejoignez un groupe existant ou créez-en un nouveau pour partager vos lectures avec d'autres utilisateurs.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="tab-content" id="all-groups">
            <?php if ($all_groups_result->num_rows > 0): ?>
                <div class="groups-grid">
                    <?php while ($group = $all_groups_result->fetch_assoc()): ?>
                        <div class="group-card <?php echo $group['is_member'] ? 'is-member' : ''; ?>">
                            <div class="group-header">
                                <h3><a href="groupe_lecture.php?id=<?php echo $group['id']; ?>"><?php echo securiser($group['name']); ?></a></h3>
                                <?php if ($group['is_member']): ?>
                                    <span class="member-badge">Membre</span>
                                <?php endif; ?>
                            </div>
                            <div class="group-info">
                                <p class="group-description"><?php echo securiser(substr($group['description'], 0, 150)); ?>...</p>
                                <p><i class="fas fa-users"></i> <?php echo $group['member_count']; ?> membre(s)</p>
                                <p><i class="fas fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($group['created_at'])); ?></p>
                                
                                <?php
                                // Récupérer le livre en cours de lecture
                                $current_book_sql = "SELECT b.title, b.id, rgb.start_date, rgb.end_date 
                                                   FROM reading_group_book rgb 
                                                   JOIN book b ON rgb.book_id = b.id 
                                                   WHERE rgb.reading_group_id = {$group['id']} 
                                                   AND CURRENT_DATE BETWEEN rgb.start_date AND rgb.end_date";
                                $current_book_result = $conn->query($current_book_sql);
                                
                                if ($current_book_result->num_rows > 0) {
                                    $current_book = $current_book_result->fetch_assoc();
                                    echo '<div class="current-book">';
                                    echo '<p><strong>Lecture en cours :</strong></p>';
                                    echo '<p><a href="livre.php?id=' . $current_book['id'] . '">' . securiser($current_book['title']) . '</a></p>';
                                    echo '<p class="book-dates">Du ' . date('d/m/Y', strtotime($current_book['start_date'])) . ' au ' . date('d/m/Y', strtotime($current_book['end_date'])) . '</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="group-actions">
                                <a href="groupe_lecture.php?id=<?php echo $group['id']; ?>" class="btn primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <?php if (!$group['is_member']): ?>
                                    <a href="rejoindre_groupe.php?id=<?php echo $group['id']; ?>" class="btn secondary">
                                        <i class="fas fa-sign-in-alt"></i> Rejoindre
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-book-reader"></i>
                    <h2>Aucun groupe de lecture disponible</h2>
                    <p>Soyez le premier à créer un groupe de lecture !</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour les onglets
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activer l'onglet sélectionné
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Script pour le modal de création de groupe
    const modal = document.getElementById("create-group-modal");
    const btn = document.getElementById("create-group-btn");
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