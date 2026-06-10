/**
 * ScrollAnimations Component
 * Handles scroll-based reveal animations using Intersection Observer
 */
class ScrollAnimations {
  constructor() {
    this.revealElements = document.querySelectorAll('.reveal');
    this.counterElements = document.querySelectorAll('.stat-number[data-target]');
    
    if (this.revealElements.length || this.counterElements.length) {
      this.init();
    }
  }

  init() {
    this.setupRevealObserver();
    this.setupCounterObserver();
  }

  setupRevealObserver() {
    const options = {
      threshold: 0.1,
      rootMargin: '0px 0px -60px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, options);

    this.revealElements.forEach(el => observer.observe(el));
  }

  setupCounterObserver() {
    const options = {
      threshold: 0.5
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, options);

    this.counterElements.forEach(el => observer.observe(el));
  }

  animateCounter(element) {
    const target = parseInt(element.dataset.target);
    const suffix = element.dataset.suffix || '';
    let current = 0;
    const step = target / 60;
    
    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current) + suffix;
    }, 20);
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new ScrollAnimations());
} else {
  new ScrollAnimations();
}
