document.addEventListener('DOMContentLoaded', function() {
    const successMessage = window.sessionData.successMessage;
    const errorMessage = window.sessionData.errorMessage;

    if (successMessage) {
        showToast(successMessage, 'success');
    }

    if (errorMessage) {
        showToast(errorMessage, 'danger');
    }

    const form = document.getElementById('createAccountForm');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    
    // real-time validation for passwords
    confirmPasswordField.addEventListener('input', function() {
        if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.setCustomValidity('Passwords do not match');
            showError('password_confirmation', 'Passwords do not match');
        } else {
            confirmPasswordField.setCustomValidity('');
            clearError('password_confirmation');
        }
    });

    passwordField.addEventListener('input', function() {
        const password = passwordField.value;
        
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*\.\-_\+])[A-Za-z\d!@#$%^&*\.\-_\+]{8,}$/;
        
        if (!password.match(passwordPattern)) {
            passwordField.setCustomValidity('Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character (e.g., !@#$%^&*.-_+).');
            showError('password', 'Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character (e.g., !@#$%^&*.-_+).');
        } else {
            passwordField.setCustomValidity('');
            clearError('password');
        }
    });

    function showError(field, message) {
        const errorElement = document.getElementById(`${field}_error`);
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    function clearError(field) {
        const errorElement = document.getElementById(`${field}_error`);
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }

    function showToast(message, type = 'success') {
        const toastElement = document.createElement('div');
        toastElement.classList.add('toast', 'align-items-center', 'text-white', 'border-0');
        toastElement.classList.add(`bg-${type}`);
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');

        toastElement.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        const toastContainer = document.getElementById('toastContainer');
        toastContainer.appendChild(toastElement);

        const toast = new bootstrap.Toast(toastElement, {
            delay: 6000,
            animation: true
        });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
});
