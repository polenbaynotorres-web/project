<?php
/**
 * session_init.php — starts the PHP session with a long-lived cookie so
 * customers stay logged in across visits instead of being signed out
 * automatically (e.g. as soon as the browser is closed, or after a
 * short period of inactivity).
 *
 * Every file that reads or writes $_SESSION should:
 *     require __DIR__ . '/session_init.php';
 * instead of calling session_start() directly, so the cookie lifetime
 * and expiry rules stay consistent everywhere.
 */

if (session_status() === PHP_SESSION_NONE) {
    // How long a logged-in customer stays logged in without visiting the
    // site, in seconds. 30 days — long enough that closing the browser,
    // restarting the phone, or a short idle period never logs them out.
    $sessionLifetime = 60 * 60 * 24 * 30;

    // Make sure the server doesn't garbage-collect the session data
    // before the cookie itself expires.
    ini_set('session.gc_maxlifetime', (string)$sessionLifetime);

    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    // Refresh the cookie's expiry on every request so an active visitor
    // is never logged out mid-session just because the original cookie
    // was about to expire.
    if (isset($_COOKIE[session_name()])) {
        setcookie(
            session_name(),
            $_COOKIE[session_name()],
            time() + $sessionLifetime,
            '/',
            '',
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            true
        );
    }
}
