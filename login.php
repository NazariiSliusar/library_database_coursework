<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT reader_id, username, first_name, last_name, phone, age FROM reader WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['reader_id'] = $user['reader_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['age'] = $user['age'];
        header("Location: profile.php");
        exit;
    }
    $error = "Неправильний логін або пароль";
}
?>

<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Вхід</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-md-4 mx-auto">
        <h3>Вхід</h3>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" class="form-control mb-2" name="username" placeholder="Логін" required>
            <input type="password" class="form-control mb-2" name="password" placeholder="Пароль" required>
            <button type="submit" class="btn btn-primary w-100">Увійти</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Назад</a>
        </form>
        <p class="mt-2"><a href="register.php">Немає акаунту?</a></p>
    </div>
</div>
</body>
</html>