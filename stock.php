<?php
/**
 * stock.php — shared helpers for reading vehicle color/stock status.
 * Only admin.php / admin_update_stock.php ever WRITE to the
 * vehicle_colors table; every public page only reads via these
 * functions.
 */

/** Loads the whole vehicle_colors table into [car_slug][color_name] => bool. */
function load_vehicle_stock(PDO $pdo) {
    $rows = $pdo->query("SELECT car_slug, color_name, in_stock FROM vehicle_colors")->fetchAll();
    $map = [];
    foreach ($rows as $row) {
        $map[$row['car_slug']][$row['color_name']] = (bool)$row['in_stock'];
    }
    return $map;
}

/**
 * Returns only the colors of $car that are currently in stock. A color
 * not yet present in the database (e.g. right after adding a new car,
 * before an admin has touched it) defaults to "in stock" so nothing
 * appears unavailable by accident.
 */
function in_stock_colors($car, $stockMap) {
    $slug = car_slug($car['name']);
    $available = [];
    foreach ($car['colors'] as $color) {
        $isIn = $stockMap[$slug][$color['name']] ?? true;
        if ($isIn) {
            $available[] = $color;
        }
    }
    return $available;
}