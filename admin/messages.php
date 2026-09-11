<?php
require_once '../database/config.php';
requireDatabase(); // Kick user out if MySQL is down (shows maintenance page)

$pageTitle = "Messages";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php?status=deleted');
    exit;
}

// Handle Mark as Read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit;
}

// Fetch messages
$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Messages</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Simple Admin CSS */
        body { background: #000; color: #f5f5f5; font-family: 'Inter', sans-serif; margin: 0; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { font-family: 'Bebas Neue', sans-serif; font-size: 48px; margin-bottom: 20px; letter-spacing: .05em; }
        
        /* Simple Nav */
        .admin-nav { margin-bottom: 30px; }
        .admin-nav a { color: #ff5a1f; text-decoration: none; margin-right: 20px; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: .1em; }
        .admin-nav a:hover { text-decoration: underline; }

        /* Alerts */
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: rgba(74, 222, 128, 0.1); border: 1px solid #4ade80; color: #4ade80; }

        /* Table */
        .table-wrap { background: #0e0e0e; border: 1px solid #222; border-radius: 8px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 16px; border-bottom: 1px solid #222; font-size: 14px; }
        th { background: #151515; color: #888; text-transform: uppercase; font-size: 11px; letter-spacing: .1em; }
        tr:hover { background: rgba(255,255,255,0.02); }
        tr.unread { font-weight: 600; background: rgba(255, 90, 31, 0.05); }
        
        /* Buttons */
        .btn-small { padding: 6px 12px; font-size: 11px; text-transform: uppercase; background: #333; color: #fff; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px; transition: .2s; }
        .btn-small:hover { background: #555; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .empty { padding: 40px; text-align: center; color: #666; }
    </style>
</head>
<body>
<div class="container">
    
    <!-- Simple Manual Navigation -->
    <div class="admin-nav">
        <a href="index.php">← Back to Dashboard</a>
    </div>

    <h1>Contact Messages</h1>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert alert-success">✅ Message deleted successfully.</div>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="7" class="empty">No messages yet. When someone submits the contact form, it will appear here.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?= $msg['is_read'] ? '' : 'unread' ?>">
                            <td><?= $msg['is_read'] ? '<span style="color:#888;">Read</span>' : '<span style="color:#ff5a1f;">Unread</span>' ?></td>
                            <td><?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?></td>
                            <td><?= htmlspecialchars($msg['name']) ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>" style="color:#ff5a1f;"><?= htmlspecialchars($msg['email']) ?></a></td>
                            <td><?= htmlspecialchars($msg['subject']) ?></td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= htmlspecialchars($msg['message']) ?>
                            </td>
                            <td>
                                <?php if (!$msg['is_read']): ?>
                                    <a href="messages.php?read=<?= $msg['id'] ?>" class="btn-small">Mark Read</a>
                                <?php endif; ?>
                                <a href="messages.php?delete=<?= $msg['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>