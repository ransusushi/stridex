<?php
require_once '../database/config.php';

$pageTitle = "About Us";

// Load data from file with fallback
$about = [];
if (file_exists('../about_data.php')) {
    $about = include '../about_data.php';
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
    <style>
        .about-page { padding: 80px 0; background: var(--bg); }
        .about-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }
        .about-card { background: #0e0e0e; padding: 30px; border-radius: var(--radius); border: 1px solid var(--line); }
        .about-card h2 { font-family: var(--font-display); font-size: 28px; text-transform: uppercase; margin-bottom: 20px; }
        .about-card p { color: var(--muted); line-height: 1.7; }
        .contact-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); }
        .contact-item .label { color: var(--muted); font-weight: 600; width: 80px; }
        .contact-item .value { color: var(--text); }
        .faq-item { padding: 16px 0; border-bottom: 1px solid var(--line); }
        .faq-item .question { font-weight: 600; color: #fff; font-size: 16px; }
        .faq-item .answer { color: var(--muted); margin-top: 6px; line-height: 1.6; }
        @media (max-width: 768px) {
            .about-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include '../header.php'; ?>

<section class="about-page">
    <div class="container">
        <h1 class="section-title" style="margin-bottom: 40px;">About Us</h1>

        <div class="about-grid">
            <div class="about-card">
                <h2><?= e($about['company_name'] ?? 'StrideX') ?></h2>
                <p><?= nl2br(e($about['description'] ?? '')) ?></p>

                <h2 style="margin-top: 40px;">FAQ</h2>
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
            </div>

            <div class="about-card">
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
    </div>
</section>

<?php include '../footer.php'; ?>
</body>
</html>