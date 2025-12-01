<div class="container" style="margin-top: 100px;">
    <div class="mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 20px; font-weight: 600; text-align: left; padding: 15px 30px; border-radius: 8px;">
        Популярні книги
    </div>

    <div class="row justify-content-center g-3">
        <?php foreach($books as $book): ?>
            <div class="col-auto">
                <div class="card" style="width: 18rem;">
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>"
                         class="card-img-top" alt="Book">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text">
                            <?= htmlspecialchars($book['description'] ?? 'Опис відсутній') ?>
                        </p>
                        <a href="#" class="btn btn-primary">Взяти в оренду</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
