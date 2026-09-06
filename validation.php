<?php
/**
 * validation.php — shared validation functions.
 * Required by every endpoint that processes form input (login,
 * register, reserve, subscribe) so validation rules live in ONE
 * place instead of being copy-pasted into every file.
 *
 * Every function returns an error message string when the input is
 * invalid, or an empty string "" when it's valid — so callers do:
 *
 *     $error = validate_email($email);
 *     if ($error !== "") { ...collect it... }
 */

function validate_name($name) {
    if (trim($name) === '') {
        return "Full name is required.";
    }
    if (strlen(trim($name)) < 2) {
        return "Full name must be at least 2 characters.";
    }
    return "";
}

function validate_email($email) {
    if (trim($email) === '') {
        return "Email is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Please enter a valid email address.";
    }
    return "";
}

function validate_password($password, $min_length = 8) {
    if ($password === '') {
        return "Password is required.";
    }
    if (strlen($password) < $min_length) {
        return "Password must be at least $min_length characters.";
    }
    return "";
}

function validate_password_match($password, $confirm) {
    if ($password !== $confirm) {
        return "Passwords do not match.";
    }
    return "";
}

function validate_phone($phone) {
    if (trim($phone) === '') {
        return "Mobile number is required.";
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
        return "Please enter a valid mobile number.";
    }
    return "";
}