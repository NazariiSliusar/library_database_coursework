<?php
include "db.php";
include "Book.php";
include "Genre.php";

$bookObj = new Book($conn);
$genreObj = new Genre($conn);

$books = $bookObj->getPopularBooks(3);
$slides = $genreObj->getAllGenres(6);

include "header.php";
include "navbar.php";
include "popular_books.php";
include "genres_carousel.php";
include "footer.php";
