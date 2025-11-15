<?php
include "db.php";
include "Book.php";
include "Genre.php";

$bookObj = new Book($conn);
$genreObj = new Genre($conn);

$books = $bookObj->getPopularBooks(3);
$slides = $genreObj->getAllGenres(6);
?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Бібліотека</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar з пошуком -->
<nav class="navbar bg-body-tertiary">
    <div class="container d-flex justify-content-center">
        <form class="d-flex w-100" style="max-width: 65%;" role="search">
            <input class="form-control me-2" type="search" placeholder="Введіть автора або назву книги" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Пошук</button>
        </form>
    </div>
</nav>

<!-- Dropdown меню користувачів -->
<div class="d-flex justify-content-between align-items-center" style="position: absolute; top: 8px; left: 20px; pointer-events: none;">
    <div class="dropdown" style="pointer-events: auto;">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Користувач
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Читач</a></li>
            <li><a class="dropdown-item" href="#">Працівник бібліотеки</a></li>
        </ul>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center" style="position: absolute; top: 8px; right: 20px; pointer-events: none;">
    <div class="dropdown" style="pointer-events: auto;">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Вхід/Реєстрація
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Вхід</a></li>
            <li><a class="dropdown-item" href="#">Реєстрація</a></li>
        </ul>
    </div>
</div>

<!-- Популярні книги -->
<div class="container" style="margin-top: 100px;">
    <div class="mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 20px; font-weight: 600; text-align: left; padding: 15px 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
        Популярні книги
    </div>
    <div class="row justify-content-center g-3">
        <?php foreach($books as $book): ?>
            <div class="col-auto">
                <div class="card" style="width: 18rem;">
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>" class="card-img-top" alt="Book">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(isset($book['description']) ? $book['description'] : 'Опис відсутній'); ?></p>
                        <a href="#" class="btn btn-primary">Взяти в оренду</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Карусель жанрів -->
<div class="container my-5">
    <div class="mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 20px; font-weight: 600; text-align: left; padding: 15px 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
        Шукати за жанром
    </div>
    <div id="genreCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $isFirstSlide = true;
            foreach ($slides as $slide) {
                echo '<div class="carousel-item ' . ($isFirstSlide ? 'active' : '') . '">';
                echo '<div class="d-flex justify-content-center gap-4">';
                foreach ($slide as $genre) {
                    echo '<div class="text-center">';
                    echo '<div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:80px;height:80px;background-color:#e9ecef;font-weight:bold;">';
                    echo htmlspecialchars(isset($genre['genre']) ? $genre['genre'] : 'N/A');
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div></div>';
                $isFirstSlide = false;
            }
            ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#genreCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-primary rounded-circle p-3"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#genreCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-primary rounded-circle p-3"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>