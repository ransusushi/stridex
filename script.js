// ============================================================
// StrideX – Global JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function() {


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


    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) {
                this.value = 1;
            }
        });
    });


    console.log('✅ StrideX JavaScript loaded!');
});