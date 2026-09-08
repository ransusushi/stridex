<?php
require_once 'database/config.php';

// Protect page: require login
if (!isLoggedIn()) {
    header('Location: login/login.php?redirect=profile.php');
    exit;
}

$user = getCurrentUser();
$pdo = getConnection();

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = 'Email is already taken by another user.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $_SESSION['user_id']]);
            $success = 'Profile updated successfully!';
            $user = getCurrentUser(); // refresh
        }
    }
}

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile – <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stridex.css">
    <style>
        .profile-page { padding: 80px 0; background: var(--bg); }
        .profile-card { max-width: 600px; margin: 0 auto; background: #0e0e0e; padding: 30px; border-radius: var(--radius); border: 1px solid var(--line); }
        .profile-card h1 { font-family: var(--font-display); text-transform: uppercase; text-align: center; margin: 0 0 30px; }
        .profile-card .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--line); }
        .profile-card .info-row .label { color: var(--muted); font-size: 14px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; }
        .profile-card .info-row .value { color: var(--text); font-size: 16px; }
        .profile-card label { display: block; font-size: 12px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .profile-card input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; font-size: 14px; outline: none; margin-bottom: 16px; }
        .profile-card input:focus { border-color: var(--accent); }
        .profile-card .btn { width: 100%; justify-content: center; margin-top: 8px; }
        .profile-card .error { color: var(--accent); font-size: 14px; margin-bottom: 12px; }
        .profile-card .success { color: #4ade80; font-size: 14px; margin-bottom: 12px; }
        .edit-toggle { text-align: center; margin-top: 20px; }
        .edit-toggle a { color: var(--accent); text-decoration: underline; cursor: pointer; }
        .edit-form { display: none; margin-top: 20px; }
        .order-history { max-width: 800px; margin: 40px auto 0; }
        .order-history h2 { font-family: var(--font-display); text-transform: uppercase; margin-bottom: 20px; }
        .order-history table { width: 100%; border-collapse: collapse; }
        .order-history th { text-align: left; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .order-history td { padding: 10px 0; border-bottom: 1px solid var(--line); }
        .order-history .btn--small { padding: 4px 12px; font-size: 10px; }
        .status-pending { color: #f5b342; }
        .status-paid { color: #4ade80; }
        .status-shipped { color: #60a5fa; }
        .status-delivered { color: #a78bfa; }
        .no-orders { color: var(--muted); padding: 20px 0; text-align: center; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<section class="profile-page">
    <div class="container">
        <div class="profile-card">
            <h1>My Profile</h1>

            <?php if ($error): ?>
                <div class="error"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= e($success) ?></div>
            <?php endif; ?>

            <!-- View Mode -->
            <div id="view-mode">
                <div class="info-row">
                    <span class="label">First Name</span>
                    <span class="value"><?= e($user['first_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Last Name</span>
                    <span class="value"><?= e($user['last_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value"><?= e($user['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Joined</span>
                    <span class="value"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Role</span>
                    <span class="value"><?= ucfirst(e($user['role'] ?? 'User')) ?></span>
                </div>
                <div class="edit-toggle">
                    <a onclick="toggleEdit()">Edit Profile</a>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="edit-mode" class="edit-form">
                <form method="post">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= e($user['first_name']) ?>" required>

                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= e($user['last_name']) ?>" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" required>

                    <button type="submit" name="update_profile" class="btn btn--primary">Save Changes</button>
                </form>
                <div class="edit-toggle" style="margin-top:10px;">
                    <a onclick="toggleEdit()">Cancel</a>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="order-history">
            <h2>Your Transactions</h2>
            <?php if (empty($orders)): ?>
                <p class="no-orders">You haven't placed any orders yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>#<?= $o['id'] ?></td>
                                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                <td>$<?= number_format($o['total'], 2) ?></td>
                                <td class="status-<?= $o['status'] ?>"><?= strtoupper($o['status']) ?></td>
                                <td>
                                    <a href="order-detail.php?id=<?= $o['id'] ?>" class="btn btn--primary btn--small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    function toggleEdit() {
        var view = document.getElementById('view-mode');
        var edit = document.getElementById('edit-mode');
        if (view.style.display === 'none') {
            view.style.display = 'block';
            edit.style.display = 'none';
        } else {
            view.style.display = 'none';
            edit.style.display = 'block';
        }
    }
</script>

<?php include 'footer.php'; ?>
</body>
</html>