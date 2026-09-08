<?php
/**
 * logout.php — destroys the current session (AJAX POST from script.js).
 */

header('Content-Type: application/json');
require __DIR__ . '/session_init.php';

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

echo json_encode(["ok" => true, "message" => "You've been logged out."]);