<?php
/**
 * admin_login.php — staff login. Uses a separate session key
 * ($_SESSION['admin_id']) from customer logins ($_SESSION['user_id']),
 * so the two are independent.
 */

session_start();
require __DIR__ . '/config.php'; // provides $pdo

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :u");
        $stmt->execute([":u" => $username]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $error = "Incorrect username or password.";
        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Torres Vehicle Rental</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-page-wrap">
    <div class="auth-card">
        <h1>Admin Login</h1>
        <p class="auth-card-sub">Staff access only — manage fleet color/stock status.</p>
        <?php if ($error): ?><p class="modal-error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        <form method="POST">
            <label>Username<input type="text" name="username" required autofocus></label>
            <label>Password<input type="password" name="password" required></label>
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
    </div>
</div>
</body>
</html>