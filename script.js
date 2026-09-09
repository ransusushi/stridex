// ============================================================
// StrideX – Global JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function() {

    // ----------------------------------------------------------
    // 1. Delete confirmation for admin actions
    // ----------------------------------------------------------
    const deleteLinks = document.querySelectorAll('.delete-confirm, .btn--danger, [onclick*="confirm"]');
    deleteLinks.forEach(link => {
        // Only if it doesn't already have a confirm handler
        if (!link.hasAttribute('data-confirm-set')) {
            link.setAttribute('data-confirm-set', 'true');
            link.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm-message') || 'Are you sure you want to delete this? This action cannot be undone.';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        }
    });

    // ----------------------------------------------------------
    // 2. Auto-hide success messages after 5 seconds
    // ----------------------------------------------------------
    const successMessages = document.querySelectorAll('.success, .success-message, .alert-success');
    successMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => {
                if (msg.parentNode) msg.style.display = 'none';
            }, 500);
        }, 5000);
    });

    // ----------------------------------------------------------
    // 3. Auto-hide error messages after 8 seconds (optional)
    // ----------------------------------------------------------
    const errorMessages = document.querySelectorAll('.error, .error-message, .alert-danger');
    errorMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => {
                if (msg.parentNode) msg.style.display = 'none';
            }, 500);
        }, 8000);
    });

    // ----------------------------------------------------------
    // 4. Toggle edit profile (if you have the inline function)
    // ----------------------------------------------------------
    // This is a fallback – you already have toggleEdit() in profile.php.
    // We'll keep it as a global function.

    // ----------------------------------------------------------
    // 5. Cart quantity – prevent negative or zero values
    // ----------------------------------------------------------
    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) {
                this.value = 1;
            }
        });
    });

    // ----------------------------------------------------------
    // 6. Responsive navigation toggle (optional – add a hamburger menu later)
    // ----------------------------------------------------------
    // If you add a hamburger button with id="menu-toggle", this will work:
    // const menuToggle = document.getElementById('menu-toggle');
    // const mainNav = document.querySelector('.main-nav');
    // if (menuToggle && mainNav) {
    //     menuToggle.addEventListener('click', function() {
    //         mainNav.classList.toggle('open');
    //     });
    // }

    console.log('✅ StrideX JavaScript loaded!');
});