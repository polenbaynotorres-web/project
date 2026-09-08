<?php
/**
 * header.php — shared header/nav, included at the top of every page.
 * Expects $pageTitle and $activePage to be set by the including page
 * before require'ing this file, e.g.:
 *
 *   $pageTitle = "Fleet | Torres Vehicle Rental";
 *   $activePage = "fleet.php";
 *   require __DIR__ . '/header.php';
 *
 * $activePage controls which nav link gets the "active" underline.
 */

require __DIR__ . '/session_init.php';
$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($pageTitle)) {
    $pageTitle = $siteName . " | Rentals Built for the Whole Journey";
}
if (!isset($activePage)) {
    $activePage = basename($_SERVER['PHP_SELF']);
}

function nav_active($page, $activePage) {
    return $page === $activePage ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- ===================== HEADER / NAV ===================== -->
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="index.php" class="logo">
            <img src="img/logo-cropped.png" alt="<?php echo htmlspecialchars($siteName); ?> logo" class="logo-img">
        </a>

        <nav class="main-nav" id="mainNav">
            <a href="index.php"<?php echo nav_active('index.php', $activePage); ?>>Home</a>
            <a href="fleet.php"<?php echo nav_active('fleet.php', $activePage); ?>>Fleet</a>
            <a href="whyus.php"<?php echo nav_active('whyus.php', $activePage); ?>>Why Us</a>
            <a href="how_it_works.php"<?php echo nav_active('how_it_works.php', $activePage); ?>>How It Works</a>
            <a href="reviews.php"<?php echo nav_active('reviews.php', $activePage); ?>>Reviews</a>
            <a href="contact.php"<?php echo nav_active('contact.php', $activePage); ?>>Contact</a>
            <?php if ($isLoggedIn): ?>
            <a href="reservations.php"<?php echo nav_active('reservations.php', $activePage); ?>>My Reservations</a>
            <?php endif; ?>
        </nav>

        <div class="header-actions">
            <div class="auth-area" id="headerAuthArea">
                <!-- Filled in by script.js depending on login state -->
            </div>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>