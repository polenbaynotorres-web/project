<?php
/**
 * login.php — handles login (AJAX POST from script.js).
 * Verifies email + password against the stored hash and starts a
 * session on success. Uses validation.php for input checks.
 */

header('Content-Type: application/json');
require __DIR__ . '/session_init.php';
require __DIR__ . '/validation.php';
require __DIR__ . '/config.php'; // provides $pdo

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["ok" => false, "error" => "Method not allowed."]);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
$emailError = validate_email($email);
if ($emailError !== "") $errors[] = $emailError;
if (trim($password) === '') $errors[] = "Password is required.";

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM users WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch();

    // Deliberately vague on failure — don't reveal whether the email exists.
    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(["ok" => false, "error" => "Incorrect email or password."]);
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];

    echo json_encode([
        "ok" => true,
        "message" => "Welcome back, " . $user['name'] . "!",
        "user" => ["name" => $user['name'], "email" => $user['email']],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Could not log you in. Please try again."]);
}