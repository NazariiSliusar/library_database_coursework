<?php
session_start();
include "db.php";
include "Book.php";
include "Genre.php";

$bookObj = new Book($conn);
$genreObj = new Genre($conn);

$books = $bookObj->getPopularBooks(3);
$slides = $genreObj->getAllGenres(6);

$showPopover = isset($_GET['borrowed']) && $_GET['borrowed'] == 1;
$showBlocked = isset($_GET['error']) && $_GET['error'] == 'blocked';
$showAge = isset($_GET['error']) && $_GET['error'] == 'age';

include "header.php";
include "navbar.php";

if ($showPopover): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>✓ Успіх!</strong> Книга взята в оренду на 14 днів.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif;

if ($showBlocked): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>✗ Помилка!</strong> Ваш акаунт заблокований. Ви не можете брати книги.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif;

if ($showAge): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>⚠ Увага!</strong> Ви занадто молоді для цієї книги. Мінімальний вік обмежений.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif;

include "popular_books.php";
include "genres_carousel.php";
include "footer.php";
?>