<?php
require_once 'includes/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: books.php');
    exit();
}

// Récupérer les données du formulaire
$user_id = $_SESSION['user_id'];
$book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
$rating = mysqli_real_escape_string($conn, $_POST['rating']);
$comment = mysqli_real_escape_string($conn, $_POST['comment']);

// Valider les données
if (!is_numeric($book_id) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
    header('Location: book-details.php?id=' . $book_id . '&error=invalid_data');
    exit();
}

// Vérifier si le livre existe
$book_check_query = "SELECT * FROM books WHERE id = $book_id";
$book_check_result = mysqli_query($conn, $book_check_query);

if (mysqli_num_rows($book_check_result) == 0) {
    header('Location: books.php');
    exit();
}

// Vérifier si l'utilisateur a déjà publié une critique pour ce livre
$review_check_query = "SELECT * FROM reviews WHERE user_id = $user_id AND book_id = $book_id";
$review_check_result = mysqli_query($conn, $review_check_query);

if (mysqli_num_rows($review_check_result) > 0) {
    // L'utilisateur a déjà publié une critique - mettre à jour la critique existante
    $update_query = "UPDATE reviews SET rating = $rating, comment = '$comment', created_at = NOW() 
                    WHERE user_id = $user_id AND book_id = $book_id";
    
    if (mysqli_query($conn, $update_query)) {
        header('Location: book-details.php?id=' . $book_id . '&success=review_updated');
    } else {
        header('Location: book-details.php?id=' . $book_id . '&error=db_error');
    }
} else {
    // Ajouter une nouvelle critique
    $insert_query = "INSERT INTO reviews (user_id, book_id, rating, comment) 
                    VALUES ($user_id, $book_id, $rating, '$comment')";
    
    if (mysqli_query($conn, $insert_query)) {
        header('Location: book-details.php?id=' . $book_id . '&success=review_added');
    } else {
        header('Location: book-details.php?id=' . $book_id . '&error=db_error');
    }
}

exit();
?>