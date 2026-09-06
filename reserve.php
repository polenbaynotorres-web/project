<?php
/**
 * reserve.php — handles reservation form submissions (AJAX POST from
 * script.js). Requires the customer to be logged in — name/email come
 * from the session (set by login.php/register.php), not the form, so
 * they can't be spoofed. Only phone number and car come from the form.
 * Validation rules live in validation.php.
 */

header('Content-Type: application/json');
session_start();
require __DIR__ . '/validation.php';
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "Please log in to reserve a vehicle.", "requiresLogin" => true]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$name   = $_SESSION['name'];
$email  = $_SESSION['email'];
$phone  = trim($_POST['phone'] ?? '');
$car    = trim($_POST['car']   ?? '');

$errors = [];
$phoneError = validate_phone($phone);
if ($phoneError !== "") $errors[] = $phoneError;

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO reservations (user_id, name, phone, email, car) VALUES (:user_id, :name, :phone, :email, :car)"
    );
    $stmt->execute([
        ":user_id" => $userId,
        ":name"    => $name,
        ":phone"   => $phone,
        ":email"   => $email,
        ":car"     => $car !== '' ? $car : "Not specified",
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