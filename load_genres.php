<?php

include "db.php";
$result = $conn->query("SELECT * FROM genres");

$all_genres = [];
while($row = $result->fetch_assoc()) {
    $all_genres[] = $row;
}

$genres_per_slide = 4; // по 4 бульбашки на слайд
$slides = array_chunk($all_genres, $genres_per_slide);

$isFirstSlide = true;
foreach($slides as $slide) {
    echo '<div class="carousel-item ' . ($isFirstSlide ? 'active' : '') . '">';
    echo '<div class="d-flex justify-content-center gap-4">';
    foreach($slide as $genre) {
        echo '<div class="text-center">';
        echo '<div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width:80px;height:80px;background-color:#e9ecef;font-weight:bold;">';
        echo htmlspecialchars($genre['genre_name']);
        echo '</div>';
        echo '</div>';
    }
    echo '</div></div>';
    $isFirstSlide = false;
}
?>
