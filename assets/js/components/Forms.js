/**
 * Forms Component
 * Handles form validation, error display, and submission feedback
 */
class Forms {
  constructor() {
    this.forms = document.querySelectorAll('.validate-form');
    
    if (this.forms.length) {
      this.init();
    }
  }

  init() {
    this.forms.forEach(form => {
      this.setupFormValidation(form);
      this.setupUploadZones(form);
    });
  }

  setupFormValidation(form) {
    const fields = form.querySelectorAll('input, select, textarea');

    fields.forEach(field => {
      field.addEventListener('blur', () => this.validateField(field));
      field.addEventListener('input', () => {
        if (field.closest('.form-group').classList.contains('error')) {
          this.validateField(field);
        }
      });
      
      // Add focus animations
      field.addEventListener('focus', (e) => {
        e.target.closest('.form-group').classList.add('focused');
      });
      field.addEventListener('blur', (e) => {
        e.target.closest('.form-group').classList.remove('focused');
      });
    });

    form.addEventListener('submit', (e) => {
      let valid = true;
      fields.forEach(field => {
        if (!this.validateField(field)) valid = false;
      });

      if (!valid) {
        e.preventDefault();
        const errorField = form.querySelector('.form-group.error input, .form-group.error select, .form-group.error textarea');
        if (errorField) errorField.focus();
      }
    });
  }

  validateField(field) {
    const group = field.closest('.form-group');
    if (!group) return true;

    const error = group.querySelector('.form-error');
    let valid = true;

    // Check if field is required
    if (field.required && !field.value.trim()) {
      group.classList.add('error');
      if (error) error.textContent = field.dataset.errorRequired || 'This field is required.';
      valid = false;
    }
    // Check email format
    else if (field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
      group.classList.add('error');
      if (error) error.textContent = field.dataset.errorEmail || 'Please enter a valid email.';
      valid = false;
    }
    // Check phone format
    else if (field.dataset.type === 'phone' && field.value && !/^[\d\s\+\-\(\)]{8,20}$/.test(field.value)) {
      group.classList.add('error');
      if (error) error.textContent = field.dataset.errorPhone || 'Please enter a valid phone number.';
      valid = false;
    }
    else {
      group.classList.remove('error');
    }

    return valid;
  }

  setupUploadZones(form) {
    const uploadZones = form.querySelectorAll('.upload-zone');

    uploadZones.forEach(zone => {
      const input = zone.querySelector('input[type="file"]') || 
                   document.querySelector(zone.dataset.inputFor ? '#' + zone.dataset.inputFor : null);
      
      if (!input) return;

      zone.addEventListener('click', () => input.click());

      zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('dragover');
      });

      zone.addEventListener('dragleave', () => {
        zone.classList.remove('dragover');
      });

      zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length) {
          const dt = new DataTransfer();
          dt.items.add(files[0]);
          input.files = dt.files;
          input.dispatchEvent(new Event('change'));
        }
      });

      // Handle file preview
      input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        const preview = zone.querySelector('.upload-preview') || zone.nextElementSibling;
        if (!preview) return;

        const reader = new FileReader();
        reader.onload = (e) => {
          if (file.type.startsWith('video/')) {
            preview.innerHTML = `<video src="${e.target.result}" controls style="max-width:200px;max-height:150px;border-radius:8px;"></video>`;
          } else {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width:200px;max-height:150px;object-fit:cover;border-radius:8px;">`;
          }
        };
        reader.readAsDataURL(file);
      });
    });
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new Forms());
} else {
  new Forms();
}
