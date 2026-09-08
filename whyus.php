<?php
require __DIR__ . '/data.php';
$pageTitle = "Why Us | " . $siteName;
$activePage = "whyus.php";
require __DIR__ . '/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Why Rent With Torres</h1>
        <p>Transparent service. Reliable vehicles. Peace of mind, from booking to drop-off.</p>
    </div>
</section>

<section class="why-us" style="padding-top: 64px;">
    <div class="container">
        <div class="features-grid">
            <?php foreach ($features as $f): ?>
            <div class="feature">
                <span class="feature-icon" data-icon="<?php echo htmlspecialchars($f['icon']); ?>"></span>
                <h3><?php echo htmlspecialchars($f['title']); ?></h3>
                <p><?php echo htmlspecialchars($f['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>