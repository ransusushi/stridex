<?php
$site = [
    'brand'   => 'StrideX',
    'tagline' => 'MOVE IN STYLE. LIVE WITHOUT LIMITS.',
    'year'    => date('Y'),
];

// ---------- Navigation ----------
$navLinks = [
    ['label' => 'Home',        'href' => '/stride/index.php'],
    ['label' => 'Shop',        'href' => '#shop'],
    ['label' => 'Men',         'href' => '/stride/nav/men.php'],
    ['label' => 'Women',       'href' => '/stride/nav/women.php'],
    ['label' => 'Collections', 'href' => '#collections'],
    ['label' => 'About Us',    'href' => '#about'],
];

// ---------- Featured products ----------
$products = [
    [
        'name'    => 'STRIDEX URBAN',
        'color'   => 'Midnight Red',
        'price'   => '79.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/bestseller.png',
        'bg'      => 'bg-red',
        'badge'   => 'BESTSELLER',
        'swatches'=> ['#c8102e', '#111', '#e8e8e8'],
    ],
    [
        'name'    => 'STRIDEX FLEX',
        'color'   => 'Cloud White',
        'price'   => '79.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/newdrop.png',
        'bg'      => 'bg-black',
        'badge'   => 'NEW DROP',
        'swatches'=> ['#ffffff', '#111', '#e8e8e8'],
    ],
    [
        'name'    => 'STRIDEX CORE',
        'color'   => 'Carbon Gray',
        'price'   => '89.99',
        'rating'  => 4,
        'reviews' => 246,
        'image'   => 'image/limited.png',
        'bg'      => 'bg-gray',
        'badge'   => 'LIMITED',
        'swatches'=> ['#ff5a1f', '#111', '#e8e8e8'],
    ],
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
    'FOLLOW US' => [
        ['label' => 'Instagram', 'href' => '#'],
        ['label' => 'Facebook', 'href' => '#'],
        ['label' => 'TikTok', 'href' => '#'],
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
?>