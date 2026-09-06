<?php
/**
 * subscribe.php — handles newsletter subscription submissions (AJAX
 * POST from script.js). Validation lives in validation.php.
 */

header('Content-Type: application/json');
require __DIR__ . '/validation.php';
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

$email = trim($_POST['email'] ?? '');

$emailError = validate_email($email);
if ($emailError !== "") {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => $emailError]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (:email)");
    $stmt->execute([":email" => $email]);
    echo json_encode(["ok" => true, "message" => "Thanks for subscribing!"]);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode(["ok" => true, "message" => "You're already subscribed!"]);
    } else {
        http_response_code(500);
        echo json_encode(["ok" => false, "error" => "Could not save your subscription. Please try again."]);
    }
}