<?php
/**
 * StrideX — Header + Footer only
 */

$site = [
    'brand'   => 'StrideX',
    'tagline' => 'MOVE IN STYLE. LIVE WITHOUT LIMITS.',
    'year'    => date('Y'),
];

$navLinks = [
    ['label' => 'Home',        'href' => 'stride.php'],
    ['label' => 'Shop',        'href' => '#shop'],
    ['label' => 'Men',         'href' => 'men.php'],
    ['label' => 'Women',       'href' => 'women.php'],
    ['label' => 'Collections', 'href' => '#collections'],
    ['label' => 'About Us',    'href' => '#about'],
];

$footerGroups = [
    'SHOP' => [
        ['label' => 'Men', 'href' => '#men'],
        ['label' => 'Women', 'href' => '#women'],
        ['label' => 'Collections', 'href' => '#collections'],
    ],
    'COMPANY' => [
        ['label' => 'About Us', 'href' => '#about'],
        ['label' => 'Our Mission', 'href' => '#mission'],
        ['label' => 'Why StrideX', 'href' => '#why'],
    ],
    'SUPPORT' => [
        ['label' => 'Contact', 'href' => '#contact'],
        ['label' => 'Shipping & Returns', 'href' => '#shipping'],
        ['label' => 'FAQ', 'href' => '#faq'],
    ],
    'FOLLOW' => [
        ['label' => 'Instagram', 'href' => '#'],
        ['label' => 'Facebook', 'href' => '#'],
        ['label' => 'TikTok', 'href' => '#'],
    ],
];

function icon(string $name): string {
    $icons = [
        'send' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>',
    ];
    return $icons[$name] ?? '';
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
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
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="#home" class="logo">
            <img src="image/stridex-logo.png" alt="<?= e($site['brand']) ?>">
        </a>
        <nav class="main-nav" aria-label="Primary">
            <ul>
                <?php foreach ($navLinks as $link): ?>
                    <li><a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- ====== All main content removed ====== -->

<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand-custom">
            <img src="image/stridex-logo.png" alt="<?= e($site['brand']) ?>" class="footer-logo-img">
            <p class="footer-tag"><?= e($site['tagline']) ?></p>
        </div>

        <div class="footer-links">
            <?php foreach ($footerGroups as $title => $links): ?>
                <div class="footer-col">
                    <h4 class="footer-heading"><?= e($title) ?></h4>
                    <ul>
                        <?php foreach ($links as $link): ?>
                            <li><a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>

            <div class="footer-col footer-subscribe">
                <h4 class="footer-heading">GET THE DROP</h4>
                <form class="subscribe-form" action="#" method="post">
                    <input type="email" placeholder="your@email.com" required>
                    <button type="submit" aria-label="Subscribe"><?= icon('send') ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>&copy; <?= e((string)$site['year']) ?> <?= e(strtoupper($site['brand'])) ?>. All rights reserved.</p>
        <p class="legal">
            <a href="#">Privacy</a>
            <span>&middot;</span>
            <a href="#">Terms</a>
            <span>&middot;</span>
            <a href="#">Cookies</a>
        </p>
    </div>
</footer>

</body>
</html>