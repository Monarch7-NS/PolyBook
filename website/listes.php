<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Listes - PolyBook</title>
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
    
    // Créer une nouvelle liste
    if (isset($_POST['create_list'])) {
        $list_name = $conn->real_escape_string($_POST['list_name']);
        $is_public = isset($_POST['is_public']) ? 1 : 0;
        
        $sql = "INSERT INTO reading_list (created_at, name, user_id, is_public) 
                VALUES (NOW(), '$list_name', $user_id, $is_public)";
        
        if ($conn->query($sql)) {
            $list_id = $conn->insert_id;
            redirect('liste.php?id=' . $list_id, 'Liste créée avec succès !', 'success');
        } else {
            $error = "Erreur lors de la création de la liste : " . $conn->error;
        }
    }
    
    // Récupérer les listes de l'utilisateur
    $lists_sql = "SELECT rl.*, COUNT(rlb.id) as book_count 
                 FROM reading_list rl 
                 LEFT JOIN reading_list_book rlb ON rl.id = rlb.reading_list_id 
                 WHERE rl.user_id = $user_id 
                 GROUP BY rl.id 
                 ORDER BY rl.created_at DESC";
    $lists_result = $conn->query($lists_sql);
    ?>

    <main class="container">
        <h1>Mes listes de lecture</h1>
        
        <div class="lists-header">
            <p class="intro">
                Créez des listes personnalisées pour organiser vos lectures : livres à lire, favoris par genre, recommandations, etc.
            </p>
            <button id="create-list-btn" class="btn primary">
                <i class="fas fa-plus"></i> Créer une nouvelle liste
            </button>
        </div>
        
        <!-- Modal pour créer une liste -->
        <div id="create-list-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Créer une nouvelle liste</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="list_name">Nom de la liste :</label>
                        <input type="text" id="list_name" name="list_name" required>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_public" name="is_public">
                        <label for="is_public">Liste publique (visible par tous les utilisateurs)</label>
                    </div>
                    <button type="submit" name="create_list" class="btn primary">Créer</button>
                </form>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="notification error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($lists_result->num_rows > 0): ?>
            <div class="lists-grid">
                <?php while ($list = $lists_result->fetch_assoc()): ?>
                    <div class="list-card">
                        <div class="list-header">
                            <h3><a href="liste.php?id=<?php echo $list['id']; ?>"><?php echo securiser($list['name']); ?></a></h3>
                            <span class="visibility-badge <?php echo $list['is_public'] ? 'public' : 'private'; ?>">
                                <i class="fas fa-<?php echo $list['is_public'] ? 'globe' : 'lock'; ?>"></i>
                                <?php echo $list['is_public'] ? 'Publique' : 'Privée'; ?>
                            </span>
                        </div>
                        <div class="list-info">
                            <p><i class="fas fa-book"></i> <?php echo $list['book_count']; ?> livre(s)</p>
                            <p><i class="fas fa-calendar"></i> Créée le <?php echo date('d/m/Y', strtotime($list['created_at'])); ?></p>
                        </div>
                        <div class="list-actions">
                            <a href="liste.php?id=<?php echo $list['id']; ?>" class="btn primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="modifier_liste.php?id=<?php echo $list['id']; ?>" class="btn secondary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="supprimer_liste.php?id=<?php echo $list['id']; ?>" class="btn error" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette liste ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-list"></i>
                <h2>Vous n'avez pas encore de listes</h2>
                <p>Créez votre première liste pour commencer à organiser vos lectures !</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    
    <script>
    // Script pour le modal de création de liste
    const modal = document.getElementById("create-list-modal");
    const btn = document.getElementById("create-list-btn");
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