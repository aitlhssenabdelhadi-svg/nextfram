/**
 * Navbar Component
 * Handles sticky navbar behavior, mobile menu toggle, and active link tracking
 */
class Navbar {
  constructor() {
    this.navbar = document.getElementById('navbar');
    this.hamburger = document.getElementById('hamburger');
    this.navLinks = document.getElementById('navLinks');
    this.scrollThreshold = 50;
    
    if (this.navbar) {
      this.init();
    }
  }

  init() {
    this.setupScrollListener();
    this.setupMobileMenu();
    this.setupAnchorLinks();
  }

  setupScrollListener() {
    window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
    this.handleScroll(); // Initial check
  }

  handleScroll() {
    const isScrolled = window.scrollY > this.scrollThreshold;
    this.navbar.classList.toggle('scrolled', isScrolled);
  }

  setupMobileMenu() {
    if (!this.hamburger || !this.navLinks) return;

    this.hamburger.addEventListener('click', () => this.toggleMobileMenu());
    
    // Close menu on nav link click
    this.navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => this.closeMobileMenu());
    });
  }

  toggleMobileMenu() {
    const isOpen = this.hamburger.classList.toggle('open');
    this.navLinks.classList.toggle('open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  closeMobileMenu() {
    this.hamburger.classList.remove('open');
    this.navLinks.classList.remove('open');
    document.body.style.overflow = '';
  }

  setupAnchorLinks() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
      link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href !== '#') {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
          }
        }
      });
    });
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new Navbar());
} else {
  new Navbar();
}
