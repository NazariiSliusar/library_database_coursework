<?php
class Genre {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllGenres($perSlide = 6) {
        $result = $this->conn->query("SELECT DISTINCT genre FROM book");

        if (!$result) {
            die("SQL Error: " . $this->conn->error);
        }

        $all_genres = array();
        while($row = $result->fetch_assoc()) {
            $all_genres[] = $row;
        }

        return array_chunk($all_genres, $perSlide);
    }
}
?>