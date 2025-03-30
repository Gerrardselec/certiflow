/**
 * Main JavaScript file
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/public/assets/js/main.js
 * Contains custom JavaScript functionality for the application
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('CertiFlow application initialized');
    
    // Initialize any flash messages
    initFlashMessages();
    
    // Initialize form validation
    initFormValidation();
});

/**
 * Initialize flash messages with auto-dismiss
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert-dismissible');
    
    flashMessages.forEach(function(alert) {
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            } else {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            }
        }, 5000);
    });
}

/**
 * Initialize custom form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Toggle password visibility
 * 
 * @param {string} inputId The ID of the password input
 * @param {string} toggleId The ID of the toggle button
 */
function togglePasswordVisibility(inputId, toggleId) {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = document.getElementById(toggleId);
    
    if (passwordInput && toggleButton) {
        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Update toggle button text
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    }
}

/**
 * Format a date input to a specific format
 * 
 * @param {HTMLInputElement} input The date input element
 * @param {string} format The desired format (e.g., 'dd/mm/yyyy')
 */
function formatDateInput(input, format) {
    input.addEventListener('blur', function() {
        const value = this.value;
        if (!value) return;
        
        const date = new Date(value);
        if (isNaN(date.getTime())) return;
        
        let formattedDate = format;
        formattedDate = formattedDate.replace('dd', String(date.getDate()).padStart(2, '0'));
        formattedDate = formattedDate.replace('mm', String(date.getMonth() + 1).padStart(2, '0'));
        formattedDate = formattedDate.replace('yyyy', date.getFullYear());
        
        this.value = formattedDate;
    });
}