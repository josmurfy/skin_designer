// Original: warehouse/marketplace/ebay/category_form.js
// category_ebay_form.js

// ============================================
// FUNCTIONS DUPLICATED FROM TOOLS.JS (PRODUCTION SAFETY)
// ============================================

function htmlspecialchars(str) {
    if (str === null || str === undefined) {
        return '';
    }
    str = String(str);
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

function htmlspecialchars_decode(str) {
    if (str === null || str === undefined) {
        return '';
    }
    str = String(str);
    return str.replace(/&amp;/g, '&')
              .replace(/&lt;/g, '<')
              .replace(/&gt;/g, '>')
              .replace(/&quot;/g, '"')
              .replace(/&#039;/g, "'");
}

function ucwords(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
}

// ============================================
// CATEGORY EBAY FORM LOGIC
// ============================================

$(document).ready(function() {
    // Initialize Summernote on description textareas
    if ($.fn.summernote) {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    }
});
