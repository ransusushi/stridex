<!-- ============ FOOTER ============ -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand-custom">
            <img src="/stride/image/stridex-logo.png" alt="<?= e($site['brand']) ?>" class="footer-logo-img">
            <p class="footer-tag"><?= e($site['tagline']) ?></p>
            <p class="footer-description">Lightweight, engineered footwear for athletes, students, and everyday movers.</p>
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
        <p>&copy; 2028 <?= e(strtoupper($site['brand'])) ?>. ALL RIGHTS RESERVED.</p>
        <p class="legal">
            <a href="#">PRIVACY</a>
            <span>&middot;</span>
            <a href="#">TERMS</a>
            <span>&middot;</span>
            <a href="#">COOKIES</a>
        </p>
    </div>
</footer>