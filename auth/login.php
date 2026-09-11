<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require '../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In – <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .auth-page { padding: 80px 0; background: var(--bg); }
        .auth-form { max-width: 480px; margin: 0 auto; background: #0e0e0e; padding: 40px; border-radius: var(--radius); border: 1px solid var(--line); }
        .auth-form h1 { font-family: var(--font-display); text-align: center; margin-bottom: 30px; }
        .auth-form label { display: block; font-size: 12px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .auth-form input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; font-size: 14px; outline: none; margin-bottom: 16px; }
        .auth-form input:focus { border-color: var(--accent); }
        .auth-form input::placeholder { color: #666; }   /* placeholder color */
        .auth-form .btn { width: 100%; justify-content: center; margin-top: 8px; }
        .auth-form .error { color: var(--accent); font-size: 14px; margin-bottom: 12px; }
        .auth-form .success { color: #4ade80; font-size: 14px; margin-bottom: 12px; }
        .auth-form .remember { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .auth-form .remember input { width: auto; margin: 0; }
        .auth-form .remember label { margin: 0; font-size: 14px; text-transform: none; letter-spacing: normal; color: var(--text); }
        .auth-form .links { text-align: center; margin-top: 20px; color: var(--muted); }
        .auth-form .links a { color: var(--accent); }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<section class="auth-page">
    <div class="container">
        <div class="auth-form">
            <h1>LOG IN</h1>

            <?php if ($status === 'error' && $message): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($status === 'success' && $message): ?>
                <div class="success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" action="auth_function.php?redirect=<?= urlencode($redirect) ?>">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="james@example.com" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <div class="remember">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" name="login" class="btn btn--primary">LOG IN</button>
            </form>

            <div class="links">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>