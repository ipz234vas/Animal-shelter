document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-control.is-invalid').forEach(function (input) {
        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');
            let feedback = input.closest('.mb-3')?.querySelector('.invalid-feedback');
            if (feedback)
                feedback.style = 'display: none !important';
        });
    });
});