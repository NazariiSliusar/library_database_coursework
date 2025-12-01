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

include "header.php";
include "navbar.php";

if ($showPopover): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px;">
        <strong>✓ Успіх!</strong> Книга взята в оренду на 14 днів.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif;

include "popular_books.php";
include "genres_carousel.php";
include "footer.php";
?>