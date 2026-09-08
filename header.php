<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($site['brand']) ?> — Built for the Streets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stridex.css">
    <style>
        .cta-form .form-group {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .cta-form .form-group input {
            width: 100%;
            padding: 14px 20px;
            background: rgba(255, 255, 255, .05);
            border: 1px solid var(--line, rgba(255,255,255,.12));
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color .25s ease;
        }
        .cta-form .form-group input:focus {
            border-color: #ff5a1f;
        }
        .cta-form .form-group input::placeholder {
            color: #9a9a9a;
        }
        .error-message {
            color: #ff5a1f;
            font-size: 12px;
            min-height: 18px;
            display: block;
        }
        @media (max-width: 768px) {
            .cta-form {
                flex-direction: column;
            }
            .cta-form .form-group {
                min-width: auto;
                width: 100%;
            }
            .cta-form .btn {
                width: 100%;
                justify-content: center;
            }
        }
    .main-nav ul {
        display: flex !important;
        gap: 44px !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .main-nav ul li {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .main-nav ul li a {
        white-space: nowrap !important;
        display: inline-block !important;
    }
</style>
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="#home" class="logo"><img src="/stride/image/stridex-logo.png" alt="<?= e($site['brand']) ?>" style="height:50px;width:auto;"></a>
        <nav class="main-nav" aria-label="Primary">
            <ul>
                <?php foreach ($navLinks as $link): ?>
                    <li><a href="<?= e($link['href']) ?>"><?= e(strtoupper($link['label'])) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="header-actions">
           <a href="search.php" class="icon-btn" aria-label="Search"><?= icon('search') ?></a>
            <?php if (isLoggedIn()): ?>
    <a href="profile.php" class="icon-btn" aria-label="Account"><?= icon('user') ?></a>
    <a href="login/logout.php" class="icon-btn" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .1em;">Logout</a>
<?php else: ?>
    <a href="login/login.php" class="icon-btn" aria-label="Log In"><?= icon('user') ?></a>
<?php endif; ?>
            
            <a href="cart.php" class="icon-btn cart" aria-label="Cart">
    <?= icon('bag') ?>
    <span class="cart-count">
        <?php 
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        echo $count;
        ?>
    </span>
</a>
        </div>
    </div>
</header>