import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Price/Currency Input Automatic Formatting Logic (Indonesian format: . for thousands, , for decimal)
function formatPriceInput(input) {
    let value = input.value;
    let selectionStart = input.selectionStart;
    let originalLength = value.length;
    
    // Remove all characters except digits and comma/dot
    let cleanValue = value.replace(/[^0-9.,]/g, '');
    
    let hasComma = cleanValue.includes(',');
    let hasDot = cleanValue.includes('.');
    
    let decimalPart = '';
    let integerPart = cleanValue;
    
    if (hasComma) {
        let parts = cleanValue.split(',');
        // The last part is the decimal part
        decimalPart = parts.pop();
        integerPart = parts.join(''); // Join the rest as integer
    } else if (hasDot) {
        // Check if the last dot is near the end (1 or 2 digits)
        let lastDotIndex = cleanValue.lastIndexOf('.');
        let digitsAfterDot = cleanValue.length - 1 - lastDotIndex;
        if (digitsAfterDot === 1 || digitsAfterDot === 2) {
            decimalPart = cleanValue.substring(lastDotIndex + 1);
            integerPart = cleanValue.substring(0, lastDotIndex);
        } else {
            // Just thousands separator, remove all dots
            integerPart = cleanValue;
        }
    }
    
    // Remove any non-digits from integer part (including remaining dots/commas)
    integerPart = integerPart.replace(/[^0-9]/g, '');
    // Limit decimal part to 2 digits and digits only
    decimalPart = decimalPart.replace(/[^0-9]/g, '').substring(0, 2);
    
    // Format integer part with dots
    let formattedInteger = '';
    if (integerPart.length > 0) {
        if (integerPart.length > 1 && integerPart.startsWith('0')) {
            integerPart = integerPart.replace(/^0+/, '');
            if (integerPart === '') integerPart = '0';
        }
        
        let rev = integerPart.split('').reverse().join('');
        let formattedRev = '';
        for (let i = 0; i < rev.length; i++) {
            if (i > 0 && i % 3 === 0) {
                formattedRev += '.';
            }
            formattedRev += rev[i];
        }
        formattedInteger = formattedRev.split('').reverse().join('');
    }
    
    // Reconstruct new value
    let newValue = formattedInteger;
    if (cleanValue.endsWith(',') || (hasDot && !hasComma && cleanValue.endsWith('.'))) {
        newValue += ',';
    } else if (decimalPart.length > 0) {
        newValue += ',' + decimalPart;
    }
    
    if (input.value !== newValue) {
        input.value = newValue;
        let lengthDifference = newValue.length - originalLength;
        let newCursorPosition = selectionStart + lengthDifference;
        newCursorPosition = Math.max(0, Math.min(newCursorPosition, newValue.length));
        input.setSelectionRange(newCursorPosition, newCursorPosition);
    }
}

// Display or hide a price validation warning message and toggle red border
function showPriceInputWarning(input, message) {
    let warningId = 'warning-' + input.id;
    let warningEl = document.getElementById(warningId);
    
    let borderContainer = input;
    if (input.classList.contains('border-none')) {
        borderContainer = input.closest('.flex') || input;
    }
    
    if (message) {
        borderContainer.classList.add('border-red-500');
        borderContainer.classList.remove('border-gray-200', 'dark:border-gray-700', 'border-gray-300', 'dark:border-gray-600', 'focus-within:border-scg-red', 'focus:border-scg-red');
        
        if (!warningEl) {
            warningEl = document.createElement('p');
            warningEl.id = warningId;
            warningEl.className = 'text-red-500 text-xs mt-1 price-warning-msg';
            warningEl.innerHTML = '❌ ' + message;
            
            let parent = input.closest('.space-y-2') || input.parentElement;
            parent.appendChild(warningEl);
        } else {
            warningEl.innerHTML = '❌ ' + message;
        }
    } else {
        borderContainer.classList.remove('border-red-500');
        if (input.classList.contains('border-none')) {
            borderContainer.classList.add('border-gray-200', 'dark:border-gray-700');
        } else {
            borderContainer.classList.add('border-gray-300', 'dark:border-gray-600');
        }
        if (warningEl) {
            warningEl.remove();
        }
    }
}

// Check and warn if value exceeds maximum limit without modifying the value
function checkMaxLimit(input) {
    let value = input.value;
    let maxAttr = input.getAttribute('data-max');
    if (maxAttr) {
        let maxVal = parseFloat(maxAttr);
        if (!isNaN(maxVal)) {
            let numericValStr = value.replace(/\./g, '').replace(/,/g, '.');
            let currentVal = parseFloat(numericValStr);
            if (!isNaN(currentVal) && currentVal > maxVal) {
                let maxStrParts = maxVal.toString().split('.');
                let integerPartMax = maxStrParts[0];
                let decimalPartMax = maxStrParts[1] || '';
                
                let revMax = integerPartMax.split('').reverse().join('');
                let formattedRevMax = '';
                for (let i = 0; i < revMax.length; i++) {
                    if (i > 0 && i % 3 === 0) {
                        formattedRevMax += '.';
                    }
                    formattedRevMax += revMax[i];
                }
                let formattedIntegerMax = formattedRevMax.split('').reverse().join('');
                
                let formattedMax = formattedIntegerMax;
                if (decimalPartMax.length > 0) {
                    formattedMax += ',' + decimalPartMax.substring(0, 2);
                }
                
                showPriceInputWarning(input, `Harga tidak boleh melebihi batas maksimal Rp ${formattedMax}!`);
                return false;
            }
        }
    }
    showPriceInputWarning(input, null);
    return true;
}

// Global Event Delegation for Price Inputs
document.addEventListener('input', function (e) {
    if (e.target && e.target.classList.contains('price-input')) {
        formatPriceInput(e.target);
        checkMaxLimit(e.target);
    }
});

// Global Event Delegation for Max Limit Warning on change
document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('price-input')) {
        checkMaxLimit(e.target);
    }
});

// Prevent form submission if there are active price warning messages
document.addEventListener('submit', function (e) {
    let form = e.target;
    let warningMessages = form.querySelectorAll('.price-warning-msg');
    if (warningMessages.length > 0) {
        e.preventDefault();
        alert('Mohon perbaiki kesalahan input harga sebelum mengirim form!');
    }
});

// Dark Mode Toggle & Initial Price Formatting
document.addEventListener('DOMContentLoaded', function () {
    // Format and check initial price inputs
    document.querySelectorAll('.price-input').forEach(function (input) {
        formatPriceInput(input);
        checkMaxLimit(input);
    });

    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var themeToggleBtn = document.getElementById('theme-toggle');

    if (themeToggleDarkIcon && themeToggleLightIcon && themeToggleBtn) {
        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function () {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('theme')) {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        });
    }
});
