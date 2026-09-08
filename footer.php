<?php
/* ---- Handle the footer signup (PHP only, no JavaScript) ---- */
$subEmail = '';
$subMsg   = '';
$subState = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'footer') {
    $subEmail = trim($_POST['email'] ?? '');

    if ($subEmail === '') {
        $subMsg   = 'Please enter your email address.';
        $subState = 'is-error';
    } elseif (!filter_var($subEmail, FILTER_VALIDATE_EMAIL)) {
        $subMsg   = 'Please enter a valid email address.';
        $subState = 'is-error';
    } else {
        // TODO: save $subEmail to your database or mailing list here.
        $subMsg   = "You're subscribed. Watch your inbox for the next drop.";
        $subState = 'is-success';
        $subEmail = '';
    }
}
?>
<!-- ============ FOOTER ============ -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand-custom">
            <img src="/stride/image/stridex-logo.png" alt="<?= e($site['brand']) ?>" class="footer-logo-img">
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
        
        </div>
    </div>
    <div class="container footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= e(strtoupper($site['brand'])) ?>. ALL RIGHTS RESERVED.</p>
        <p class="legal">
            <a href="#">PRIVACY</a>
            <span>&middot;</span>
            <a href="#">TERMS</a>
            <span>&middot;</span>
            <a href="#">COOKIES</a>
        </p>
    </div>
</footer>