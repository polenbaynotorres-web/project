<?php
/**
 * reserve.php — handles reservation form submissions (AJAX POST from
 * script.js). Requires the customer to be logged in — name/email come
 * from the session (set by login.php/register.php), not the form, so
 * they can't be spoofed. Phone, car, location, and pick-up/return
 * date & time come from the form. A reservation is only saved once
 * every one of those fields is present and valid — see validation.php.
 */

header('Content-Type: application/json');
require __DIR__ . '/session_init.php';
require __DIR__ . '/validation.php';
require __DIR__ . '/data.php';    // provides $locations, $fleet
require __DIR__ . '/config.php';  // provides $pdo
require __DIR__ . '/stock.php';   // provides in_stock_colors()

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

$userId      = (int)$_SESSION['user_id'];
$name        = $_SESSION['name'];
$email       = $_SESSION['email'];
$phone       = trim($_POST['phone']       ?? '');
$car         = trim($_POST['car']         ?? '');
$color       = trim($_POST['color']       ?? '');
$location    = trim($_POST['location']    ?? '');
$pickupDate  = trim($_POST['pickup_date'] ?? '');
$pickupTime  = trim($_POST['pickup_time'] ?? '');
$returnDate  = trim($_POST['return_date'] ?? '');
$returnTime  = trim($_POST['return_time'] ?? '');

$errors = [];

$phoneError = validate_phone($phone);
if ($phoneError !== "") $errors[] = $phoneError;

if (trim($car) === '') {
    $errors[] = "Please choose a vehicle.";
} else {
    // Match the posted car name against the fleet so we can validate the
    // color against that specific car's currently in-stock colors,
    // rather than trusting whatever was submitted from the client.
    $carEntry = null;
    foreach ($fleet as $fleetCar) {
        if ($fleetCar['name'] === $car) {
            $carEntry = $fleetCar;
            break;
        }
    }
    if ($carEntry === null) {
        $errors[] = "Please choose a valid vehicle.";
    } elseif (trim($color) === '') {
        $errors[] = "Please select a color.";
    } else {
        $stockMap = load_vehicle_stock($pdo);
        $availableColorNames = array_column(in_stock_colors($carEntry, $stockMap), 'name');
        if (!in_array($color, $availableColorNames, true)) {
            $errors[] = "Please select a color that's currently in stock.";
        }
    }
}

$locationError = validate_location($location, $locations);
if ($locationError !== "") $errors[] = $locationError;

$pickupDateError = validate_date($pickupDate, "pick-up date");
if ($pickupDateError !== "") $errors[] = $pickupDateError;

$pickupTimeError = validate_time($pickupTime, "pick-up time");
if ($pickupTimeError !== "") $errors[] = $pickupTimeError;

$returnDateError = validate_date($returnDate, "return date");
if ($returnDateError !== "") $errors[] = $returnDateError;

$returnTimeError = validate_time($returnTime, "return time");
if ($returnTimeError !== "") $errors[] = $returnTimeError;

// Only check the date/time ordering once every individual field is valid —
// otherwise this would compare garbage input and produce a confusing message.
if ($pickupDateError === "" && $pickupTimeError === "" && $returnDateError === "" && $returnTimeError === "") {
    $orderError = validate_date_order($pickupDate, $pickupTime, $returnDate, $returnTime);
    if ($orderError !== "") $errors[] = $orderError;
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "error" => implode(" ", $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO reservations
            (user_id, name, phone, email, car, color, location, pickup_date, pickup_time, return_date, return_time, status)
         VALUES
            (:user_id, :name, :phone, :email, :car, :color, :location, :pickup_date, :pickup_time, :return_date, :return_time, 'confirmed')"
    );
    $stmt->execute([
        ":user_id"     => $userId,
        ":name"        => $name,
        ":phone"       => $phone,
        ":email"       => $email,
        ":car"         => $car !== '' ? $car : "Not specified",
        ":color"       => $color,
        ":location"    => $location,
        ":pickup_date" => $pickupDate,
        ":pickup_time" => $pickupTime,
        ":return_date" => $returnDate,
        ":return_time" => $returnTime,
    ]);

    echo json_encode([
        "ok" => true,
        "message" => "Reservation received! We'll contact you shortly to confirm.",
        "reservation" => [
            "id"           => $pdo->lastInsertId(),
            "name"         => $name,
            "phone"        => $phone,
            "email"        => $email,
            "car"          => $car !== '' ? $car : "Not specified",
            "color"        => $color,
            "location"     => $locations[$location] ?? $location,
            "pickup_date"  => $pickupDate,
            "pickup_time"  => $pickupTime,
            "return_date"  => $returnDate,
            "return_time"  => $returnTime,
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Could not save your reservation. Please try again."]);
}
