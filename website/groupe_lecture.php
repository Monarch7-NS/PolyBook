<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groupe de lecture - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'ID du groupe est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        redirect('groupes_lecture.php', 'Groupe non spécifié.', 'error');
    }
    
    $group_id = intval($_GET['id']);
    
    // Récupérer les détails du groupe
    $group_sql = "SELECT rg.*, u.username as creator_name 
                 FROM reading_group rg 
                 JOIN user u ON rg.creator_user_id = u.id 
                 WHERE rg.id = $group_id";
    $group_result = $conn->query($group_sql);
    
    if ($group_result->num_rows == 0) {
        redirect('groupes_lecture.php', 'Groupe non trouvé.', 'error');
    }
    
    $group = $group_result->fetch_assoc();
    $is_creator = estConnecte() && $group['creator_user_id'] == $_SESSION['user_id'];
    $is_member = false;
    
    if (estConnecte()) {
        $user_id = $_SESSION['user_id'];
        $member_check_sql = "SELECT id FROM reading_group_member WHERE reading_group_id = $group_id AND user_id = $user_id";
        $member_check_result = $conn->query($member_check_sql);
        $is_member = ($member_check_result->num_rows > 0);
    }
    
    // Ajouter un livre au programme de lecture
    if ($is_creator && isset($_POST['add_reading'])) {
        $book_id = intval($_POST['book_id']);
        $start_date = $conn->real_escape_string($_POST['start_date']);
        $end_date = $conn->real_escape_string($_POST['end_date']);
        
        // Vérifier si la période se chevauche avec une autre lecture
        $overlap_check_sql = "SELECT id FROM reading_group_book 
                            WHERE reading_group_id = $group_id 
                            AND (
                                ('$start_date' BETWEEN start_date AND end_date) 
                                OR ('$end_date' BETWEEN start_date AND end_date) 
                                OR (start_date BETWEEN '$start_date' AND '$end_date') 
                                OR (end_date BETWEEN '$start_date' AND '$end_date')
                            )";
        $overlap_check_result = $conn->query($overlap_check_sql);
        
        if ($overlap_check_result->num_rows > 0) {
            $message = "La période sélectionnée chevauche une autre lecture déjà programmée.";
            $message_type = "error";
        } else {
            $add_reading_sql = "INSERT INTO reading_group_book (reading_group_id, book_id, start_date, end_date) 
                              VALUES ($group_id, $book_id, '$start_date', '$end_date')";
            
            if ($conn->query($add_reading_sql)) {
                $message = "Livre ajouté au programme de lecture !";
                $message_type = "success";
            } else {
                $message = "Erreur lors de l'ajout du livre : " . $conn->error;
                $message_type = "error";
            }
        }
    }
    
    // Récupérer les membres du groupe
    $members_sql = "SELECT u.id, u.username, u.email, g.name as grade_name, 
                   (u.id = rg.creator_user_id) as is_creator 
                   FROM reading_group_member rgm 
                   JOIN user u ON rgm.user_id = u.id 
                   JOIN reading_group rg ON rgm.reading_group_id = rg.id 
                   LEFT JOIN grade g ON u.grade_id = g.id 
                   WHERE rgm.reading_group_id = $group_id 
                   ORDER BY is_creator DESC, u.username";
    $members_result = $conn->query($members_sql);
    
    // Récupérer les lectures passées, actuelles et futures
    $current_date = date('Y-m-d');
    
    // Lecture actuelle
    $current_reading_sql = "SELECT rgb.*, b.title, b.author, b.isbn, b.description, b.id as book_id 
                           FROM reading_group_book rgb 
                           JOIN book b ON rgb.book_id = b.id 
                           WHERE rgb.reading_group_id = $group_id 
                           AND '$current_date' BETWEEN rgb.start_date AND rgb.end_date 
                           ORDER BY rgb.start_date DESC 
                           LIMIT 1";
    $current_reading_result = $conn->query($current_reading_sql);
    
    // Lectures à venir
    $upcoming_readings_sql = "SELECT rgb.*, b.title, b.author, b.isbn 
                            FROM reading_group_book rgb 
                            JOIN book b ON rgb.book_id = b.id 
                            WHERE rgb.reading_group_id = $group_id 
                            AND rgb.start_date > '$current_date' 
                            ORDER BY rgb.start_date ASC";
    $upcoming_readings_result = $conn->query($upcoming_readings_sql);
    
    // Lectures passées
    $past_readings_sql = "SELECT rgb.*, b.title, b.author, b.isbn 
                         FROM reading_group_book rgb 
                         JOIN book b ON rgb.book_id = b.id 
                         WHERE rgb.reading_group_id = $group_id 
                         AND rgb.end_date < '$current_date' 
                         ORDER BY rgb.end_date DESC";
    $past_readings_result = $conn->query($past_readings_sql);
    
    // Récupérer les discussions actuelles
    $discussions_sql = "SELECT d.*, u.username, COUNT(dr.id) as reply_count 
                       FROM discussion d 
                       JOIN user u ON d.user_id = u.id 
                       LEFT JOIN discussion_reply dr ON d.id = dr.discussion_id 
                       WHERE d.reading_group_id = $group_id 
                       GROUP BY d.id 
                       ORDER BY d.created_at DESC 
                       LIMIT 5";
    $discussions_result = $conn->query($discussions_sql);
    ?>

    <main class="container">
        <div class="group-details">
            <div class="group-header">
                <h1><?php echo securiser($group['name']); ?></h1>
                <div class="group-meta">
                    <p><i class="fas fa-user"></i> Créé par <?php echo securiser($group['creator_name']); ?></p>
                    <p><i class="fas fa-calendar"></i> Créé le <?php echo date('d/m/Y', strtotime($group['created_at'])); ?></p>
                    <?php
                    // Compter les membres
                    $member_count_sql = "SELECT COUNT(*) as count FROM reading_group_member WHERE reading_group_id = $group_id";
                    $member_count_result = $conn->query($member_count_sql);
                    $member_count = $member_count_result->fetch_assoc()['count'];
                    ?>
                    <p><i class="fas fa-users"></i> <?php echo $member_count; ?> membre(s)</p>
                </div>
                
                <div class="group-description">
                    <p><?php echo nl2br(securiser($group['description'])); ?></p>
                </div>
                
                <div class="group-actions">
                    <?php if (estConnecte()): ?>
                        <?php if ($is_member): ?>
                            <?php if ($is_creator): ?>
                                <a href="gerer_groupe.php?id=<?php echo $group_id; ?>" class="btn secondary">
                                    <i class="fas fa-cog"></i> Gérer le groupe
                                </a>
                            <?php else: ?>
                                <a href="quitter_groupe.php?id=<?php echo $group_id; ?>" class="btn secondary" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir quitter ce groupe ?');">
                                    <i class="fas fa-sign-out-alt"></i> Quitter le groupe
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="rejoindre_groupe.php?id=<?php echo $group_id; ?>" class="btn primary">
                                <i class="fas fa-sign-in-alt"></i> Rejoindre le groupe
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="connexion_form.php" class="btn primary">
                            <i class="fas fa-sign-in-alt"></i> Se connecter pour rejoindre
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isset($message)): ?>
                <div class="notification <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="group-content">
                <div class="readings-section">
                    <h2>Lectures du groupe</h2>
                    
                    <?php if ($is_creator): ?>
                    <button id="add-reading-btn" class="btn primary">
                        <i class="fas fa-plus"></i> Programmer une lecture
                    </button>
                    
                    <!-- Modal pour programmer une lecture -->
                    <div id="add-reading-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Programmer une nouvelle lecture</h2>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="book_id">Livre :</label>
                                    <select id="book_id" name="book_id" required>
                                        <option value="">Sélectionner un livre...</option>
                                        <?php
                                        $books_sql = "SELECT id, title, author FROM book WHERE is_approved = TRUE ORDER BY title";
                                        $books_result = $conn->query($books_sql);
                                        
                                        while ($book = $books_result->fetch_assoc()) {
                                            echo '<option value="' . $book['id'] . '">' . securiser($book['title']) . ' par ' . securiser($book['author']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">Date de début :</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Date de fin :</label>
                                    <input type="date" id="end_date" name="end_date" required>
                                </div>
                                <button type="submit" name="add_reading" class="btn primary">Programmer la lecture</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="readings-content">
                        <div class="current-reading">
                            <h3>Lecture en cours</h3>
                            <?php if ($current_reading_result->num_rows > 0): ?>
                                <?php $current_reading = $current_reading_result->fetch_assoc(); ?>
                                <div class="reading-card current">
                                    <div class="reading-book">
                                        <div class="reading-cover">
                                            <img src="https://covers.openlibrary.org/b/isbn/<?php echo $current_reading['isbn']; ?>-M.jpg" alt="<?php echo securiser($current_reading['title']); ?>">
                                        </div>
                                        <div class="reading-info">
                                            <h4><a href="livre.php?id=<?php echo $current_reading['book_id']; ?>"><?php echo securiser($current_reading['title']); ?></a></h4>
                                            <p class="reading-author">par <?php echo securiser($current_reading['author']); ?></p>
                                            <p class="reading-dates">
                                                Du <?php echo date('d/m/Y', strtotime($current_reading['start_date'])); ?> 
                                                au <?php echo date('d/m/Y', strtotime($current_reading['end_date'])); ?>
                                            </p>
                                            <p class="reading-description"><?php echo substr(securiser($current_reading['description']), 0, 200); ?>...</p>
                                            <div class="reading-actions">
                                                <a href="livre.php?id=<?php echo $current_reading['book_id']; ?>" class="btn primary">
                                                    <i class="fas fa-book"></i> Voir le livre
                                                </a>
                                                <?php if ($is_member): ?>
                                                <a href="discussion.php?group=<?php echo $group_id; ?>&book=<?php echo $current_reading['book_id']; ?>" class="btn secondary">
                                                    <i class="fas fa-comments"></i> Voir la discussion
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="no-readings">Aucune lecture en cours actuellement.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="upcoming-readings">
                            <h3>Lectures à venir</h3>
                            <?php if ($upcoming_readings_result->num_rows > 0): ?>
                                <div class="readings-list">
                                    <?php while ($reading = $upcoming_readings_result->fetch_assoc()): ?>
                                        <div class="reading-item">
                                            <div class="reading-cover-small">
                                                <img src="https://covers.openlibrary.org/b/isbn/<?php echo $reading['isbn']; ?>-S.jpg" alt="<?php echo securiser($reading['title']); ?>">
                                            </div>
                                            <div class="reading-info-small">
                                                <h4><?php echo securiser($reading['title']); ?></h4>
                                                <p class="reading-author-small">par <?php echo securiser($reading['author']); ?></p>
                                                <p class="reading-dates-small">
                                                    Du <?php echo date('d/m/Y', strtotime($reading['start_date'])); ?> 
                                                    au <?php echo date('d/m/Y', strtotime($reading['end_date'])); ?>
                                                </p>
                                            </div>
                                            <?php if ($is_creator): ?>
                                            <div class="reading-actions-small">
                                                <a href="modifier_lecture.php?id=<?php echo $reading['id']; ?>" class="btn-small secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="supprimer_lecture.php?id=<?php echo $reading['id']; ?>" class="btn-small error" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir annuler cette lecture ?');">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-readings">Aucune lecture programmée pour le moment.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="past-readings">
                            <h3>Lectures passées</h3>
                            <?php if ($past_readings_result->num_rows > 0): ?>
                                <div class="readings-list">
                                    <?php while ($reading = $past_readings_result->fetch_assoc()): ?>
                                        <div class="reading-item">
                                            <div class="reading-cover-small">
                                                <img src="https://covers.openlibrary.org/b/isbn/<?php echo $reading['isbn']; ?>-S.jpg" alt="<?php echo securiser($reading['title']); ?>">
                                            </div>
                                            <div class="reading-info-small">
                                                <h4><?php echo securiser($reading['title']); ?></h4>
                                                <p class="reading-author-small">par <?php echo securiser($reading['author']); ?></p>
                                                <p class="reading-dates-small">
                                                    Du <?php echo date('d/m/Y', strtotime($reading['start_date'])); ?> 
                                                    au <?php echo date('d/m/Y', strtotime($reading['end_date'])); ?>
                                                </p>
                                            </div>
                                            <?php if ($is_member): ?>
                                            <div class="reading-actions-small">
                                                <a href="discussion.php?group=<?php echo $group_id; ?>&book=<?php echo $reading['book_id']; ?>" class="btn-small primary">
                                                    <i class="fas fa-comments"></i>
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-readings">Aucune lecture passée enregistrée.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="members-section">
                    <h2>Membres du groupe</h2>
                    <div class="members-list">
                        <?php if ($members_result->num_rows > 0): ?>
                            <?php while ($member = $members_result->fetch_assoc()): ?>
                                <div class="member-item">
                                    <a href="profil.php?id=<?php echo $member['id']; ?>" class="member-link">
                                        <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($member['email']))); ?>?s=40&d=identicon" alt="Avatar" class="member-avatar">
                                        <div class="member-info">
                                            <span class="member-name"><?php echo securiser($member['username']); ?></span>
                                            <span class="member-grade"><?php echo securiser($member['grade_name']); ?></span>
                                        </div>
                                        <?php if ($member['is_creator']): ?>
                                            <span class="creator-badge-small">Créateur</span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="no-members">Aucun membre dans ce groupe.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($is_member): ?>
                <div class="discussions-section">
                    <h2>Discussions récentes</h2>
                    <div class="discussions-list">
                        <?php if ($discussions_result->num_rows > 0): ?>
                            <?php while ($discussion = $discussions_result->fetch_assoc()): ?>
                                <div class="discussion-item">
                                    <div class="discussion-header">
                                        <h4><?php echo securiser($discussion['title']); ?></h4>
                                        <span class="discussion-author">par <?php echo securiser($discussion['username']); ?></span>
                                    </div>
                                    <p class="discussion-excerpt"><?php echo substr(securiser($discussion['content']), 0, 150); ?>...</p>
                                    <div class="discussion-footer">
                                        <span class="discussion-date"><?php echo date('d/m/Y à H:i', strtotime($discussion['created_at'])); ?></span>
                                        <span class="discussion-replies"><?php echo $discussion['reply_count']; ?> réponse(s)</span>
                                    </div>
                                    <a href="discussion.php?id=<?php echo $discussion['id']; ?>" class="discussion-link">Voir la discussion</a>
                                </div>
                            <?php endwhile; ?>
                            <a href="discussions.php?group=<?php echo $group_id; ?>" class="btn secondary view-all-discussions">
                                <i class="fas fa-comments"></i> Voir toutes les discussions
                            </a>
                            <a href="nouvelle_discussion.php?group=<?php echo $group_id; ?>" class="btn primary new-discussion">
                                <i class="fas fa-plus"></i> Nouvelle discussion
                            </a>
                        <?php else: ?>
                            <p class="no-discussions">Aucune discussion pour le moment.</p>
                            <a href="nouvelle_discussion.php?group=<?php echo $group_id; ?>" class="btn primary new-discussion">
                                <i class="fas fa-plus"></i> Nouvelle discussion
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal d'ajout de lecture
    const modal = document.getElementById("add-reading-modal");
    const btn = document.getElementById("add-reading-btn");
    const span = document.getElementsByClassName("close")[0];
    
    if (btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }
    
    if (span) {
        span.onclick = function() {
            modal.style.display = "none";
        }
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Validation des dates
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");
    
    if (startDateInput && endDateInput) {
        const today = new Date().toISOString().split('T')[0];
        startDateInput.min = today;
        
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });
    }
    </script>
</body>
</html>