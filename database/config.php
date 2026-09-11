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
    ['label' => 'Shop',        'href' => '/stride/pages/shop.php'],
    ['label' => 'Collections', 'href' => '/stride/nav/collection.php'],
    ['label' => 'About Us',    'href' => '/stride/nav/about.php'],
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
        ['label' => 'All Products', 'href' => '/stride/pages/shop.php'],
        ['label' => 'Collections',  'href' => '/stride/nav/collection.php'],
    ],
    'COMPANY' => [
        ['label' => 'About Us',    'href' => '/stride/nav/about.php'],
        ['label' => 'Our Mission', 'href' => '/stride/index.php#mission'],
        ['label' => 'Why StrideX', 'href' => '/stride/index.php#why'],
    ],
    'SUPPORT' => [
        ['label' => 'Contact',           'href' => '/stride/nav/about.php#contact'],
        ['label' => 'Shipping & Returns', 'href' => '/stride/nav/about.php#shipping'],
        ['label' => 'FAQ',               'href' => '/stride/nav/about.php#faq'],
    ],
    'FOLLOW US' => [
        ['label' => 'Facebook',  'href' => 'https://www.facebook.com/uknow.ecnal69'],
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

// ---------- Database connection (THROWS on failure, no exit) ----------
function getConnection(): PDO
{
    $host = '127.0.0.1';
    $port = '3306';
    $db   = 'stridex_db';
    $user = 'root';
    $pass = '';

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

/**
 * Call this at the top of any page that NEEDS the database (login, cart, admin, checkout).
 * It tries to connect. If MySQL is down, it shows the friendly maintenance page and stops.
 */
function requireDatabase(): void
{
    try {
        getConnection();
    } catch (PDOException $e) {
        if (defined('ERROR_LOG_FILE')) {
            file_put_contents(
                ERROR_LOG_FILE,
                "[" . date('Y-m-d H:i:s') . "] DATABASE ERROR: " . $e->getMessage() . "\n",
                FILE_APPEND
            );
        }
        showMaintenancePage();
        exit;
    }
}

function showMaintenancePage()
{
    if (headers_sent()) return;
    http_response_code(503);
    header('Retry-After: 60');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>StrideX — Temporarily Unavailable</title>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">';
    echo '<style>
        body { background: #000; color: #f5f5f5; font-family: "Inter", system-ui, sans-serif; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; text-align: center; padding: 20px; }
        .box { max-width: 520px; }
        .icon { font-size: 64px; margin-bottom: 20px; color: #ff5a1f; }
        h1 { font-family: "Bebas Neue", sans-serif; font-size: 72px; letter-spacing: .05em; text-transform: uppercase; margin: 0 0 16px; color: #fff; line-height: 1; }
        h1 span { color: #ff5a1f; }
        p { color: #9a9a9a; font-size: 16px; line-height: 1.7; margin: 0 0 24px; }
        .btn { display: inline-block; padding: 14px 28px; background: #fff; color: #000; text-decoration: none; font-size: 12px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; border-radius: 4px; transition: all .25s ease; }
        .btn:hover { background: #ff5a1f; color: #fff; transform: translateY(-2px); }
        .small { color: #555; font-size: 12px; margin-top: 32px; letter-spacing: .1em; }
    </style></head><body>';
    echo '<div class="box">';
    echo '<div class="icon">⚠️</div>';
    echo '<h1>WE&rsquo;LL BE <span>RIGHT BACK</span></h1>';
    echo '<p>Sorry, the site is temporarily unavailable.<br>We&rsquo;re doing a quick tune‑up — please try again shortly.</p>';
    echo '<a href="/stride/index.php" class="btn">TRY AGAIN</a>';
    echo '<p class="small">ERROR 503 &middot; SERVICE UNAVAILABLE</p>';
    echo '</div></body></html>';
}

// ---------- Authentication Functions ----------
function isLoggedIn(): bool { return isset($_SESSION['user_id']); }

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

// ---------- Cart helper ----------
function getProductById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ---------- Revenue stats ----------
function getRevenueStats(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid','shipped','delivered')");
    $totalRevenue = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid','shipped','delivered') AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $monthlyRevenue = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('paid','shipped','delivered')");
    $orderCount = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT id, total, status, created_at FROM orders ORDER BY id DESC LIMIT 5");
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'total_revenue'   => (float) $totalRevenue,
        'monthly_revenue' => (float) $monthlyRevenue,
        'order_count'     => (int) $orderCount,
        'recent_orders'   => $recentOrders,
    ];
}

// ---------- Product cache ----------
function cacheProducts(): void
{
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cacheFile = __DIR__ . '/../cache/products.json';
        file_put_contents($cacheFile, json_encode($products, JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        // silently fail if DB is down
    }
}

function loadProducts($limit = null, $gender = null): array
{
    try {
        $pdo = getConnection();
        $sql = "SELECT * FROM products";
        $params = [];

        if ($gender !== null) {
            $sql .= " WHERE gender = ? OR gender = 'unisex'";
            $params[] = $gender;
        }
        $sql .= " ORDER BY id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return loadProductsFromCache($limit, $gender);
    }
}

function loadProductsFromCache($limit = null, $gender = null): array
{
    $cacheFile = __DIR__ . '/../cache/products.json';

    if (!file_exists($cacheFile)) {
        return [];
    }

    $products = json_decode(file_get_contents($cacheFile), true);

    if (!is_array($products)) {
        return [];
    }

    if ($gender !== null) {
        $products = array_values(array_filter($products, function ($p) use ($gender) {
            return ($p['gender'] ?? '') === $gender || ($p['gender'] ?? '') === 'unisex';
        }));
    }

    if ($limit !== null) {
        $products = array_slice($products, 0, $limit);
    }

    return $products;
}