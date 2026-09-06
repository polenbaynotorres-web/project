<?php
/**
 * session_check.php — reports whether the current visitor is logged
 * in. Called via AJAX GET from script.js on every page load, so the
 * header can show "Hi, Name / Log Out" instead of "Log In / Register"
 * without a full page reload.
 */

header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "ok" => true,
        "loggedIn" => true,
        "user" => ["name" => $_SESSION['name'], "email" => $_SESSION['email']],
    ]);
} else {
    echo json_encode(["ok" => true, "loggedIn" => false]);
}