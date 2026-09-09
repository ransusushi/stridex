<?php
require_once '../database/config.php';

$pageTitle = "About Us";

// Load data from file with fallback
$about = [];
if (file_exists('../includes/about_data.php')) {
    $about = include '../includes/about_data.php';
} else {
    // Default fallback data
    $about = [
        'company_name' => 'StrideX',
        'email' => 'hello@stridex.com',
        'phone' => '+1 (555) 123-4567',
        'address' => '123 Main Street, New York, NY 10001',
        'description' => 'Welcome to StrideX. We are committed to quality and performance.',
        'faq' => [
            ['question' => 'What materials are used?', 'answer' => 'High-quality synthetic and mesh materials.'],
            ['question' => 'How do I find my size?', 'answer' => 'Check our size guide on the product page.'],
        ]
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> — <?= e($site['brand']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stridex.css">
    <link rel="stylesheet" href="../nav/about.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="about-page">
    <div class="container">
        <h1 class="section-title" style="margin-bottom: 40px;">About Us</h1>

        <!-- ... header, etc. -->
<div class="about-grid">
    <div class="about-card">
        <h2><?= e($about['company_name'] ?? 'StrideX') ?></h2>
        <p><?= nl2br(e($about['description'] ?? '')) ?></p>

        <!-- FAQ section with id -->
        <h2 id="faq" style="margin-top: 40px;">FAQ</h2>
        <?php if (empty($about['faq'])): ?>
            <p style="color: var(--muted);">No FAQ yet.</p>
        <?php else: ?>
            <?php foreach ($about['faq'] as $faq): ?>
                <div class="faq-item">
                    <div class="question"><?= e($faq['question']) ?></div>
                    <div class="answer"><?= nl2br(e($faq['answer'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Shipping & Returns placeholder (optional) -->
        <h2 id="shipping" style="margin-top: 40px;">Shipping & Returns</h2>
        <p style="color: var(--muted);">We offer free shipping on orders over $50. Returns are accepted within 30 days of purchase.</p>
    </div>

    <!-- Contact card with id -->
    <div class="about-card" id="contact">
        <h2>Contact</h2>
        <div class="contact-item">
            <span class="label">Email</span>
            <span class="value"><?= e($about['email'] ?? 'N/A') ?></span>
        </div>
        <div class="contact-item">
            <span class="label">Phone</span>
            <span class="value"><?= e($about['phone'] ?? 'N/A') ?></span>
        </div>
        <div class="contact-item" style="border-bottom: none;">
            <span class="label">Address</span>
            <span class="value"><?= e($about['address'] ?? 'N/A') ?></span>
        </div>
    </div>
</div>
<!-- ... footer, etc. -->
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>