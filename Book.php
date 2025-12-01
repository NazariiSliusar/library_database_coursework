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

    public function getBookByGenre($genre) {
        $stmt = $this->conn->prepare("SELECT * FROM book WHERE genre = ?");
        $stmt->bind_param("s", $genre);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getSelectedBook($title){
        $search_term = "%".$title."%";
        $stmt = $this->conn->prepare("SELECT * FROM book WHERE title LIKE ?");
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $result ? $result : [];  // Завжди повертайте масив
    }

    public function getAllBooks() {
        $result = $this->conn->query("SELECT * FROM book");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>