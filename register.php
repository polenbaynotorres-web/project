<?php
/**
 * register.php — handles registration (AJAX POST from script.js).
 * Validates via validation.php, hashes the password, inserts the
 * new user, then logs them in immediately (starts a session).
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

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

$errors = [];
$nameError = validate_name($name);
if ($nameError !== "") $errors[] = $nameError;

$emailError = validate_email($email);
if ($emailError !== "") $errors[] = $emailError;

$passwordError = validate_password($password, 8);
if ($passwordError !== "") $errors[] = $passwordError;

$matchError = validate_password_match($password, $confirm);
if ($matchError !== "" && $passwordError === "") $errors[] = $matchError;

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    // Clear message on duplicate email instead of a generic DB error.
    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([":email" => $email]);
    if ($check->fetch()) {
        http_response_code(422);
        echo json_encode(["ok" => false, "error" => "An account with that email already exists. Try logging in instead."]);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :hash)"
    );
    $stmt->execute([
        ":name"  => $name,
        ":email" => $email,
        ":hash"  => $hash,
    ]);

    $userId = $pdo->lastInsertId();

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$userId;
    $_SESSION['name']    = $name;
    $_SESSION['email']   = $email;

    echo json_encode([
        "ok" => true,
        "message" => "Account created! You're now logged in.",
        "user" => ["name" => $name, "email" => $email],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Could not create your account. Please try again."]);
}