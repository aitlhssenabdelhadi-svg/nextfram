/**
 * Interactions Component
 * Handles micro-interactions like button effects, modals, and delete confirmations
 */
class Interactions {
  constructor() {
    this.init();
  }

  init() {
    this.setupDeleteConfirmations();
    this.setupModals();
    this.setupButtonInteractions();
    this.setupTestimonialsCarousel();
  }

  setupDeleteConfirmations() {
    document.querySelectorAll('.confirm-delete').forEach(btn => {
      btn.addEventListener('click', (e) => {
        if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
          e.preventDefault();
        }
      });
    });
  }

  setupModals() {
    // Open modals
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modal = document.getElementById(btn.dataset.modalOpen);
        if (modal) {
          modal.classList.add('open');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    // Close modals
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modal = btn.closest('.modal-overlay');
        if (modal) {
          modal.classList.remove('open');
          document.body.style.overflow = '';
        }
      });
    });

    // Click outside to close
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          overlay.classList.remove('open');
          document.body.style.overflow = '';
        }
      });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal-overlay.open');
        openModals.forEach(modal => {
          modal.classList.remove('open');
          document.body.style.overflow = '';
        });
      }
    });
  }

  setupButtonInteractions() {
    // Add smooth button press effect
    document.querySelectorAll('button, .btn').forEach(btn => {
      btn.addEventListener('mousedown', (e) => {
        if (e.button === 0) { // Left click only
          btn.style.transform = 'scale(0.98)';
        }
      });

      btn.addEventListener('mouseup', () => {
        btn.style.transform = '';
      });

      btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
      });
    });
  }

  setupTestimonialsCarousel() {
    const track = document.getElementById('testimonialTrack');
    if (!track) return;

    const cards = Array.from(document.querySelectorAll('.testimonial-card'));
    const dots = Array.from(document.querySelectorAll('.testimonial-dot'));
    const prevBtn = document.querySelector('.testimonial-prev');
    const nextBtn = document.querySelector('.testimonial-next');

    if (!cards.length || cards.length < 2) return;

    let current = 0;
    let intervalId;

    const updateCarousel = (index) => {
      current = (index + cards.length) % cards.length;
      track.style.transform = `translateX(-${current * 100}%)`;
      dots.forEach((dot, i) => dot.classList.toggle('active', i === current));
    };

    const startAutoSlide = () => {
      clearInterval(intervalId);
      intervalId = setInterval(() => updateCarousel(current + 1), 2600);
    };

    prevBtn?.addEventListener('click', () => {
      updateCarousel(current - 1);
      startAutoSlide();
    });

    nextBtn?.addEventListener('click', () => {
      updateCarousel(current + 1);
      startAutoSlide();
    });

    dots.forEach(dot => {
      dot.addEventListener('click', () => {
        updateCarousel(Number(dot.dataset.slide));
        startAutoSlide();
      });
    });

    updateCarousel(0);
    startAutoSlide();
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new Interactions());
} else {
  new Interactions();
}
