<?php
/**
 * contact_function.php — handles the Contact page's message form
 * (AJAX POST from script.js). Validates via validation.php, then
 * inserts into the `messages` table.
 */

header('Content-Type: application/json');
require __DIR__ . '/validation.php';
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
$nameError = validate_name($name);
if ($nameError !== "") $errors[] = $nameError;

$emailError = validate_email($email);
if ($emailError !== "") $errors[] = $emailError;

if ($subject === '') $errors[] = "Subject is required.";
if ($message === '') $errors[] = "Message is required.";

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)"
    );
    $stmt->execute([
        ":name"    => $name,
        ":email"   => $email,
        ":subject" => $subject,
        ":message" => $message,
    ]);

    echo json_encode([
        "ok" => true,
        "message" => "Thanks, " . $name . "! We've received your message and will get back to you soon.",
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Could not send your message. Please try again."]);
}