/**
 * PageLoader Component
 * Handles page load animation fade out
 */
class PageLoader {
  constructor() {
    this.loader = document.querySelector('.page-loader');
    
    if (this.loader) {
      this.init();
    }
  }

  init() {
    window.addEventListener('load', () => {
      setTimeout(() => {
        this.hidePage();
      }, 600);
    });
  }

  hidePage() {
    if (this.loader) {
      this.loader.classList.add('hidden');
      document.body.classList.remove('loading');
    }
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new PageLoader());
} else {
  new PageLoader();
}
