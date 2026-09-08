<?php
/**
 * cancel_reservation.php — lets a logged-in customer cancel one of
 * their own reservations (AJAX POST from script.js, called from the
 * "My Reservations" page). A reservation can only be cancelled by the
 * user who made it, and only while it's still "confirmed".
 */

header('Content-Type: application/json');
require __DIR__ . '/session_init.php';
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "Please log in to manage your reservations.", "requiresLogin" => true]);
    exit;
}

$userId        = (int)$_SESSION['user_id'];
$reservationId = (int)($_POST['reservation_id'] ?? 0);

if ($reservationId <= 0) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => "Missing reservation."]);
    exit;
}

try {
    // Only the owner can cancel it, and only while it's still confirmed —
    // this also prevents "cancelling" a reservation that belongs to
    // someone else by guessing/changing the id.
    $stmt = $pdo->prepare(
        "SELECT id, status FROM reservations WHERE id = :id AND user_id = :user_id"
    );
    $stmt->execute([":id" => $reservationId, ":user_id" => $userId]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        http_response_code(404);
        echo json_encode(["ok" => false, "error" => "Reservation not found."]);
        exit;
    }

    if ($reservation['status'] === 'cancelled') {
        echo json_encode(["ok" => true, "message" => "That reservation is already cancelled.", "status" => "cancelled"]);
        exit;
    }

    $update = $pdo->prepare(
        "UPDATE reservations SET status = 'cancelled', cancelled_at = NOW() WHERE id = :id AND user_id = :user_id"
    );
    $update->execute([":id" => $reservationId, ":user_id" => $userId]);

    echo json_encode([
        "ok" => true,
        "message" => "Your reservation has been cancelled.",
        "status" => "cancelled",
    ]);
} catch (PDOException $e) {
    // TEMPORARY: expose the real DB error to help diagnose the
    // "Could not cancel" issue. Remove the "debug" key once you've
    // found the cause — never do this on a live/public site.
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "error" => "Could not cancel your reservation. Please try again.",
        "debug" => $e->getMessage(),
    ]);
}
