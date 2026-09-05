<?php
/**
 * config.php — shared database connection (PDO + MySQL).
 *
 * Default values match a stock XAMPP/WAMP setup: MySQL on localhost,
 * user "root", no password. Change these four constants if your
 * setup differs (e.g. a password you set, or a remote host).
 *
 * Any file that needs the database does:
 *     require __DIR__ . '/config.php';
 * and then uses the $pdo variable.
 */

const DB_HOST = 'localhost';
const DB_NAME = 'torres_rental';
const DB_USER = 'root';
const DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        "ok" => false,
        "error" => "Could not connect to the database. Make sure MySQL is running and the 'torres_rental' database has been imported (see schema.sql).",
    ]);
    exit;
}