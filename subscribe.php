<?php
/**
 * subscribe.php — handles newsletter subscription submissions (AJAX
 * POST from script.js). Validates the email, then inserts it into
 * the MySQL `subscribers` table, gracefully handling duplicates via
 * the table's UNIQUE constraint.
 */

header('Content-Type: application/json');
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => "Please enter a valid email address."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
    $stmt->execute([":email" => $email]);
    echo json_encode(["ok" => true, "message" => "Thanks for subscribing!"]);
} catch (PDOException $e) {
    // MySQL error code 23000 = integrity constraint violation (duplicate email).
    if ($e->getCode() === '23000') {
        echo json_encode(["ok" => true, "message" => "You're already subscribed!"]);
    } else {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Could not save your subscription. Please try again."]);
    }
}