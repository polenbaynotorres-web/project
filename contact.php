<?php
require __DIR__ . '/data.php';
$pageTitle = "Contact | " . $siteName;
$activePage = "contact.php";
require __DIR__ . '/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Get In Touch</h1>
        <p>Questions about a booking, your fleet options, or anything else? We're here to help.</p>
    </div>
</section>

<section class="contact-page">
    <div class="container contact-grid">
        <div class="contact-form-card">
            <h2>Send Us a Message</h2>
            <p class="auth-card-sub">We typically respond within one business day.</p>

            <p class="modal-error" id="contactError" hidden></p>
            <p class="modal-success" id="contactSuccess" hidden></p>

            <form id="contactForm">
                <label>Full Name<input type="text" name="name" required></label>
                <label>Email<input type="email" name="email" required></label>
                <label>Subject<input type="text" name="subject" required></label>
                <label>Message<textarea name="message" rows="5" required></textarea></label>
                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div>

        <div class="contact-info-card">
            <h2>Find Torres Near You</h2>
            <p class="auth-card-sub">We're available in 48 cities nationwide.</p>

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
    </div>

    <div class="find-us-map contact-map">
        <iframe
            title="Torres Vehicle Rental location map"
            src="https://www.google.com/maps?q=Larena+Drive,+Dumaguete+City,+Negros+Oriental,+Philippines&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>