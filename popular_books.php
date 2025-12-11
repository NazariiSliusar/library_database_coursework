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

                        <!-- Вікове обмеження -->
                        <?php if ($book['age_limit'] > 0): ?>
                            <p style="color: #6c757d; font-size: 0.9em; margin: 8px 0;">
                                Від <?= $book['age_limit'] ?> років
                            </p>
                        <?php endif; ?>

                        <a href="borrow.php?book_id=<?= $book['book_id'] ?>" class="btn btn-primary w-100">Взяти в оренду</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="catalog.php" class="btn btn-outline-primary btn-lg">Весь каталог</a>
    </div>
</div>