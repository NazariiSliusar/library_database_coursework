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