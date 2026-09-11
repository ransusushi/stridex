<?php
require_once '../database/config.php';

$pageTitle = "About Us";

// ---------- Handle Contact Form Submission ----------
$contactSuccess = false;
$contactError = '';

// 1. Initialize variables OUTSIDE the if block to prevent warnings on page load
$name    = '';
$email   = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // 2. NOW assign the form data to the variables
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 3. Validate
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $contactError = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = 'Please enter a valid email address.';
    } else {
        // 4. Save to database ONLY if validation passes
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);

            $contactSuccess = true;
        } catch (PDOException $e) {
            // Log the real error for yourself
            if (defined('ERROR_LOG_FILE')) {
                file_put_contents(ERROR_LOG_FILE, "[" . date('Y-m-d H:i:s') . "] CONTACT FORM DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            }
            // Show a friendly error to the user
            $contactError = "We're experiencing technical difficulties. Please try again later.";
        }
    }
}

// ---------- Load Data ----------
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

        <div class="about-grid">
            <!-- Card 1: Description, FAQ, Shipping -->
            <div class="about-card">
                <h2><?= e($about['company_name'] ?? 'StrideX') ?></h2>
                <p><?= nl2br(e($about['description'] ?? '')) ?></p>

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

                <h2 id="shipping" style="margin-top: 40px;">Shipping & Returns</h2>
                <p style="color: var(--muted);">We offer free shipping on orders over $50. Returns are accepted within 30 days of purchase.</p>
            </div>

            <!-- Card 2: Contact Info (changed id to contact-info) -->
            <div class="about-card" id="contact-info">
                <h2>Contact Info</h2>
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

        <!-- ============================================== -->
        <!-- NEW: Contact Form Section (id="contact")       -->
        <!-- ============================================== -->
        <div class="contact-section" id="contact">
            <div class="contact-header">
                <span class="contact-subtitle">GET IN TOUCH</span>
                <h2 class="contact-main-title">WE'D LOVE TO HEAR FROM YOU</h2>
            </div>

            <div class="contact-card">
                <h2 class="contact-title">Send a Message</h2>
                <p class="contact-subtitle">We'll get back to you as soon as possible.</p>

                <?php if ($contactSuccess): ?>
                    <div class="contact-alert contact-alert--success">
                        ✅ Your message has been sent! We'll get back to you soon.
                    </div>
                <?php elseif (!empty($contactError)): ?>
                    <div class="contact-alert contact-alert--error">
                        ❌ <?= htmlspecialchars($contactError) ?>
                    </div>
                <?php endif; ?>

                <form class="contact-form" method="POST" action="">
                    <div class="form-field">
                        <label for="contact-name">Your Name <span class="required">*</span></label>
                        <input type="text" id="contact-name" name="name"
                               placeholder="Customer One"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="contact-email">Email Address <span class="required">*</span></label>
                        <input type="email" id="contact-email" name="email"
                               placeholder="customer1@gmail.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="contact-subject">Subject <span class="required">*</span></label>
                        <input type="text" id="contact-subject" name="subject"
                               placeholder="Order inquiry, feedback, etc."
                               value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                    </div>

                    <div class="form-field">
                        <label for="contact-message">Message <span class="required">*</span></label>
                        <textarea id="contact-message" name="message" rows="5"
                                  placeholder="Type your message here..."
                                  required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" name="contact_submit" class="btn btn--primary contact-btn">
                        Send Message
                    </button>
                </form>
            </div>
        </div>

    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>