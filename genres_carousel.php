<div class="container my-5">
    <div class="mb-4"
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white; font-size: 20px; font-weight: 600;
                text-align: left; padding: 15px 30px; border-radius: 8px;">
        Шукати за жанром
    </div>

    <div id="genreCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            <?php $first = true; ?>
            <?php foreach ($slides as $slide): ?>
                <div class="carousel-item <?= $first ? 'active' : '' ?>">
                    <div class="d-flex justify-content-center gap-4">
                        <?php foreach ($slide as $genre): ?>
                            <a href="catalog.php?genre=<?= urlencode($genre['genre']) ?>" style="text-decoration: none;">
                                <div class="text-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                         style="width:80px;height:80px;background-color:#e9ecef;font-weight:bold;cursor:pointer;">
                                        <?= htmlspecialchars($genre['genre'] ?? 'N/A') ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $first = false; ?>
            <?php endforeach; ?>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#genreCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-primary rounded-circle p-3"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#genreCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-primary rounded-circle p-3"></span>
        </button>
    </div>
</div>
