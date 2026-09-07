<!-- ============ WHY STRIDEX ============ -->
<section class="why" id="why">
    <div class="container">
        <p class="eyebrow center">THE DIFFERENCE</p><h2 class="section-title center">WHY CHOOSE STRIDEX?</h2>
        <div class="feature-grid">
            <?php foreach ($features as $f): ?>
                <div class="feature"><div class="feature-icon"><?= icon($f['icon']) ?></div><h3 class="feature-title"><?= e($f['title']) ?></h3><p class="feature-text"><?= e($f['text']) ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>