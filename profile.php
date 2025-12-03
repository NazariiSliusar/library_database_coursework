<?php
include "db.php";
session_start();

// Перевіряємо, чи користувач залогінений
if (!isset($_SESSION['reader_id'])) {
    header("Location: login.php");
    exit;
}

$reader_id = $_SESSION['reader_id'];

// Отримуємо активні позики (не повернені книги)
$query = "SELECT l.loan_id, b.title, GROUP_CONCAT(CONCAT(a.first_name, ' ', a.last_name) SEPARATOR ', ') as author, l.date_borrowed, l.date_return_planned, b.book_id
          FROM loan l 
          JOIN book b ON l.book_id = b.book_id 
          LEFT JOIN book_author ba ON b.book_id = ba.book_id
          LEFT JOIN author a ON ba.author_id = a.author_id
          WHERE l.reader_id = ? AND l.is_returned = 0
          GROUP BY l.loan_id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reader_id);
$stmt->execute();
$loans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Обробка кнопок
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['extend'])) {
        $loan_id = $_POST['loan_id'];
        $new_date = date('Y-m-d', strtotime('+14 days'));
        $update = $conn->prepare("UPDATE loan SET date_return_planned = ? WHERE loan_id = ?");
        $update->bind_param("si", $new_date, $loan_id);
        $update->execute();
        header("Location: profile.php");
        exit;
    }

    if (isset($_POST['return'])) {
        $loan_id = $_POST['loan_id'];
        $return_date = date('Y-m-d');
        $update = $conn->prepare("UPDATE loan SET date_return_actual = ?, is_returned = 1 WHERE loan_id = ?");
        $update->bind_param("si", $return_date, $loan_id);
        $update->execute();
        header("Location: profile.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Мій профіль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="mb-3">
        <a href="index.php" class="btn btn-primary">На головне меню</a>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Мій профіль</h5>
                    <p><strong>Ім'я:</strong> <?= htmlspecialchars($_SESSION['first_name']) ?></p>
                    <p><strong>Прізвище:</strong> <?= htmlspecialchars($_SESSION['last_name']) ?></p>
                    <p><strong>Логін:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                    <p><strong>Вік:</strong> <?= htmlspecialchars($_SESSION['age']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($_SESSION['phone']) ?></p>
                    <a href="logout.php" class="btn btn-danger w-100">Вихід</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h3>Мої позики</h3>
            <?php if (empty($loans)): ?>
                <p class="text-muted">Ви не позичали жодних книг</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Назва</th>
                            <th>Автор</th>
                            <th>Дата позики</th>
                            <th>Планована дата повернення</th>
                            <th>Дії</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars($loan['title']) ?></td>
                                <td><?= htmlspecialchars($loan['author']) ?></td>
                                <td><?= $loan['date_borrowed'] ?></td>
                                <td><?= $loan['date_return_planned'] ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                        <button type="submit" name="extend" class="btn btn-sm btn-warning">Продовжити</button>
                                        <button type="submit" name="return" class="btn btn-sm btn-danger">Віддати</button>
                                    </form>
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