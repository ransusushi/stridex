<?php
require_once '../database/config.php';
requireAdmin();

$pdo = getConnection();

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['role'];
    if (in_array($new_role, ['user', 'admin'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
        $message = '<div style="color: #4ade80; padding: 10px; background: #0e0e0e; border-radius: 4px; margin-bottom: 20px;">✅ User role updated!</div>';
    }
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div style="color: #4ade80; padding: 10px; background: #0e0e0e; border-radius: 4px; margin-bottom: 20px;">✅ User deleted!</div>';
    } else {
        $message = '<div style="color: var(--accent); padding: 10px; background: #0e0e0e; border-radius: 4px; margin-bottom: 20px;">⚠️ You cannot delete yourself.</div>';
    }
}

// Fetch all users with order count
$stmt = $pdo->query("
    SELECT u.*, COUNT(o.id) as order_count 
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users – Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <style>
        .admin-page { padding: 80px 0; background: var(--bg); }
        .admin-table { width: 100%; border-collapse: collapse; color: var(--text); }
        .admin-table th { text-align: left; padding: 12px 0; border-bottom: 1px solid var(--line); font-weight: 600; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); }
        .admin-table td { padding: 12px 0; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .admin-table .role-badge { padding: 4px 12px; border-radius: 4px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; }
        .role-admin { background: var(--accent); color: #000; }
        .role-user { background: #222; color: #fff; }
        .btn--small { padding: 4px 12px; font-size: 10px; }
        .btn--danger { background: #ff5a1f; color: #fff; }
        .btn--danger:hover { background: #e04a10; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .actions form { display: inline; }
        .actions select { padding: 4px 8px; background: rgba(255,255,255,.05); border: 1px solid var(--line); border-radius: var(--radius); color: #fff; font-size: 12px; outline: none; }
        .message { margin-bottom: 20px; }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="../index.php" class="logo"><img src="/stride/image/stridex-logo.png" alt="StrideX" style="height:50px;width:auto;"></a>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-page">
    <div class="container">
        <h1 class="section-title">Customers</h1>

        <?= $message ?? '' ?>

        <?php if (empty($users)): ?>
            <p>No users registered yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= e($u['first_name']) . ' ' . e($u['last_name']) ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td>
                                <span class="role-badge <?= $u['role'] === 'admin' ? 'role-admin' : 'role-user' ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="orders.php?user_id=<?= $u['id'] ?>" style="color: var(--accent);">
                                    <?= $u['order_count'] ?>
                                </a>
                            </td>
                            <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <form method="post">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <select name="role">
                                                <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                            <button type="submit" name="update_role" class="btn btn--primary btn--small">Update</button>
                                        </form>
                                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn--danger btn--small" onclick="return confirm('Delete this user?')">Delete</a>
                                    <?php else: ?>
                                        <span style="color: var(--muted); font-size: 11px;">(You)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

</body>
</html>