<?php
/**
 * create_admin.php — ONE-TIME setup page to create the first admin
 * account. Only works while the `admins` table is empty, so it can't
 * be used to create extra admins once one exists. Delete this file
 * after creating your admin account.
 */

require __DIR__ . '/validation.php';
require __DIR__ . '/config.php'; // provides $pdo

$existingCount = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();

$error = '';
$success = false;

if ($existingCount === 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    $errors = [];
    if (strlen($username) < 3) $errors[] = "Username must be at least 3 characters.";
    $passwordError = validate_password($password, 8);
    if ($passwordError !== "") $errors[] = $passwordError;
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (:u, :h)");
        $stmt->execute([":u" => $username, ":h" => $hash]);
        $success = true;
    } else {
        $error = implode(" ", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Admin Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-page-wrap">
    <div class="auth-card">
        <?php if ($existingCount > 0): ?>
            <h1>Setup Already Complete</h1>
            <p class="auth-card-sub">An admin account already exists. For security, delete <code>create_admin.php</code> from your project now.</p>
            <a href="admin_login.php" class="btn btn-primary btn-block">Go to Admin Login</a>
        <?php elseif ($success): ?>
            <h1>Admin Created</h1>
            <p class="auth-card-sub">Your admin account is ready. <strong>Please delete create_admin.php now</strong> — leaving it in place is a security risk.</p>
            <a href="admin_login.php" class="btn btn-primary btn-block">Go to Admin Login</a>
        <?php else: ?>
            <h1>Create Admin Account</h1>
            <p class="auth-card-sub">One-time setup. This form disables itself after the first admin is created.</p>
            <?php if ($error): ?><p class="modal-error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
            <form method="POST">
                <label>Username<input type="text" name="username" required></label>
                <label>Password<input type="password" name="password" required minlength="8"></label>
                <label>Confirm Password<input type="password" name="confirm" required minlength="8"></label>
                <button type="submit" class="btn btn-primary btn-block">Create Admin</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>