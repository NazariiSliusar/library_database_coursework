<?php
session_start();
include "db.php";
include "loan.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $username = $_SESSION['username'];

    // Перевіряємо, чи користувач заблокований і його вік
    $check_stmt = $conn->prepare("SELECT is_blocked, age FROM reader WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $user = $check_result->fetch_assoc();

        if ($user['is_blocked'] == 1) {
            header("Location: index.php?error=blocked");
            exit;
        }

        // Перевіряємо вік книги
        $book_stmt = $conn->prepare("SELECT age_limit FROM book WHERE book_id = ?");
        $book_stmt->bind_param("i", $book_id);
        $book_stmt->execute();
        $book_result = $book_stmt->get_result();

        if ($book_result->num_rows > 0) {
            $book = $book_result->fetch_assoc();

            if ($book['age_limit'] > 0 && $user['age'] < $book['age_limit']) {
                header("Location: index.php?error=age");
                exit;
            }
        }
    }

    $loanObj = new Loan($conn);
    $reader_id = $loanObj->getReaderIdByUsername($username);

    if ($reader_id) {
        if ($loanObj->borrowBook($book_id, $reader_id)) {
            header("Location: index.php?borrowed=1");
            exit;
        }
    }
}

header("Location: index.php");
exit;
?>