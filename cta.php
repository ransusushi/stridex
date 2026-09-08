<?php
/* ---- Handle the CTA signup (PHP only, no JavaScript) ---- */
$ctaEmail = '';
$ctaMsg   = '';
$ctaState = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_id'] ?? '') === 'cta') {
    $ctaEmail = trim($_POST['email'] ?? '');

    if ($ctaEmail === '') {
        $ctaMsg   = 'Please enter your email address.';
        $ctaState = 'is-error';
    } elseif (!filter_var($ctaEmail, FILTER_VALIDATE_EMAIL)) {
        $ctaMsg   = 'Please enter a valid email address (e.g., name@domain.com).';
        $ctaState = 'is-error';
    } else {
        // TODO: save $ctaEmail to your database or mailing list here.
        $ctaMsg   = "You're in! Check your inbox for your 15% off code.";
        $ctaState = 'is-success';
        $ctaEmail = '';   // clear the field on success
    }
}
?>
<section class="cta" id="newsletter" aria-labelledby="cta-title">
    <div class="container cta-inner">
        <div class="cta-copy">
            <span class="eyebrow">MOVE IN STYLE.</span>
            <h2 class="cta-title" id="cta-title">LIVE WITHOUT <span class="cta-highlight">LIMITS.</span></h2>
            <p class="cta-lead">StrideX is here to keep you moving with confidence, comfort,
                and unstoppable energy. Join over 12,000 athletes pushing past boundaries worldwide.</p>

            <div class="cta-form-row">
                <form class="cta-form" action="#newsletter" method="post">
                    <input type="hidden" name="form_id" value="cta">
                    <div class="form-group">
                        <label class="sr-only" for="cta-email">Email address</label>
                        <input type="email" id="cta-email" name="email" autocomplete="email"
                               placeholder="Enter your email address..."
                               value="<?= e($ctaEmail) ?>"
                               aria-describedby="cta-msg"
                               <?= $ctaState === 'is-error' ? 'class="is-invalid" aria-invalid="true"' : '' ?>
                               required>
                        <span class="form-message <?= e($ctaState) ?>" id="cta-msg" role="status">
                            <?= e($ctaMsg) ?>
                        </span>
                    </div>
                    <button type="submit" class="btn btn--primary">
                        JOIN THE MOVEMENT <?= icon('arrow') ?>
                    </button>
                </form>
            </div>

            <ul class="cta-features">
                <li class="cta-feature">
                    <span class="check-icon"><?= icon('check') ?></span> 15% Off First Order
                </li>
                <li class="cta-feature">
                    <span class="check-icon"><?= icon('check') ?></span> Early Access to Mockup Drops
                </li>
            </ul>
        </div>

        <div class="cta-visual">
            <img src="image/stridex-cta-shoes.png" alt="" loading="lazy" decoding="async">
        </div>
    </div>
</section>
