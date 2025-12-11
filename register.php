<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $age = $_POST['age'] ?? 0;

    $check = $conn->prepare("SELECT username FROM reader WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $error = "❌ Цей логін вже зареєстрований. Виберіть інший.";
    }
    // ПЕРЕВІРКА 2: Перевіряємо паролі та заповненість полів
    elseif ($password !== $confirm) {
        $error = "❌ Паролі не збігаються";
    }
    elseif (empty($username) || empty($password) || empty($first_name) || empty($last_name)) {
        $error = "❌ Заповніть всі обов'язкові поля";
    }
    else {
        $stmt = $conn->prepare("INSERT INTO reader (first_name, last_name, age, phone, registration_date, password, username) 
                                VALUES (?, ?, ?, ?, NOW(), ?, ?)");

        if (!$stmt) {
            die("Помилка prepare: " . $conn->error);
        }

        $stmt->bind_param("ssisss", $first_name, $last_name, $age, $phone, $password, $username);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "❌ Помилка при реєстрації: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Реєстрація</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-md-4 mx-auto">
        <h3>Реєстрація</h3>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" class="form-control mb-2" name="first_name" placeholder="Ім'я" required>
            <input type="text" class="form-control mb-2" name="last_name" placeholder="Прізвище" required>
            <input type="number" class="form-control mb-2" name="age" placeholder="Вік" required>
            <input type="text" class="form-control mb-2" name="username" placeholder="Логін" required>
            <input type="text" class="form-control mb-2" name="phone" placeholder="Номер телефону" required>
            <input type="password" class="form-control mb-2" name="password" placeholder="Пароль" required>
            <input type="password" class="form-control mb-2" name="confirm_password" placeholder="Підтвердіть пароль" required>
            <button type="submit" class="btn btn-primary w-100">Зареєструватися</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Назад</a>
        </form>
        <p class="mt-2"><a href="login.php">Вже маєте акаунт?</a></p>
    </div>
</div>
</body>
</html>