/**
 * BookingHelper Component
 * Handles pre-selection of services from query parameters
 */
class BookingHelper {
  constructor() {
    this.init();
  }

  init() {
    const urlParams = new URLSearchParams(window.location.search);
    const serviceParam = urlParams.get('service');
    
    if (serviceParam) {
      const serviceSelect = document.getElementById('service');
      if (serviceSelect) {
        serviceSelect.value = serviceParam;
        // Trigger change event for any listeners
        serviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new BookingHelper());
} else {
  new BookingHelper();
}
