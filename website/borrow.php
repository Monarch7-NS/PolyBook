<?php
require_once 'includes/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'ID du livre est spécifié
if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    header('Location: books.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = mysqli_real_escape_string($conn, $_GET['book_id']);

// Vérifier si le livre existe
$book_check_query = "SELECT * FROM books WHERE id = $book_id";
$book_check_result = mysqli_query($conn, $book_check_query);

if (mysqli_num_rows($book_check_result) == 0) {
    header('Location: books.php');
    exit();
}

// Vérifier si l'utilisateur a déjà emprunté ce livre
$borrow_check_query = "SELECT * FROM borrowings 
                      WHERE user_id = $user_id AND book_id = $book_id AND (status = 'borrowed' OR status = 'overdue')";
$borrow_check_result = mysqli_query($conn, $borrow_check_query);

if (mysqli_num_rows($borrow_check_result) > 0) {
    // L'utilisateur a déjà emprunté ce livre
    header('Location: book-details.php?id=' . $book_id . '&error=already_borrowed');
    exit();
}

// Vérifier si le livre est disponible
$book_availability_query = "SELECT COUNT(*) as borrow_count FROM borrowings 
                           WHERE book_id = $book_id AND (status = 'borrowed' OR status = 'overdue')";
$book_availability_result = mysqli_query($conn, $book_availability_query);
$book_availability = mysqli_fetch_assoc($book_availability_result);

if ($book_availability['borrow_count'] > 0) {
    // Le livre n'est pas disponible
    header('Location: book-details.php?id=' . $book_id . '&error=not_available');
    exit();
}

// Calculer la date de retour (2 semaines à partir d'aujourd'hui)
$due_date = date('Y-m-d', strtotime('+14 days'));

// Enregistrer l'emprunt
$borrow_query = "INSERT INTO borrowings (user_id, book_id, due_date, status) 
                VALUES ($user_id, $book_id, '$due_date', 'borrowed')";

if (mysqli_query($conn, $borrow_query)) {
    // Emprunt réussi
    header('Location: dashboard.php?success=borrowed');
} else {
    // Erreur lors de l'emprunt
    header('Location: book-details.php?id=' . $book_id . '&error=db_error');
}

exit();
?>