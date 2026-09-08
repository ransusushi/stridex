<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up – <?= e($site['brand']) ?></title>
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
        .auth-form .btn { width: 100%; justify-content: center; margin-top: 8px; }
        .auth-form .error { color: var(--accent); font-size: 14px; margin-bottom: 12px; }
        .auth-form .success { color: #4ade80; font-size: 14px; margin-bottom: 12px; }
        .auth-form .links { text-align: center; margin-top: 20px; color: var(--muted); }
        .auth-form .links a { color: var(--accent); }
    </style>
</head>
<body>

<?php include '../header.php'; ?>

<section class="auth-page">
    <div class="container">
        <div class="auth-form">
            <h1>SIGN UP</h1>

            <?php if ($status === 'error' && $message): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($status === 'success' && $message): ?>
                <div class="success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" action="auth_function.php">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password (min. 6 characters)</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="signup" class="btn btn--primary">CREATE ACCOUNT</button>
            </form>

            <div class="links">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </div>
    </div>
</section>

<?php include '../footer.php'; ?>
</body>
</html>