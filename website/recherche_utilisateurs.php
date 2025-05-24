<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rechercher des utilisateurs - PolyBook</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php 
    require_once 'connexion.php';
    include 'header.php';
    
    if (!estConnecte()) {
        redirect('connexion_form.php', 'Vous devez être connecté.', 'error');
    }
    ?>

    <main class="container">
        <h1>Rechercher des utilisateurs</h1>
        
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Nom d'utilisateur..." 
                   value="<?php echo isset($_GET['search']) ? securiser($_GET['search']) : ''; ?>">
            <button type="submit" class="btn primary">Rechercher</button>
        </form>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <?php
            $search = $conn->real_escape_string($_GET['search']);
            $users_sql = "SELECT id, username, email FROM user 
                         WHERE username LIKE '%$search%' 
                         AND id != {$_SESSION['user_id']} 
                         ORDER BY username";
            $users_result = $conn->query($users_sql);
            ?>

            <div class="users-results">
                <?php if ($users_result->num_rows > 0): ?>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <div class="user-card">
                            <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user['email']))); ?>?s=50&d=identicon" alt="Avatar">
                            <div class="user-info">
                                <h3><?php echo securiser($user['username']); ?></h3>
                            </div>
                            <a href="profil.php?id=<?php echo $user['id']; ?>" class="btn primary">Voir le profil</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Aucun utilisateur trouvé.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>