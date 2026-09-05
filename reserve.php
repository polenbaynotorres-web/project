<?php
/**
 * reserve.php — handles reservation form submissions (AJAX POST
 * from script.js). Validates input server-side, then inserts the
 * reservation into the MySQL `reservations` table via a prepared
 * statement. Responds with JSON so script.js can show a real
 * success/error state instead of a fake one.
 */

header('Content-Type: application/json');
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

$name  = trim($_POST['name']  ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$car   = trim($_POST['car']   ?? '');

// Basic server-side validation — never trust the client alone.
$errors = [];
if ($name === '') {
    $errors[] = "Full name is required.";
}
if ($phone === '' || !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    $errors[] = "Please enter a valid mobile number.";
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO reservations (name, phone, email, car) VALUES (:name, :phone, :email, :car)"
    );
    $stmt->execute([
        ":name"  => $name,
        ":phone" => $phone,
        ":email" => $email,
        ":car"   => $car !== '' ? $car : "Not specified",
    ]);

    echo json_encode([
        "ok" => true,
        "message" => "Reservation received! We'll contact you shortly to confirm.",
        "reservation" => [
            "id"    => $pdo->lastInsertId(),
            "name"  => $name,
            "phone" => $phone,
            "email" => $email,
            "car"   => $car !== '' ? $car : "Not specified",
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Could not save your reservation. Please try again."]);
}