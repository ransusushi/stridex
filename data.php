<?php
// ---------- Featured products (homepage) ----------
$products = [
    [
        'id'       => 'f1',
        'name'     => 'STRIDEX URBAN',
        'color'    => 'Midnight Red',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'image/bestseller.png',
        'bg'       => 'bg-red',
        'badge'    => 'BESTSELLER',
        'swatches' => ['#c8102e', '#111', '#e8e8e8'],
        'quantity' => 10,   // <-- added
    ],
    [
        'id'       => 'f2',
        'name'     => 'STRIDEX FLEX',
        'color'    => 'Cloud White',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'image/newdrop.png',
        'bg'       => 'bg-black',
        'badge'    => 'NEW DROP',
        'swatches' => ['#ffffff', '#111', '#e8e8e8'],
        'quantity' => 10,   // <-- added
    ],
    [
        'id'       => 'f3',
        'name'     => 'STRIDEX CORE',
        'color'    => 'Carbon Gray',
        'price'    => '89.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'image/limited.png',
        'bg'       => 'bg-gray',
        'badge'    => 'LIMITED',
        'swatches' => ['#ff5a1f', '#111', '#e8e8e8'],
        'quantity' => 10,   // <-- added
    ],
];

// ---------- Men's Products ----------
$menProducts = [
    [
        'id'       => 'm1',
        'name'     => 'STRIDEX URBAN',
        'color'    => 'Midnight Red',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'men/bestseller.png',
        'bg'       => 'bg-red',
        'badge'    => 'BESTSELLER',
        'swatches' => ['#c8102e', '#111', '#e8e8e8'],
        'quantity' => 10,
    ],
    [
        'id'       => 'm2',
        'name'     => 'STRIDEX SHIFT',
        'color'    => 'Cloud White',
        'price'    => '79.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'men/newdrop.png',
        'bg'       => 'bg-black',
        'badge'    => 'NEW DROP',
        'swatches' => ['#ffffff', '#111', '#e8e8e8'],
        'quantity' => 10,
    ],
    [
        'id'       => 'm3',
        'name'     => 'STRIDEX CORE',
        'color'    => 'Carbon Gray',
        'price'    => '89.99',
        'rating'   => 4,
        'reviews'  => 246,
        'image'    => 'men/limited.png',
        'bg'       => 'bg-gray',
        'badge'    => 'LIMITED',
        'swatches' => ['#ff5a1f', '#111', '#e8e8e8'],
        'quantity' => 10,
    ],
];

// ---------- Women's Products ----------
$womenProducts = [
    [
        'id'       => 'w1',
        'name'     => 'STRIDEX LUNA',
        'color'    => 'Rose Gold',
        'price'    => '99.99',
        'rating'   => 5,
        'reviews'  => 189,
        'image'    => 'women/newdrop.png',
        'bg'       => 'bg-red',
        'badge'    => 'NEW DROP',
        'swatches' => ['#ff5a1f', '#fff', '#111'],
        'quantity' => 10,
    ],
    [
        'id'       => 'w2',
        'name'     => 'STRIDEX VIVA',
        'color'    => 'Pearl White',
        'price'    => '84.99',
        'rating'   => 4,
        'reviews'  => 160,
        'image'    => 'women/bestseller.png',
        'bg'       => 'bg-black',
        'badge'    => 'BESTSELLER',
        'swatches' => ['#fff', '#111', '#e8e8e8'],
        'quantity' => 10,
    ],
    [
        'id'       => 'w3',
        'name'     => 'STRIDEX NOVA',
        'color'    => 'Monochrome',
        'price'    => '89.99',
        'rating'   => 5,
        'reviews'  => 210,
        'image'    => 'women/limited.png',
        'bg'       => 'bg-gray',
        'badge'    => 'LIMITED',
        'swatches' => ['#111', '#fff', '#e8e8e8'],
        'quantity' => 10,
    ],
];

// ---------- Merge all products for cart ----------
$allProducts = array_merge($products, $menProducts, $womenProducts);

// ---------- Helper: get product by ID ----------
function getProductById($id) {
    global $allProducts;
    foreach ($allProducts as $p) {
        if ($p['id'] == $id) {
            return $p;
        }
    }
    return null;
}