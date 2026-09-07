<section class="cta" id="about">
    <div class="container cta-inner">
        <div class="cta-copy">
            <span class="eyebrow">MOVE IN STYLE.</span>
            <h2 class="cta-title">LIVE WITHOUT<br><span class="cta-highlight">LIMITS.</span></h2>
            <p class="cta-lead">StrideX is here to keep you moving with confidence,<br>comfort, and unstoppable energy. Join over 12,000 athletes pushing past boundaries worldwide.</p>

            <div class="cta-form-row">
                <form class="cta-form" id="ctaForm" action="#" method="post" novalidate>
                    <div class="form-group">
                        <input type="email" id="cta-email" placeholder="Enter your email address..." required>
                        <span class="error-message" id="cta-error"></span>
                    </div>
                    <button type="submit" class="btn btn--primary">JOIN THE MOVEMENT <?= icon('arrow') ?></button>
                </form>
            </div>

            <div class="cta-features">
                <span class="cta-feature"><span class="check-icon"><?= icon('check') ?></span> 15% Off First Order</span>
                <span class="cta-feature"><span class="check-icon"><?= icon('check') ?></span> Early Access to Mockup Drops</span>
            </div>
        </div>
        <div class="cta-visual" aria-hidden="true">
            <img src="image/stridex-cta-shoes.png" alt="StrideX sneaker">
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ctaForm');
        const emailInput = document.getElementById('cta-email');
        const errorSpan = document.getElementById('cta-error');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            errorSpan.textContent = '';
            errorSpan.style.color = '';
            emailInput.style.borderColor = '';

            const email = emailInput.value.trim();
            if (!email) {
                errorSpan.textContent = 'Please enter your email address.';
                emailInput.style.borderColor = '#ff5a1f';
                return;
            }
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errorSpan.textContent = 'Please enter a valid email address (e.g., name@domain.com).';
                emailInput.style.borderColor = '#ff5a1f';
                return;
            }
            errorSpan.textContent = "You're in! Check your inbox for your 15% off code.";
            errorSpan.style.color = '#4ade80';
            emailInput.value = '';
        });

        emailInput.addEventListener('input', function() {
            errorSpan.textContent = '';
            errorSpan.style.color = '';
            emailInput.style.borderColor = '';
        });
    });
</script>