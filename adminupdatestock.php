<?php
/**
 * admin_update_stock.php — processes admin.php's checkbox form.
 * Requires an admin session. For every car/color known in data.php,
 * sets in_stock = 1 if its checkbox was submitted (checked), or 0 if
 * it was omitted (unchecked — HTML forms don't submit unchecked boxes).
 */

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

require __DIR__ . '/data.php';   // $fleet, car_slug()
require __DIR__ . '/config.php'; // provides $pdo

$submitted = $_POST['stock'] ?? []; // [slug][colorName] => "1" when checked

$stmt = $pdo->prepare(
    "INSERT INTO vehicle_colors (car_slug, color_name, in_stock)
     VALUES (:slug, :color, :in_stock)
     ON DUPLICATE KEY UPDATE in_stock = :in_stock2"
);

foreach ($fleet as $car) {
    $slug = car_slug($car['name']);
    foreach ($car['colors'] as $color) {
        $name = $color['name'];
        $inStock = isset($submitted[$slug][$name]) ? 1 : 0;
        $stmt->execute([
            ":slug" => $slug,
            ":color" => $name,
            ":in_stock" => $inStock,
            ":in_stock2" => $inStock,
        ]);
    }
}

header('Location: admin.php?saved=1');
exit;