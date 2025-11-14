<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
<nav class="navbar bg-body-tertiary">
    <div class="container d-flex justify-content-center">
        <form class="d-flex w-100" style="max-width: 65%;" role="search">
            <input class="form-control me-2" type="search"
                   placeholder="Введіть автора або назву книги" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Пошук</button>
        </form>
    </div>
</nav>

<div class="d-flex justify-content-between align-items-center" style="position: absolute; top: 8px; left: 20px; right: 20px; pointer-events: none;">
    <div class="dropdown" style="pointer-events: auto;">
        <button class="btn btn-secondary dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
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
        <button class="btn btn-secondary dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
            Вхід/Реєстрація
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Вхід</a></li>
            <li><a class="dropdown-item" href="#">Реєстрація</a></li>
        </ul>
    </div>
</div>

<div class="container" style="margin-top: 100px;">
    <!-- Довгий банер з градієнтом -->
    <div class="mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 20px; font-weight: 600; text-align: left; padding: 15px 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
        Популярні книги
    </div>

    <div class="row justify-content-center g-3">
    </div>
    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center g-3">
            <div class="col-auto">
                <div class="card" style="width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Book name</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="#" class="btn btn-primary">Взяти в оренду</a>
                    </div>
                </div>
            </div>

            <div class="col-auto">
                <div class="card" style="width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Book name</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="#" class="btn btn-primary">Взяти в оренду</a>
                    </div>
                </div>
            </div>

            <div class="col-auto">
                <div class="card" style="width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Book name</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="#" class="btn btn-primary">Взяти в оренду</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<div class="container my-5">
    <h4 class="mb-4">Шукати за жанром</h4>

    <div id="genreCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php include "load_genres.php"; ?>
        </div>

        <!-- Кнопки навігації -->
        <button class="carousel-control-prev" type="button" data-bs-target="#genreCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-primary rounded-circle p-3" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#genreCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-primary rounded-circle p-3" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>