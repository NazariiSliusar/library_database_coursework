<?php
session_start();
include "db.php";

if (isset($_SESSION['staff'])) {
    header("Location: staff_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT staff_id, username, password, first_name, last_name FROM staff WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();
        $_SESSION['staff'] = $staff['username'];
        $_SESSION['staff_first_name'] = $staff['first_name'];
        $_SESSION['staff_last_name'] = $staff['last_name'];
        $_SESSION['role'] = "staff";

        header("Location: staff_dashboard.php");
        exit;
    } else {
        $error = "❌ Невірний логін або пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід працівника</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 400px;">
    <h3 class="mb-3 text-center">Вхід працівника</h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" class="form-control mb-2" name="username" placeholder="Логін" required>
        <input type="password" class="form-control mb-2" name="password" placeholder="Пароль" required>
        <button type="submit" class="btn btn-primary w-100">Увійти</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">Назад</a>
    </form>
</div>

</body>
</html>