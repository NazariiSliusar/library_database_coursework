<nav class="navbar bg-body-tertiary">
    <div class="container d-flex justify-content-center">
        <form class="d-flex w-100" style="max-width: 65%;" role="search"
              method="GET" action="catalog.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Введіть автора або назву книги" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Пошук</button>
        </form>
    </div>
</nav>

<div class="d-flex justify-content-between align-items-center" style="position: absolute; top: 8px; left: 20px; pointer-events: none;">
    <div class="dropdown" style="pointer-events: auto;">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
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
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Вхід/Реєстрація
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Вхід</a></li>
            <li><a class="dropdown-item" href="#">Реєстрація</a></li>
        </ul>
    </div>
</div>
