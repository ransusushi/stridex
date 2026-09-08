<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Site configuration ----------
$site = [
    'brand'   => 'StrideX',
    'tagline' => 'MOVE IN STYLE. LIVE WITHOUT LIMITS.',
    'year'    => date('Y'),
];

// ---------- Navigation ----------
$navLinks = [
    ['label' => 'Home',        'href' => '/stride/index.php'],
    ['label' => 'Shop',        'href' => '/stride/shop.php'],
    ['label' => 'Collections', 'href' => '/stride/nav/collection.php'],
    ['label' => 'About Us',    'href' => '/stride/nav/about.php'],   // ← changed
];

// ---------- Feature bullets ----------
$features = [
    ['icon'  => 'award', 'title' => 'PREMIUM QUALITY', 'text'  => 'High-grade materials for lasting comfort and durability.'],
    ['icon'  => 'bolt',  'title' => 'PERFORMANCE',     'text'  => 'Engineered for speed, agility, and stability on any surface.'],
    ['icon'  => 'box',   'title' => 'LIGHTWEIGHT',     'text'  => 'Move faster with precision-engineered, minimal weight design.'],
    ['icon'  => 'return','title' => 'EASY RETURNS',    'text'  => '30-day return policy for completely worry-free shopping.'],
];

// ---------- Footer ----------
$footerGroups = [
    'SHOP' => [
        ['label' => 'All Products', 'href' => '/stride/shop.php'],
        ['label' => 'Collections',  'href' => '/stride/nav/collection.php'],
    ],

    'COMPANY' => [
        ['label' => 'About Us',    'href' => '#about'],
        ['label' => 'Our Mission', 'href' => '#mission'],
        ['label' => 'Why StrideX', 'href' => '#why'],
    ],
    'SUPPORT' => [
        ['label' => 'Contact',           'href' => '#contact'],
        ['label' => 'Shipping & Returns', 'href' => '#shipping'],
        ['label' => 'FAQ',               'href' => '#faq'],
    ],
    'FOLLOW US' => [
        ['label' => 'Instagram', 'href' => '#'],
        ['label' => 'Facebook',  'href' => '#'],
        ['label' => 'TikTok',    'href' => '#'],
    ],
];

// ---------- Helpers ----------
function icon(string $name): string {
    $icons = [
        'award'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="9" r="5"/><path d="M8.5 13 7 21l5-3 5 3-1.5-8"/></svg>',
        'bolt'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/></svg>',
        'box'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/></svg>',
        'return' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>',
        'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
        'user'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
        'bag'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14l-1 13H6L5 7Z"/><path d="M9 7a3 3 0 1 1 6 0"/></svg>',
        'arrow'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>',
        'send'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/></svg>',
        'check'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>',
    ];
    return $icons[$name] ?? '';
}

function star_row(int $rating, int $max = 5): string {
    $html = '';
    for ($i = 1; $i <= $max; $i++) {
        $filled = $i <= $rating;
        $html .= '<span class="star ' . ($filled ? 'is-filled' : '') . '">' .
            ($filled
                ? '<svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18l-5.9 3 1.2-6.5L2.5 9.9 9.1 9Z"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="m12 3 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18l-5.9 3 1.2-6.5L2.5 9.9 9.1 9Z"/></svg>'
            ) .
        '</span>';
    }
    return $html;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// ---------- Load product data ----------
require_once __DIR__ . '/../data.php';

// ---------- Database connection ----------
function getConnection(): PDO
{
   $host = '127.0.0.1';
    $port = '3307';
    $db   = 'stridex_db';   
    $user = 'root';         
    $pass = '';           
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", 
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// ---------- Authentication Functions ----------

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function getCurrentUser()
{
    if (!isLoggedIn()) return null;
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function logout()
{
    unset($_SESSION['user_id']);
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    session_destroy();
}

function isAdmin(): bool
{
    if (!isLoggedIn()) return false;
    $user = getCurrentUser();
    return ($user && isset($user['role']) && $user['role'] === 'admin');
}

function requireAdmin()
{
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit;
    }
}