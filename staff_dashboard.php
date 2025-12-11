<?php
session_start();
include "db.php";

if (!isset($_SESSION['staff'])) {
    header("Location: staff_login.php");
    exit;
}

$staff_name = $_SESSION['staff_first_name'] . " " . $_SESSION['staff_last_name'];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'loans';

// Обробка всіх POST запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Повернення книги
    if (isset($_POST['return_book'])) {
        $loan_id = $_POST['loan_id'];
        $stmt = $conn->prepare("UPDATE loan SET date_return_actual = CURDATE(), is_returned = 1 WHERE loan_id = ?");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
        header("Location: staff_dashboard.php?tab=$active_tab");
        exit;
    }

    // Продовження позики
    if (isset($_POST['extend_loan'])) {
        $loan_id = $_POST['loan_id'];
        $new_date = date('Y-m-d', strtotime('+14 days'));
        $stmt = $conn->prepare("UPDATE loan SET date_return_planned = ? WHERE loan_id = ?");
        $stmt->bind_param("si", $new_date, $loan_id);
        $stmt->execute();
        header("Location: staff_dashboard.php?tab=$active_tab");
        exit;
    }

    // Додавання книги
    if (isset($_POST['add_book'])) {
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $year = $_POST['year_published'];
        $isbn = $_POST['isbn'];
        $quantity = $_POST['quantity'];
        $image_path = $_POST['image_path'];
        $age_limit = $_POST['age_limit'];

        $stmt = $conn->prepare("INSERT INTO book (title, genre, year_published, isbn, quantity, image_path, age_limit) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssi", $title, $genre, $year, $isbn, $quantity, $image_path, $age_limit);

        if ($stmt->execute()) {
            $success_msg = "✓ Книга успішно додана!";
        } else {
            $error_msg = "❌ Помилка при додаванні книги";
        }
    }

    // Редагування книги
    if (isset($_POST['edit_book'])) {
        $book_id = $_POST['book_id'];
        $title = $_POST['title'];
        $genre = $_POST['genre'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("UPDATE book SET title = ?, genre = ?, quantity = ? WHERE book_id = ?");
        $stmt->bind_param("ssii", $title, $genre, $quantity, $book_id);

        if ($stmt->execute()) {
            $success_msg = "✓ Книга успішно оновлена!";
            $active_tab = 'manage_books';
        }
    }

    // Видалення книги
    if (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];

        // Перевіряємо, чи немає активних позик
        $check = $conn->prepare("SELECT COUNT(*) as count FROM loan WHERE book_id = ? AND is_returned = 0");
        $check->bind_param("i", $book_id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();

        if ($result['count'] > 0) {
            $error_msg = "❌ Неможна видалити книгу з активними позиками!";
        } else {
            $stmt = $conn->prepare("DELETE FROM book WHERE book_id = ?");
            $stmt->bind_param("i", $book_id);
            if ($stmt->execute()) {
                $success_msg = "✓ Книга видалена!";
                $active_tab = 'manage_books';
            }
        }
    }

    // Блокування читача
    if (isset($_POST['block_reader'])) {
        $reader_id = $_POST['reader_id'];
        $stmt = $conn->prepare("UPDATE reader SET is_blocked = 1 WHERE reader_id = ?");
        $stmt->bind_param("i", $reader_id);
        $stmt->execute();
        $success_msg = "✓ Читач заблокований!";
    }

    // Розблокування читача
    if (isset($_POST['unblock_reader'])) {
        $reader_id = $_POST['reader_id'];
        $stmt = $conn->prepare("UPDATE reader SET is_blocked = 0 WHERE reader_id = ?");
        $stmt->bind_param("i", $reader_id);
        $stmt->execute();
        $success_msg = "✓ Читач розблокований!";
    }
    if (isset($_POST['add_reader'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $age = $_POST['age'];
        $phone = $_POST['phone'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("INSERT INTO reader (first_name, last_name, age, phone, username, password, registration_date) 
                           VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("ssisss", $first_name, $last_name, $age, $phone, $username, $password);

        if ($stmt->execute()) {
            $success_msg = "✓ Читач успішно доданий!";
            $active_tab = 'manage_readers';
        } else {
            $error_msg = "❌ Помилка при додаванні читача: " . $stmt->error;
        }
    }
}

// Завантаження даних для вкладок
if ($active_tab === 'loans') {
    $query = "SELECT l.loan_id, b.title, r.first_name, r.last_name, l.date_borrowed, l.date_return_planned, l.is_returned,
              CASE WHEN l.date_return_planned < CURDATE() AND l.is_returned = 0 THEN 'Просрочено' ELSE 'В порядку' END as status
              FROM loan l
              JOIN book b ON l.book_id = b.book_id
              JOIN reader r ON l.reader_id = r.reader_id
              ORDER BY l.date_return_planned DESC";
    $loans = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
}

if ($active_tab === 'add_book') {
    $genres = $conn->query("SELECT DISTINCT genre FROM book ORDER BY genre")->fetch_all(MYSQLI_ASSOC);
}

if ($active_tab === 'manage_books') {
    $books = $conn->query("SELECT book_id, title, genre, quantity FROM book ORDER BY title")->fetch_all(MYSQLI_ASSOC);
}

if ($active_tab === 'manage_readers') {
    // Перевіряємо, чи існує колона is_blocked
    $check = $conn->query("SHOW COLUMNS FROM reader LIKE 'is_blocked'");
    $has_blocked = $check->num_rows > 0;

    if ($has_blocked) {
        $readers = $conn->query("SELECT DISTINCT reader_id, first_name, last_name, is_blocked FROM reader ORDER BY first_name")->fetch_all(MYSQLI_ASSOC);
    } else {
        $readers = $conn->query("SELECT DISTINCT reader_id, first_name, last_name FROM reader ORDER BY first_name")->fetch_all(MYSQLI_ASSOC);
    }
}

$readers_found = [];
$reader_info = [];
$reader_loans = [];

if (isset($_GET['book_id']) && $active_tab === 'book_search') {
    $book_id = $_GET['book_id'];
    $stmt = $conn->prepare("SELECT l.loan_id, r.first_name, r.last_name, l.date_borrowed, l.date_return_planned, l.is_returned
                           FROM loan l
                           JOIN reader r ON l.reader_id = r.reader_id
                           WHERE l.book_id = ? AND l.is_returned = 0");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book_copies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$book_search_results = [];
if ($active_tab === 'book_search' && isset($_POST['search_book'])) {
    $search = "%" . $_POST['search_book'] . "%";
    $stmt = $conn->prepare("SELECT b.book_id, b.title, b.quantity, 
                           COUNT(l.loan_id) as borrowed,
                           b.quantity - COUNT(l.loan_id) as available
                           FROM book b
                           LEFT JOIN loan l ON b.book_id = l.book_id AND l.is_returned = 0
                           WHERE b.title LIKE ?
                           GROUP BY b.book_id");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $book_search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$reader_loans = [];
$readers_found = [];
$reader_info = [];
if ($active_tab === 'reader_search' && isset($_POST['search_reader'])) {
    $search = "%" . $_POST['search_reader'] . "%";
    $stmt = $conn->prepare("SELECT reader_id, first_name, last_name, phone, age FROM reader 
                           WHERE first_name LIKE ? OR last_name LIKE ?");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $readers_found = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($readers_found)) {
        $reader_id = $readers_found[0]['reader_id'];
        $reader_info = $readers_found[0];
        $stmt = $conn->prepare("SELECT l.loan_id, b.title, l.date_borrowed, l.date_return_planned, l.is_returned,
                               CASE WHEN l.date_return_planned < CURDATE() AND l.is_returned = 0 THEN 'Просрочено' ELSE 'В порядку' END as status
                               FROM loan l
                               JOIN book b ON l.book_id = b.book_id
                               WHERE l.reader_id = ?
                               ORDER BY l.date_return_planned DESC");
        $stmt->bind_param("i", $reader_id);
        $stmt->execute();
        $reader_loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Панель працівника</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar bg-primary">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 text-white">📚 Бібліотека</span>
        <div class="text-white">
            <strong><?= htmlspecialchars($staff_name) ?></strong>
            <a href="logout.php" class="btn btn-danger btn-sm ms-3">Вихід</a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="?tab=loans" class="list-group-item list-group-item-action <?= $active_tab === 'loans' ? 'active' : '' ?>">📖 Всі позики</a>
                <a href="?tab=book_search" class="list-group-item list-group-item-action <?= $active_tab === 'book_search' ? 'active' : '' ?>">🔍 Пошук книги</a>
                <a href="?tab=add_book" class="list-group-item list-group-item-action <?= $active_tab === 'add_book' ? 'active' : '' ?>">➕ Додати книгу</a>
                <a href="?tab=manage_books" class="list-group-item list-group-item-action <?= $active_tab === 'manage_books' ? 'active' : '' ?>">📚 Управління книгами</a>
                <a href="?tab=reader_search" class="list-group-item list-group-item-action <?= $active_tab === 'reader_search' ? 'active' : '' ?>">👤 Пошук читача</a>
                <a href="?tab=add_reader" class="list-group-item list-group-item-action <?= $active_tab === 'add_reader' ? 'active' : '' ?>">➕ Додати читача</a>
                <a href="?tab=manage_readers" class="list-group-item list-group-item-action <?= $active_tab === 'manage_readers' ? 'active' : '' ?>">👥 Управління читачами</a>
            </div>
        </div>

        <div class="col-md-10">
            <!-- ДОДАВАННЯ КНИГИ -->
            <?php if ($active_tab === 'add_book'): ?>
                <h3>➕ Додати нову книгу</h3>
                <form method="POST" class="card p-4" style="max-width: 600px;">
                    <div class="mb-3">
                        <label class="form-label">Назва</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Жанр</label>
                        <input type="text" name="genre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Рік видання</label>
                        <input type="number" name="year_published" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Кількість примірників</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Шлях до зображення</label>
                        <input type="text" name="image_path" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Вікове обмеження</label>
                        <input type="number" name="age_limit" class="form-control">
                    </div>
                    <button type="submit" name="add_book" class="btn btn-primary">Додати книгу</button>
                </form>
            <?php endif; ?>

            <!-- УПРАВЛІННЯ КНИГАМИ -->
            <?php if ($active_tab === 'manage_books'): ?>
                <h3>📚 Управління книгами</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                        <tr>
                            <th>Назва</th>
                            <th>Жанр</th>
                            <th>Кількість</th>
                            <th>Дії</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['genre']) ?></td>
                                <td><?= $book['quantity'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $book['book_id'] ?>">✏️ Редагувати</button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                        <button type="submit" name="delete_book" class="btn btn-sm btn-danger" onclick="return confirm('Ви впевнені?')">🗑️ Видалити</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Модальне вікно редагування -->
                            <div class="modal fade" id="editModal<?= $book['book_id'] ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Редагувати книгу</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Назва</label>
                                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Жанр</label>
                                                    <input type="text" name="genre" class="form-control" value="<?= htmlspecialchars($book['genre']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Кількість примірників</label>
                                                    <input type="number" name="quantity" class="form-control" value="<?= $book['quantity'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                                                <button type="submit" name="edit_book" class="btn btn-primary">Зберегти</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- УПРАВЛІННЯ ЧИТАЧАМИ -->
            <?php if ($active_tab === 'manage_readers'): ?>
                <h3>👥 Управління читачами</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                        <tr>
                            <th>Ім'я</th>
                            <th>Прізвище</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($readers as $reader): ?>
                            <tr class="<?= $has_blocked && $reader['is_blocked'] ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($reader['first_name']) ?></td>
                                <td><?= htmlspecialchars($reader['last_name']) ?></td>
                                <td>
                                    <?php if ($has_blocked && $reader['is_blocked']): ?>
                                        <span class="badge bg-danger">🚫 Заблокований</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">✓ Активний</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="reader_id" value="<?= $reader['reader_id'] ?>">
                                        <?php if ($has_blocked && $reader['is_blocked']): ?>
                                            <button type="submit" name="unblock_reader" class="btn btn-sm btn-success">🔓 Розблокувати</button>
                                        <?php else: ?>
                                            <button type="submit" name="block_reader" class="btn btn-sm btn-danger">🚫 Заблокувати</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>


            <!-- HTML для вкладки додавання читача -->
            <?php if ($active_tab === 'add_reader'): ?>
                <h3>➕ Додати нового читача</h3>
                <form method="POST" class="card p-4" style="max-width: 600px;">
                    <div class="mb-3">
                        <label class="form-label">Ім'я</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Прізвище</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Вік</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Номер телефону</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Логін</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="add_reader" class="btn btn-primary">Додати читача</button>
                </form>
            <?php endif; ?>

            <!-- ПОШУК КНИГИ -->
            <?php if ($active_tab === 'book_search'): ?>
                <h3>🔍 Пошук книги</h3>
                <form method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search_book" class="form-control" placeholder="Введіть назву книги" required>
                        <button type="submit" class="btn btn-primary">Пошук</button>
                    </div>
                </form>

                <?php if (!empty($book_search_results)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                            <tr>
                                <th>Книга</th>
                                <th>Всього примірників</th>
                                <th>Видано</th>
                                <th>Доступно</th>
                                <th>Дії</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($book_search_results as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= $book['quantity'] ?></td>
                                    <td><?= $book['borrowed'] ?></td>
                                    <td><strong><?= $book['available'] ?></strong></td>
                                    <td>
                                        <a href="?tab=book_search&book_id=<?= $book['book_id'] ?>" class="btn btn-sm btn-info">Деталі</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (isset($_POST['search_book'])): ?>
                    <p class="text-muted">Книги не знайдені</p>
                <?php endif; ?>

                <?php if (isset($_GET['book_id']) && !empty($book_copies)): ?>
                    <h4 class="mt-5">Видані примірники</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                            <tr>
                                <th>Читач</th>
                                <th>Дата позики</th>
                                <th>Планована повернення</th>
                                <th>Дії</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($book_copies as $copy): ?>
                                <tr>
                                    <td><?= htmlspecialchars($copy['first_name'] . " " . $copy['last_name']) ?></td>
                                    <td><?= $copy['date_borrowed'] ?></td>
                                    <td><?= $copy['date_return_planned'] ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="loan_id" value="<?= $copy['loan_id'] ?>">
                                            <button type="submit" name="return_book" class="btn btn-sm btn-success">Повернути</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- ПОШУК ЧИТАЧА -->
            <?php if ($active_tab === 'reader_search'): ?>

                <h3>👤 Пошук читача</h3>
                <form method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search_reader" class="form-control" placeholder="Введіть ім'я або прізвище" required>
                        <button type="submit" class="btn btn-primary">Пошук</button>
                    </div>
                </form>

                <?php if (!empty($readers_found)): ?>
                    <div class="card mb-4 border-primary">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title"><?= htmlspecialchars($reader_info['first_name'] . " " . $reader_info['last_name']) ?></h5>
                                    <p><strong>ID користувача:</strong> <?= $reader_info['reader_id'] ?></p>
                                    <p><strong>Вік:</strong> <?= $reader_info['age'] ?> років</p>
                                    <p><strong>Номер телефону:</strong> <?= htmlspecialchars($reader_info['phone']) ?></p>
                                    <p class="text-muted mt-3">📊 Всього позик: <strong><?= count($reader_loans) ?></strong></p>
                                </div>
                                <div class="col-md-4">
                                    <form method="POST">
                                        <input type="hidden" name="reader_id" value="<?= $reader_info['reader_id'] ?>">
                                        <?php
                                        // Перевіряємо, чи існує колона is_blocked
                                        $check = $conn->query("SHOW COLUMNS FROM reader LIKE 'is_blocked'");
                                        $has_blocked = $check->num_rows > 0;

                                        if ($has_blocked) {
                                            $stmt = $conn->prepare("SELECT is_blocked FROM reader WHERE reader_id = ?");
                                            $stmt->bind_param("i", $reader_info['reader_id']);
                                            $stmt->execute();
                                            $result = $stmt->get_result()->fetch_assoc();
                                            $is_blocked = $result['is_blocked'];
                                        }
                                        ?>

                                        <?php if ($has_blocked && $is_blocked): ?>
                                            <button type="submit" name="unblock_reader" class="btn btn-success w-100">
                                                🔓 Розблокувати користувача
                                            </button>
                                            <p class="text-danger mt-2">⚠️ Користувач заблокований</p>
                                        <?php else: ?>
                                            <button type="submit" name="block_reader" class="btn btn-danger w-100">
                                                🚫 Заблокувати користувача
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>📚 Позики читача</h4>
                    <?php if (!empty($reader_loans)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                <tr>
                                    <th>Книга</th>
                                    <th>Дата позики</th>
                                    <th>Планована повернення</th>
                                    <th>Статус</th>
                                    <th>Дії</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($reader_loans as $loan): ?>
                                    <tr class="<?= $loan['is_returned'] ? 'table-success' : ($loan['status'] === 'Просрочено' ? 'table-danger' : '') ?>">
                                        <td><?= htmlspecialchars($loan['title']) ?></td>
                                        <td><?= $loan['date_borrowed'] ?></td>
                                        <td><?= $loan['date_return_planned'] ?></td>
                                        <td><strong><?= $loan['is_returned'] ? '✓ Повернена' : $loan['status'] ?></strong></td>
                                        <td>
                                            <?php if (!$loan['is_returned']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                    <button type="submit" name="return_book" class="btn btn-sm btn-success">Повернути</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">📭 У читача немає активних позик</p>
                    <?php endif; ?>
                <?php elseif (isset($_POST['search_reader'])): ?>
                    <p class="text-muted">❌ Читач не знайдений</p>
                <?php endif; ?>
            <?php endif; ?>

            <!-- ВСІ ПОЗИКИ -->
            <?php if ($active_tab === 'loans'): ?>
                <h3>📖 Всі позики</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>Книга</th>
                            <th>Читач</th>
                            <th>Дата позики</th>
                            <th>Планована повернення</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr class="<?= $loan['is_returned'] ? 'table-success' : ($loan['status'] === 'Просрочено' ? 'table-danger' : '') ?>">
                                <td><?= htmlspecialchars($loan['title']) ?></td>
                                <td><?= htmlspecialchars($loan['first_name'] . " " . $loan['last_name']) ?></td>
                                <td><?= $loan['date_borrowed'] ?></td>
                                <td><?= $loan['date_return_planned'] ?></td>
                                <td><strong><?= $loan['is_returned'] ? '✓ Повернена' : $loan['status'] ?></strong></td>
                                <td>
                                    <?php if (!$loan['is_returned']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                            <button type="submit" name="return_book" class="btn btn-sm btn-success">Повернути</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>