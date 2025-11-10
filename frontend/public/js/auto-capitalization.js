/**
 * Auto Capitalization Script for JJ Flower Shop
 * Provides Title Case for names and Sentence Case for instructions/messages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto Title Case for names
    function toTitleCase(str) {
        return str.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }
    
    // Auto Sentence Case for instructions and messages
    function toSentenceCase(str) {
        if (!str) return str;
        // Only capitalize the first letter, keep the rest as typed
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    // Apply auto title case to name fields only
    const nameFields = document.querySelectorAll('input[name*="name"]:not([name*="phone"]):not([name*="email"]):not([name*="address"])');
    nameFields.forEach(field => {
        // Real-time title case as user types
        field.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const originalValue = this.value;
            const titleCaseValue = toTitleCase(originalValue);
            
            if (originalValue !== titleCaseValue) {
                this.value = titleCaseValue;
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function() {
            this.value = toTitleCase(this.value);
        });
        
        field.addEventListener('paste', function() {
            setTimeout(() => {
                this.value = toTitleCase(this.value);
            }, 10);
        });
    });
    
    // Apply auto sentence case to instruction fields
    const instructionFields = document.querySelectorAll('input[name*="instructions"]');
    instructionFields.forEach(field => {
        // Real-time sentence case as user types
        field.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const originalValue = this.value;
            const sentenceCaseValue = toSentenceCase(originalValue);
            
            if (originalValue !== sentenceCaseValue) {
                this.value = sentenceCaseValue;
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function() {
            this.value = toSentenceCase(this.value);
        });
        
        field.addEventListener('paste', function() {
            setTimeout(() => {
                this.value = toSentenceCase(this.value);
            }, 10);
        });
    });
    
    // Apply auto sentence case to message fields
    const messageFields = document.querySelectorAll('textarea[name*="message"]');
    messageFields.forEach(field => {
        // Real-time sentence case as user types
        field.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const originalValue = this.value;
            const sentenceCaseValue = toSentenceCase(originalValue);
            
            if (originalValue !== sentenceCaseValue) {
                this.value = sentenceCaseValue;
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function() {
            this.value = toSentenceCase(this.value);
        });
        
        field.addEventListener('paste', function() {
            setTimeout(() => {
                this.value = toSentenceCase(this.value);
            }, 10);
        });
    });
    
    // Apply auto title case to general name fields (for profile forms)
    const generalNameFields = document.querySelectorAll('input[name="name"]:not([name*="phone"]):not([name*="email"]):not([name*="address"])');
    generalNameFields.forEach(field => {
        field.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const originalValue = this.value;
            const titleCaseValue = toTitleCase(originalValue);
            
            if (originalValue !== titleCaseValue) {
                this.value = titleCaseValue;
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
        
        field.addEventListener('blur', function() {
            this.value = toTitleCase(this.value);
        });
        
        field.addEventListener('paste', function() {
            setTimeout(() => {
                this.value = toTitleCase(this.value);
            }, 10);
        });
    });
});
