<?php
/**
 * Torres Vehicle Rental — Homepage
 * A simple PHP + CSS + JS marketing homepage built from the client mock-up.
 * PHP is used to hold the page's content as data (fleet, features, steps,
 * testimonials) so the markup below stays clean and easy to update.
 */

$siteName = "Torres Vehicle Rental";

$fleet = [
    [
        "name"  => "Toyota Vios",
        "seats" => 5,
        "price" => 1800,
        "class" => "car-sedan",
        "img"   => "img/VIOS-cropped.png",
    ],
    [
        "name"  => "Toyota Innova",
        "seats" => 7,
        "price" => 2500,
        "class" => "car-mpv",
        "img"   => "img/INNOVA-cropped.png",
    ],
    [
        "name"  => "Fortuner",
        "seats" => 7,
        "price" => 3500,
        "class" => "car-suv",
        "img"   => "img/FORTUNER-cropped.png",
    ],
    [
        "name"  => "Hiace Commuter",
        "seats" => 15,
        "price" => 3800,
        "class" => "car-van",
        "img"   => "img/HIACE-cropped.png",
    ],
];

$features = [
    ["icon" => "tag",      "title" => "No Hidden Fees",       "desc" => "What you see is what you pay. All-in pricing, always."],
    ["icon" => "calendar", "title" => "Flexible Cancelation",  "desc" => "Free cancellation within the allowed window."],
    ["icon" => "key",      "title" => "Contactless Pick-Up",   "desc" => "Grab your keys safely and start your journey."],
    ["icon" => "gauge",    "title" => "No Mileage Caps",       "desc" => "Drive worry-free with no hidden mileage limits."],
    ["icon" => "doc",      "title" => "Instant Digital Agreement", "desc" => "Sign, confirm, and go — 100% digital, 100% convenient."],
    ["icon" => "shield",   "title" => "24/7 Roadside Support", "desc" => "We're here for you, anytime, anywhere."],
];

$steps = [
    ["num" => "01", "title" => "Choose Your Vehicle",  "desc" => "Browse our fleet and select the perfect ride for you."],
    ["num" => "02", "title" => "Pick Your Date",        "desc" => "Select your pick-up and return dates and time."],
    ["num" => "03", "title" => "Confirm Booking",       "desc" => "Review the price and terms, then confirm booking."],
    ["num" => "04", "title" => "Pick-Up & Drive",       "desc" => "Pick up your vehicle and enjoy a safe, smooth journey."],
];

