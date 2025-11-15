<?php
class Book {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPopularBooks($limit = 3) {
        $result = $this->conn->query("SELECT * FROM book LIMIT " . intval($limit));

        if (!$result) {
            die("SQL Error: " . $this->conn->error);
        }

        $books = array();
        while($row = $result->fetch_assoc()) {
            $books[] = $row;
        }

        return $books;
    }
}
?>