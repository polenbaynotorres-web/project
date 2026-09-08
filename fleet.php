<?php
require __DIR__ . '/data.php';
require __DIR__ . '/config.php'; // provides $pdo
require __DIR__ . '/stock.php';
$pageTitle = "Fleet | " . $siteName;
$activePage = "fleet.php";
require __DIR__ . '/header.php';

$stockMap = load_vehicle_stock($pdo);
?>

<section class="page-banner">
    <div class="container">
        <h1>Our Fleet</h1>
        <p>Well-maintained, reliable vehicles for every kind of trip — from city errands to family road trips.</p>
    </div>
</section>

<section class="fleet" id="fleet" style="padding-top: 64px;">
    <div class="container">
        <div class="fleet-grid">
            <?php foreach ($fleet as $car):
                $availableColors = in_stock_colors($car, $stockMap);
                $isOutOfStock = empty($availableColors);
            ?>
            <article class="car-card<?php echo $isOutOfStock ? ' out-of-stock' : ''; ?>">
                <div class="car-photo<?php echo empty($car['img']) ? ' ' . htmlspecialchars($car['class']) : ''; ?>">
                    <?php if (!empty($car['img'])): ?>
                        <img src="<?php echo htmlspecialchars($car['img']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
                    <?php else: ?>
                        <svg viewBox="0 0 64 32" class="car-silhouette" aria-hidden="true">
                            <path d="M6 22c0-2 2-3 4-3l4-8c1-2 3-3 5-3h18c2 0 4 1 5 3l4 8c2 0 4 1 4 3v4c0 1-1 2-2 2h-4a5 5 0 0 1-10 0H22a5 5 0 0 1-10 0H8c-1 0-2-1-2-2v-4z"/>
                            <circle cx="17" cy="26" r="3" class="wheel"/>
                            <circle cx="47" cy="26" r="3" class="wheel"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="car-body">
                    <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                    <p class="car-seats">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" stroke-linecap="round"/></svg>
                        <?php echo (int)$car['seats']; ?> Seats
                    </p>
                    <?php if ($isOutOfStock): ?>
                        <span class="out-of-stock-badge">Out of Stock</span>
                    <?php else: ?>
                        <p class="car-colors"><strong>Available:</strong> <?php echo htmlspecialchars(implode(", ", array_column($availableColors, 'name'))); ?></p>
                    <?php endif; ?>
                    <p class="car-price">&#8369;<?php echo number_format($car['price'], 0); ?><span>/day</span></p>
                    <?php if ($isOutOfStock): ?>
                        <button class="btn btn-primary btn-block rent-btn" disabled>Out of Stock</button>
                    <?php else: ?>
                        <button class="btn btn-primary btn-block rent-btn" data-car="<?php echo htmlspecialchars($car['name']); ?>">Rent Now</button>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>