$testimonials = [
    ["name" => "Jessica Reyes",  "role" => "Family Traveler",   "quote" => "Torres made our family trip completely stress-free. The car was clean, the fuel was full, and the staff were amazing.", "rating" => 5],
    ["name" => "Marcus Feld",    "role" => "Business Traveler", "quote" => "Booking was so easy and transparent. The vehicle was exactly what we booked, no surprises at all.", "rating" => 5],
    ["name" => "Anna Dela Cruz", "role" => "Road Tripper",      "quote" => "Great service from start to finish. Their 24/7 support gave us peace of mind on the road.", "rating" => 5],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($siteName); ?> | Rentals Built for the Whole Journey</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- ===================== HEADER / NAV ===================== -->
<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <span class="logo">
            <img src="img/logo-cropped.png" alt="<?php echo htmlspecialchars($siteName); ?> logo" class="logo-img">
        </span>

        <nav class="main-nav" id="mainNav">
            <span>Home</span>
            <span>Fleet</span>
            <span>Why Us</span>
            <span>How It Works</span>
            <span>Reviews</span>
            <span>Contact</span>
        </nav>

        <div class="header-actions">
            <div class="auth-area" id="headerAuthArea">
                <!-- Filled in by script.js depending on login state -->
            </div>
            <a href="#reserve" class="btn btn-primary">Reserve Now</a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<!-- ===================== HERO ===================== -->
<section class="hero" id="home">
    <div class="hero-media" aria-hidden="true"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-inner">
        <p class="eyebrow">Your Safety Is Our Priority</p>
        <h1>Rentals built for<br>the whole journey.</h1>
        <p class="hero-copy">
            <?php echo htmlspecialchars($siteName); ?> keeps business travel, family road trips,
            and daily commutes smooth and effortless with a modern fleet and a rental process you can trust.
        </p>
        <div class="hero-ctas">
            <a href="#reserve" class="btn btn-primary">Reserve Your Vehicle</a>
            <a href="#fleet" class="btn btn-outline">View The Fleet</a>
        </div>

        <!-- Search / reservation bar -->
        <form class="search-bar" id="searchForm" action="#fleet" method="get">
            <div class="field">
                <label for="location">Location</label>
                <select id="location" name="location">
                    <option value="">Select location</option>
                    <option value="dumaguete">Dumaguete City</option>
                    <option value="cebu">Cebu City</option>
                    <option value="manila">Manila</option>
                    <option value="davao">Davao City</option>
                </select>
            </div>
            <div class="field">
                <label for="pickupDate">Pick-up Date</label>
                <input type="date" id="pickupDate" name="pickup_date" required>
            </div>
            <div class="field">
                <label for="pickupTime">Pick-up Time</label>
                <input type="time" id="pickupTime" name="pickup_time" value="09:00" required>
            </div>
            <div class="field">
                <label for="returnDate">Return Date</label>
                <input type="date" id="returnDate" name="return_date" required>
            </div>
            <div class="field">
                <label for="returnTime">Return Time</label>
                <input type="time" id="returnTime" name="return_time" value="09:00" required>
            </div>
            <button type="submit" class="btn btn-primary search-btn">Search Vehicles</button>
        </form>
    </div>
</section>

<!-- ===================== FLEET ===================== -->
<section class="fleet" id="fleet">
    <div class="container">
        <div class="section-head">
            <h2>Explore Our Fleet</h2>
            <p>Well-maintained, reliable, and ready for your next journey.</p>
        </div>

        <div class="fleet-grid">
            <?php foreach ($fleet as $car): ?>
            <article class="car-card">
                <div class="car-photo">
                    <img src="<?php echo htmlspecialchars($car['img']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
                </div>
                <div class="car-body">
                    <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                    <p class="car-seats">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" stroke-linecap="round"/></svg>
                        <?php echo (int)$car['seats']; ?> Seats
                    </p>
                    <p class="car-price">&#8369;<?php echo number_format($car['price'], 0); ?><span>/day</span></p>
                    <button class="btn btn-primary btn-block rent-btn" data-car="<?php echo htmlspecialchars($car['name']); ?>">Rent Now</button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="fleet-cta">
            <a href="#fleet" class="btn btn-outline">View All Vehicles</a>
        </div>
    </div>
</section>

<!-- ===================== WHY RENT WITH US ===================== -->
<section class="why-us" id="why-us">
    <div class="container">
        <div class="section-head light">
            <h2>Why Rent With Torres</h2>
            <p>Transparent service. Reliable vehicles. Peace of mind.</p>
        </div>

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

<!-- ===================== HOW IT WORKS ===================== -->
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-head">
            <h2>How It Works</h2>
            <p>Renting a vehicle is quick and easy.</p>
        </div>

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
    </div>
</section>

<!-- ===================== TESTIMONIALS ===================== -->
<section class="reviews" id="reviews">
    <div class="container">
        <div class="section-head">
            <h2>What Our Customers Say</h2>
        </div>

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

<!-- ===================== FIND US / CONTACT ===================== -->
<section class="find-us" id="contact">
    <div class="find-us-info">
        <h2>Find Torres Near You</h2>
        <p class="find-us-sub">We're available in 48 cities nationwide</p>

        <ul class="contact-list">
            <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .6 3a2 2 0 0 1-.5 2L7.9 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2-.5c1 .3 2 .5 3 .6a2 2 0 0 1 1.8 2.1z"/></svg>
                <div><span class="label">Call Us:</span><span>+63 933 226 1514</span></div>
            </li>
            <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <div><span class="label">Business Hours:</span><span>Mon–Sun: 7:00 AM–9:00 PM</span></div>
            </li>
            <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
                <div><span class="label">Email Us:</span><span>torresrentalvehicle@gmail.com</span></div>
            </li>
            <li>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div><span class="label">Visit Us:</span><span>123 Larena Drive St., Dumaguete City, Negros Oriental, Philippines</span></div>
            </li>
        </ul>
    </div>

    <div class="find-us-map" id="mapEmbed">
        <iframe
            title="Torres Vehicle Rental location map"
            src="https://www.google.com/maps?q=Larena+Drive,+Dumaguete+City,+Negros+Oriental,+Philippines&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="#home" class="logo footer-logo">
                <img src="img/logo-icon.png" alt="<?php echo htmlspecialchars($siteName); ?> logo" class="logo-icon-img">
                <span><?php echo htmlspecialchars($siteName); ?></span>
            </a>
            <p>Your safety is our priority.<br>Reliable vehicles. Flexible rentals. Service you can count on.</p>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <a href="#home">Home</a>
            <a href="#fleet">Fleet</a>
            <a href="#why-us">Why Us</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#reviews">Reviews</a>
            <a href="#contact">Contact</a>
        </div>

        <div class="footer-col">
            <h4>Customer Care</h4>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms &amp; Conditions</a>
            <a href="#">Rental Agreement</a>
            <a href="#">Shipping &amp; Delivery</a>
            <a href="#">Return &amp; Refunds</a>
            <a href="#">FAQ</a>
        </div>

        <div class="footer-col newsletter">
            <h4>Newsletter</h4>
            <p>Subscribe to get the latest updates, exclusive offers, and travel tips.</p>
            <form id="newsletterForm" class="newsletter-form">
                <input type="email" name="email" placeholder="Your email" required aria-label="Email address">
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
            <p class="newsletter-msg" id="newsletterMsg" role="status"></p>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- Reservation modal (only reachable when logged in) -->
<div class="modal" id="reserveModal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="modal-close" id="modalClose" aria-label="Close" type="button">&times;</button>
        <h3 id="modalTitle">Reserve a Vehicle</h3>
        <p id="modalCarName">Complete your details and we'll confirm your booking shortly.</p>
        <form id="reserveForm">
            <input type="hidden" name="car" id="reserveCarField" value="">
            <label>Mobile Number<input type="tel" name="phone" required placeholder="09XX XXX XXXX"></label>
            <button type="submit" class="btn btn-primary btn-block">Confirm Reservation</button>
        </form>
        <p class="modal-error" id="modalError" hidden></p>
        <p class="modal-success" id="modalSuccess" hidden>Thanks! Your reservation request has been received.</p>
    </div>
</div>

<!-- Login modal -->
<div class="modal" id="loginModal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
        <button class="modal-close" id="loginModalClose" aria-label="Close" type="button">&times;</button>
        <h3 id="loginModalTitle">Log In</h3>
        <p id="loginModalMsg">Log in to book a vehicle.</p>
        <form id="loginForm">
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required></label>
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
        <p class="modal-error" id="loginError" hidden></p>
        <p class="modal-switch">Don't have an account? <a href="#" id="switchToRegister">Register</a></p>
    </div>
</div>

<!-- Register modal -->
<div class="modal" id="registerModal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="registerModalTitle">
        <button class="modal-close" id="registerModalClose" aria-label="Close" type="button">&times;</button>
        <h3 id="registerModalTitle">Create an Account</h3>
        <p>Register to book vehicles with us.</p>
        <form id="registerForm">
            <label>Full Name<input type="text" name="name" required></label>
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required minlength="8"></label>
            <label>Confirm Password<input type="password" name="confirm" required minlength="8"></label>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="modal-error" id="registerError" hidden></p>
        <p class="modal-switch">Already have an account? <a href="#" id="switchToLogin">Log In</a></p>
    </div>
</div>

<button class="back-to-top" id="backToTop" aria-label="Back to top" type="button">&uarr;</button>

<script src="script.js"></script>
</body>
</html>