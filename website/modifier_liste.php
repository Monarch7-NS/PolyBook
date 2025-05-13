<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une liste - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    // Vérifier si l'utilisateur est connecté
    if (!estConnecte()) {
        redirect('connexion_form.php', 'Vous devez être connecté pour modifier une liste.', 'error');
    }
    
    // Vérifier si l'ID de la liste est fourni
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        redirect('listes.php', 'Liste non spécifiée.', 'error');
    }
    
    $list_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    // Vérifier si la liste existe et appartient à l'utilisateur connecté
    $list_sql = "SELECT * FROM reading_list WHERE id = $list_id AND user_id = $user_id";
    $list_result = $conn->query($list_sql);
    
    if ($list_result->num_rows == 0) {
        redirect('listes.php', "Cette liste n'existe pas ou ne vous appartient pas.", 'error');
    }
    
    $list = $list_result->fetch_assoc();
    
    // Traiter le formulaire de modification
    if (isset($_POST['update_list'])) {
        $list_name = $conn->real_escape_string($_POST['list_name']);
        $is_public = isset($_POST['is_public']) ? 1 : 0;
        
        $update_sql = "UPDATE reading_list 
                      SET name = '$list_name', is_public = $is_public 
                      WHERE id = $list_id";
        
        if ($conn->query($update_sql)) {
            redirect('liste.php?id=' . $list_id, 'Liste mise à jour avec succès.', 'success');
        } else {
            $error = "Erreur lors de la mise à jour de la liste : " . $conn->error;
        }
    }
    ?>

    <main class="container">
        <h1>Modifier ma liste</h1>
        
        <?php if (isset($error)): ?>
            <div class="notification error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="" class="edit-list-form">
                <div class="form-group">
                    <label for="list_name">Nom de la liste :</label>
                    <input type="text" id="list_name" name="list_name" value="<?php echo securiser($list['name']); ?>" required>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_public" name="is_public" <?php if ($list['is_public']) echo 'checked'; ?>>
                    <label for="is_public">Liste publique (visible par tous les utilisateurs)</label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_list" class="btn primary">Enregistrer les modifications</button>
                    <a href="liste.php?id=<?php echo $list_id; ?>" class="btn secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>