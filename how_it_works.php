<?php
require __DIR__ . '/data.php';
$pageTitle = "How It Works | " . $siteName;
$activePage = "how_it_works.php";
require __DIR__ . '/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>How It Works</h1>
        <p>Renting a vehicle with us is quick, transparent, and hassle-free — here's the whole process.</p>
    </div>
</section>

<section class="how-it-works" style="padding-top: 64px;">
    <div class="container">
        <div class="steps">
            <?php foreach ($steps as $i => $s): ?>
            <div class="step">
                <div class="step-icon"><?php echo htmlspecialchars($s['num']); ?></div>
                <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                <p><?php echo htmlspecialchars($s['desc']); ?></p>
            </div>
            <?php if ($i < count($steps) - 1): ?><div class="step-connector" aria-hidden="true"></div><?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="fleet-cta">
            <a href="fleet.php" class="btn btn-primary">Browse The Fleet</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>