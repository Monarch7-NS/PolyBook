<!-- logout.php -->
<?php
// Inclure le fichier de connexion à la base de données
require_once 'includes/db.php';

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit();
?>

<!-- dashboard.php -->
<?php
// Inclure le fichier de connexion à la base de données
// require_once 'includes/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Récupérer les emprunts de l'utilisateur
$query = "SELECT b.*, books.title, books.author FROM borrowings b 
          JOIN books ON b.book_id = books.id 
          WHERE b.user_id = $user_id 
          ORDER BY b.borrow_date DESC";
$borrowings_result = mysqli_query($conn, $query);

$page_title = 'Mon Compte';
?>

<?php include 'includes/header.php'; ?>

<section class="dashboard">
    <div class="container">
        <h2>Tableau de bord</h2>
        
        <div class="dashboard-info">
            <div class="user-info">
                <h3>Mes informations</h3>
                <p><strong>Nom d'utilisateur:</strong> <?php echo $user['username']; ?></p>
                <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                <p><strong>Nom complet:</strong> <?php echo $user['full_name']; ?></p>
                <p><strong>Membre depuis:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                <a href="edit-profile.php" class="btn">Modifier mon profil</a>
            </div>
        </div>
        
        <div class="borrowings">
            <h3>Mes emprunts</h3>
            
            <?php if (mysqli_num_rows($borrowings_result) > 0): ?>
                <table class="borrowings-table">
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th>Auteur</th>
                            <th>Date d'emprunt</th>
                            <th>Date de retour</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($borrowing = mysqli_fetch_assoc($borrowings_result)): ?>
                            <tr>
                                <td><?php echo $borrowing['title']; ?></td>
                                <td><?php echo $borrowing['author']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrowing['borrow_date'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($borrowing['due_date'])); ?></td>
                                <td class="status-<?php echo $borrowing['status']; ?>">
                                    <?php 
                                    switch ($borrowing['status']) {
                                        case 'borrowed':
                                            echo 'Emprunté';
                                            break;
                                        case 'returned':
                                            echo 'Retourné';
                                            break;
                                        case 'overdue':
                                            echo 'En retard';
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Vous n'avez pas encore emprunté de livres.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>