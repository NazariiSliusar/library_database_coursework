<?php
class Loan {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function borrowBook($book_id, $reader_id) {
        $date_return = date('Y-m-d', strtotime('+14 days'));

        $stmt = $this->conn->prepare("INSERT INTO loan (book_id, reader_id, date_return_planned) 
                                      VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $book_id, $reader_id, $date_return);

        return $stmt->execute();
    }

    public function getReaderIdByUsername($username) {
        $stmt = $this->conn->prepare("SELECT reader_id FROM reader WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['reader_id'];
        }
        return null;
    }
}
?>