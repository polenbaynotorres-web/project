<?php
/**
 * admin.php — protected dashboard for managing which car colors are
 * in stock. Only accessible when $_SESSION['admin_id'] is set (i.e.
 * logged in via admin_login.php). Submits to admin_update_stock.php.
 */

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

require __DIR__ . '/data.php';   // $fleet, car_slug()
require __DIR__ . '/config.php'; // provides $pdo

// Load current stock status for every car/color.
$stockRows = $pdo->query("SELECT car_slug, color_name, in_stock FROM vehicle_colors")->fetchAll();
$stockMap = []; // [car_slug][color_name] => bool
foreach ($stockRows as $row) {
    $stockMap[$row['car_slug']][$row['color_name']] = (bool)$row['in_stock'];
}

$saved = isset($_GET['saved']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Fleet Stock | <?php echo htmlspecialchars($siteName); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">
            <img src="img/logo-cropped.png" alt="<?php echo htmlspecialchars($siteName); ?> logo" class="logo-img">
        </a>
        <div class="header-actions" style="margin-left:auto;">
            <span class="user-greeting">Admin: <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="admin_logout.php" class="btn btn-outline-navy btn-sm">Log Out</a>
        </div>
    </div>
</header>

<section class="page-banner">
    <div class="container">
        <h1>Fleet Stock Management</h1>
        <p>Check a color to mark it in stock. Uncheck to mark it out of stock. A car with no colors checked shows as "Out of Stock" on the site.</p>
    </div>
</section>

<section class="contact-page">
    <div class="container">
        <?php if ($saved): ?>
            <p class="modal-success" style="margin-bottom:20px;">Stock updated successfully.</p>
        <?php endif; ?>

        <form method="POST" action="admin_update_stock.php">
            <div class="admin-stock-grid">
                <?php foreach ($fleet as $car): $slug = car_slug($car['name']); ?>
                <div class="admin-car-card">
                    <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                    <div class="admin-color-list">
                        <?php foreach ($car['colors'] as $color):
                            $isChecked = $stockMap[$slug][$color['name']] ?? true;
                        ?>
                        <label class="admin-color-toggle">
                            <input type="checkbox"
                                   name="stock[<?php echo htmlspecialchars($slug); ?>][<?php echo htmlspecialchars($color['name']); ?>]"
                                   value="1"
                                   <?php echo $isChecked ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($color['name']); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top:24px;">Save Stock Changes</button>
        </form>
    </div>
</section>

</body>
</html>