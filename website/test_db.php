<?php
// Inclure le fichier de connexion
require_once 'includes/db.php';

// Si aucune erreur n'apparaît, la connexion est réussie
echo "Connexion à la base de données réussie!";

// Tester une requête simple
$test_query = "SELECT * FROM users LIMIT 1";
$result = mysqli_query($conn, $test_query);

if ($result) {
    echo "<br>Requête exécutée avec succès.";
    echo "<br>Nombre d'utilisateurs trouvés: " . mysqli_num_rows($result);
} else {
    echo "<br>Erreur lors de l'exécution de la requête: " . mysqli_error($conn);
}
?>