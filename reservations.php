<?php
require __DIR__ . '/data.php';
require __DIR__ . '/config.php'; // provides $pdo
$pageTitle = "My Reservations | " . $siteName;
$activePage = "reservations.php";
require __DIR__ . '/header.php'; // starts the session and sets $isLoggedIn
?>

<section class="page-banner">
    <div class="container">
        <h1>My Reservations</h1>
        <p>View your bookings and cancel anytime — no need to call in.</p>
    </div>
</section>

<section class="contact-page" style="padding-top: 24px; padding-bottom: 64px;">
    <div class="container">
        <?php if (!$isLoggedIn): ?>
            <div class="contact-form-card" style="max-width: 480px; margin: 0 auto; text-align: center;">
                <h2>Log In to View Your Reservations</h2>
                <p class="auth-card-sub">You need to be logged in to see and manage your bookings.</p>
                <button type="button" class="btn btn-primary btn-block" id="reservationsLoginTrigger">Log In</button>
            </div>
        <?php else:
            $stmt = $pdo->prepare(
                "SELECT id, car, color, location, pickup_date, pickup_time, return_date, return_time, status, created_at
                 FROM reservations
                 WHERE user_id = :user_id
                 ORDER BY created_at DESC"
            );
            $stmt->execute([":user_id" => $_SESSION['user_id']]);
            $reservations = $stmt->fetchAll();
        ?>
            <?php if (empty($reservations)): ?>
                <div class="contact-form-card" style="max-width: 480px; margin: 0 auto; text-align: center;">
                    <h2>No Reservations Yet</h2>
                    <p class="auth-card-sub">You haven't booked a vehicle with us yet.</p>
                    <a href="fleet.php" class="btn btn-primary btn-block">Browse the Fleet</a>
                </div>
            <?php else: ?>
                <div class="reservations-list" id="reservationsList">
                    <?php foreach ($reservations as $r):
                        $isCancelled = $r['status'] === 'cancelled';
                        $locationLabel = $locations[$r['location']] ?? ($r['location'] !== '' ? $r['location'] : 'Not specified');
                    ?>
                    <article class="reservation-card<?php echo $isCancelled ? ' cancelled' : ''; ?>" data-reservation-id="<?php echo (int)$r['id']; ?>">
                        <div class="reservation-main">
                            <h3><?php echo htmlspecialchars($r['car']); ?></h3>
                            <?php if (!empty($r['color'])): ?>
                                <p><strong>Color:</strong> <?php echo htmlspecialchars($r['color']); ?></p>
                            <?php endif; ?>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($locationLabel); ?></p>
                            <p><strong>Pick-up:</strong> <?php echo htmlspecialchars($r['pickup_date'] ?: '—'); ?><?php echo $r['pickup_time'] ? ' at ' . htmlspecialchars(substr($r['pickup_time'], 0, 5)) : ''; ?></p>
                            <p><strong>Return:</strong> <?php echo htmlspecialchars($r['return_date'] ?: '—'); ?><?php echo $r['return_time'] ? ' at ' . htmlspecialchars(substr($r['return_time'], 0, 5)) : ''; ?></p>
                            <p class="reservation-meta">Booked <?php echo htmlspecialchars(date("M j, Y", strtotime($r['created_at']))); ?></p>
                        </div>
                        <div class="reservation-status">
                            <span class="reservation-badge <?php echo $isCancelled ? 'status-cancelled' : 'status-confirmed'; ?>">
                                <?php echo $isCancelled ? 'Cancelled' : 'Confirmed'; ?>
                            </span>
                            <?php if (!$isCancelled): ?>
                                <button type="button" class="btn btn-outline-navy btn-sm cancel-reservation-btn" data-id="<?php echo (int)$r['id']; ?>">
                                    Cancel Reservation
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
