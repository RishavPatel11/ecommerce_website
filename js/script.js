// Basic JS for interactivity (e.g., confirmations)
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (confirm('Are you sure?')) {
                return true;
            } else {
                e.preventDefault();
            }
        });
    });
});