<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user['username'];
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter your credentials.';
    }
}

$settings = getSettings();
$logoPath = assetUrl($settings['logo_path'] ?? '');
$agencyName = $settings['agency_name'] ?? 'Nextfram';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= htmlspecialchars($agencyName) ?></title>
    <?php if ($logoPath): ?>
    <link rel="icon" type="image/png" href="<?= $logoPath ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">

<div class="admin-login-page">
    <div class="login-card">
        <div class="login-logo">
            <?php if ($logoPath): ?>
                <img src="<?= $logoPath ?>" alt="<?= htmlspecialchars($agencyName) ?>">
            <?php else: ?>
                <div class="logo-text"><?= htmlspecialchars($agencyName) ?></div>
            <?php endif; ?>
            <p class="login-subtitle">Admin Panel</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="validate-form" novalidate>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus autocomplete="username">
                <div class="form-error">This field is required.</div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
                <div class="form-error">This field is required.</div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">
                Sign In
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>

        <div style="text-align:center;margin-top:1.5rem;">
            <a href="<?= SITE_URL ?>/index.php" style="color:var(--text-dim);font-size:0.85rem;transition:var(--transition);">← Back to website</a>
        </div>
    </div>
</div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
