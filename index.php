<?php
require __DIR__ . '/data.php';
require __DIR__ . '/config.php'; // provides $pdo
require __DIR__ . '/stock.php';
$pageTitle = $siteName . " | Rentals Built for the Whole Journey";
$activePage = "index.php";
require __DIR__ . '/header.php';

$stockMap = load_vehicle_stock($pdo);
?>

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
            <a href="fleet.php" class="btn btn-outline">View The Fleet</a>
        </div>

        <!-- Search / reservation bar -->
        <form class="search-bar" id="searchForm" action="fleet.php" method="get">
            <div class="field">
                <label for="location">Location</label>
                <select id="location" name="location">
                    <option value="">Select location</option>
                    <?php foreach ($locations as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
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
            <?php foreach (array_slice($fleet, 0, 4) as $car):
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

        <div class="fleet-cta">
            <a href="fleet.php" class="btn btn-outline">View All Vehicles</a>
        </div>
    </div>
</section>

<!-- ===================== WHY RENT WITH US (teaser) ===================== -->
<section class="why-us" id="why-us">
    <div class="container">
        <div class="section-head light">
            <h2>Why Rent With Torres</h2>
            <p>Transparent service. Reliable vehicles. Peace of mind.</p>
        </div>

        <div class="features-grid">
            <?php foreach (array_slice($features, 0, 3) as $f): ?>
            <div class="feature">
                <span class="feature-icon" data-icon="<?php echo htmlspecialchars($f['icon']); ?>"></span>
                <h3><?php echo htmlspecialchars($f['title']); ?></h3>
                <p><?php echo htmlspecialchars($f['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="fleet-cta">
            <a href="why-us.php" class="btn btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.7);">See All Reasons</a>
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

<!-- ===================== TESTIMONIALS (teaser) ===================== -->
<section class="reviews" id="reviews">
    <div class="container">
        <div class="section-head">
            <h2>What Our Customers Say</h2>
        </div>

        <div class="reviews-track" id="reviewsTrack">
            <?php foreach (array_slice($testimonials, 0, 3) as $t): ?>
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

        <div class="fleet-cta">
            <a href="reviews.php" class="btn btn-outline">Read More Reviews</a>
        </div>
    </div>
</section>

<!-- ===================== CONTACT (teaser) ===================== -->
<section class="find-us" id="contact">
    <div class="find-us-info">
        <h2>Find Torres Near You</h2>
        <p class="find-us-sub">We're available in 48 cities nationwide</p>
        <p style="color:#BFD3F5; margin-bottom: 24px;">Call us, drop by, or send a message — our team responds fast.</p>
        <a href="contact.php" class="btn btn-primary">Get In Touch</a>
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

<?php require __DIR__ . '/footer.php'; ?>