<?php
session_start();
include "db.php";
include "Book.php";

$bookObj = new Book($conn);

//Беремо параметри
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Логіка вибору
if ($search) {
    $books = $bookObj->getSelectedBook($search);
}
elseif ($genre) {
    $books = $bookObj->getBookByGenre($genre);
}
else {
    $books = $bookObj->getAllBooks();
}
?>

<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Каталог книг <?php echo $genre ? "- ".htmlspecialchars($genre) : ""; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
    <a href="index.php" class="btn btn-primary mb-3">На головне меню</a>

    <h2><?php echo $search ? "Результати пошуку: ".htmlspecialchars($search) : ($genre ? "Жанр: ".htmlspecialchars($genre) : "Всі книги"); ?></h2>
    <div class="row g-3">
        <?php foreach ($books as $book): ?>
            <div class="col-md-3">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>" class="card-img-top" alt="Book" style="height: 380px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($book['description'] ?? 'Опис відсутній'); ?></p>

                        <!-- Вікове обмеження -->
                        <?php if ($book['age_limit'] > 0): ?>
                            <p style="color: #6c757d; font-size: 0.9em; margin: 8px 0;">
                                Від <?= $book['age_limit'] ?> років
                            </p>
                        <?php endif; ?>

                        <a href="borrow.php?book_id=<?= $book['book_id'] ?>" class="btn btn-primary w-100">Взяти в оренду</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($books)): ?>
            <p>На жаль, книги цього жанру відсутні.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>