<header class="site-header">
    <div class="container">
        <div class="header-logo">
            <a href="index.php">
                <h1>Poly<span>Book</span></h1>
            </a>
        </div>
        
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <?php if (estConnecte()): ?>
                    <li><a href="cercles.php">Cercles d'amis</a></li>
                    <li><a href="groupes_lecture.php">Groupes de lecture</a></li>
                    <li><a href="listes.php">Mes listes</a></li>
                    <li><a href="recherche_utilisateurs.php">Rechercher des amis</a></li>
                    <?php endif; ?>
            </ul>
        </nav>
        
        <div class="header-search">
            <form action="catalogue.php" method="GET">
                <input type="text" name="search" placeholder="Rechercher un livre..." <?php if (isset($_GET['search'])) echo 'value="' . securiser($_GET['search']) . '"'; ?>>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        
        <div class="user-menu">
            <?php if (estConnecte()): ?>
                <div class="user-dropdown">
                    <button class="dropdown-btn">
                        <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($_SESSION['username']))); ?>?s=40&d=identicon" alt="Avatar" class="user-avatar">
                        <span><?php echo securiser($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="profil.php"><i class="fas fa-user"></i> Mon profil</a>
                        <a href="favoris.php"><i class="fas fa-heart"></i> Mes favoris</a>
                        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="admin/index.php"><i class="fas fa-cog"></i> Administration</a>
                        <?php endif; ?>
                        <a href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="connexion_form.php" class="btn login-btn">Se connecter</a>
                <a href="inscription_form.php" class="btn register-btn">S'inscrire</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (isset($_SESSION['message'])): ?>
    <div class="notification <?php echo $_SESSION['message_type']; ?>">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php 
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
<?php endif; ?>