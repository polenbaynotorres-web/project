<?php
require __DIR__ . '/data.php';
$pageTitle = "Reviews | " . $siteName;
$activePage = "reviews.php";
require __DIR__ . '/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>What Our Customers Say</h1>
        <p>Real experiences from real travelers who've rented with Torres.</p>
    </div>
</section>

<section class="reviews" style="padding-top: 64px;">
    <div class="container">
        <div class="reviews-track" id="reviewsTrack">
            <?php foreach ($testimonials as $t): ?>
            <div class="review-card">
                <div class="stars" aria-label="<?php echo (int)$t['rating']; ?> out of 5 stars">
                    <?php echo str_repeat('&#9733;', (int)$t['rating']); ?>
                </div>
                <p class="review-quote">&ldquo;<?php echo htmlspecialchars($t['quote']); ?>&rdquo;</p>
                <div class="review-person">
                    <div class="avatar"><?php echo strtoupper(substr($t['name'], 0, 1)); ?></div>
                    <div>
                        <p class="review-name"><?php echo htmlspecialchars($t['name']); ?></p>
                        <p class="review-role"><?php echo htmlspecialchars($t['role']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="review-dots" id="reviewDots"></div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>