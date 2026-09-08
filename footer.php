<?php
/**
 * footer.php — shared footer, modals, and script include, used at
 * the bottom of every page. Expects $siteName to already be set
 * (data.php does this).
 */
?>
<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="index.php" class="logo footer-logo">
                <img src="img/logo-icon.png" alt="<?php echo htmlspecialchars($siteName); ?> logo" class="logo-icon-img">
                <span><?php echo htmlspecialchars($siteName); ?></span>
            </a>
            <p>Your safety is our priority.<br>Reliable vehicles. Flexible rentals. Service you can count on.</p>
        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <a href="index.php">Home</a>
            <a href="fleet.php">Fleet</a>
            <a href="whyus.php">Why Us</a>
            <a href="how_it_works.php">How It Works</a>
            <a href="reviews.php">Reviews</a>
            <a href="contact.php">Contact</a>
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

<?php
/**
 * Per-car in-stock colors, used to populate the reservation modal's
 * color dropdown in script.js. fleet.php/index.php (the only pages
 * with "Rent Now" buttons) load $stockMap via stock.php; everywhere
 * else this just falls back to each car's full color list, which is
 * harmless since there's no rent button to open the modal from.
 */
$reserveCarColors = [];
if (!empty($fleet)) {
    foreach ($fleet as $car) {
        $colors = isset($stockMap) ? in_stock_colors($car, $stockMap) : $car['colors'];
        $reserveCarColors[$car['name']] = array_column($colors, 'name');
    }
}
?>
<script>
    window.CAR_COLORS = <?php echo json_encode($reserveCarColors); ?>;
</script>

<!-- Reservation modal (only reachable when logged in) -->
<div class="modal" id="reserveModal" aria-hidden="true">
    <div class="modal-card modal-card-wide" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="modal-close" id="modalClose" aria-label="Close" type="button">&times;</button>
        <h3 id="modalTitle">Reserve a Vehicle</h3>
        <p id="modalCarName">Complete your details and we'll confirm your booking shortly.</p>
        <form id="reserveForm">
            <input type="hidden" name="car" id="reserveCarField" value="">
            <label>Mobile Number<input type="tel" name="phone" required placeholder="09XX XXX XXXX"></label>
            <div class="modal-row">
                <label class="field-half">Location
                    <select name="location" id="reserveLocation" required>
                        <option value="">Select location</option>
                        <?php foreach ($locations as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field-half">Color
                    <select name="color" id="reserveColor" required>
                        <option value="">Select color</option>
                    </select>
                </label>
            </div>
            <div class="modal-row">
                <label class="field-half">Pick-up Date<input type="date" name="pickup_date" id="reservePickupDate" required></label>
                <label class="field-half">Pick-up Time<input type="time" name="pickup_time" id="reservePickupTime" value="09:00" required></label>
            </div>
            <div class="modal-row">
                <label class="field-half">Return Date<input type="date" name="return_date" id="reserveReturnDate" required></label>
                <label class="field-half">Return Time<input type="time" name="return_time" id="reserveReturnTime" value="09:00" required></label>
            </div>
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

<script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>