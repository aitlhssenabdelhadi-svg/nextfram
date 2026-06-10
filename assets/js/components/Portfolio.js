/**
 * Portfolio Component
 * Handles portfolio filtering, lightbox, and interactions
 */
class Portfolio {
  constructor() {
    this.filterBtns = document.querySelectorAll('.filter-btn');
    this.portfolioItems = document.querySelectorAll('.portfolio-item');
    this.lightbox = document.getElementById('lightbox');
    this.lightboxContent = document.getElementById('lightboxContent');
    this.lightboxClose = document.getElementById('lightboxClose');
    
    if (this.portfolioItems.length) {
      this.init();
    }
  }

  init() {
    this.setupFilter();
    this.setupLightbox();
    this.setupItemHovers();
  }

  setupFilter() {
    this.filterBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleFilter(btn);
      });
    });
  }

  handleFilter(btn) {
    // Update active button
    this.filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;
    const itemsToShow = [];
    const itemsToHide = [];

    // Determine which items to show/hide
    this.portfolioItems.forEach(item => {
      const matches = filter === 'all' || item.dataset.category === filter;
      if (matches) {
        itemsToShow.push(item);
      } else {
        itemsToHide.push(item);
      }
    });

    // Animate out items to hide
    itemsToHide.forEach(item => {
      item.style.opacity = '0';
      item.style.transform = 'scale(0.95)';
      setTimeout(() => {
        item.classList.add('hidden');
      }, 150);
    });

    // Animate in items to show with stagger
    setTimeout(() => {
      itemsToShow.forEach((item, index) => {
        item.classList.remove('hidden');
        item.style.opacity = '0';
        item.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
          requestAnimationFrame(() => {
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
          });
        }, index * 50);
      });
    }, 150);
  }

  setupLightbox() {
    if (!this.lightbox) return;

    this.portfolioItems.forEach(item => {
      item.addEventListener('click', () => this.openLightbox(item));
    });

    if (this.lightboxClose) {
      this.lightboxClose.addEventListener('click', () => this.closeLightbox());
    }

    this.lightbox.addEventListener('click', (e) => {
      if (e.target === this.lightbox) this.closeLightbox();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.closeLightbox();
    });
  }

  openLightbox(item) {
    const media = item.dataset.media;
    const type = item.dataset.type;
    const title = item.dataset.title || '';

    this.lightboxContent.innerHTML = '';

    if (type === 'video' && media) {
      const video = document.createElement('video');
      video.src = media;
      video.controls = true;
      video.autoplay = true;
      video.style.maxWidth = '100%';
      this.lightboxContent.appendChild(video);
    } else if (media) {
      const img = document.createElement('img');
      img.src = media;
      img.alt = title;
      img.style.maxWidth = '100%';
      this.lightboxContent.appendChild(img);
    }

    this.lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  closeLightbox() {
    if (this.lightbox) {
      this.lightbox.classList.remove('open');
      this.lightboxContent.innerHTML = '';
      document.body.style.overflow = '';
    }
  }

  setupItemHovers() {
    this.portfolioItems.forEach(item => {
      item.addEventListener('mouseenter', () => {
        item.style.transform = 'scale(1.02)';
      });
      item.addEventListener('mouseleave', () => {
        item.style.transform = 'scale(1)';
      });
    });
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new Portfolio());
} else {
  new Portfolio();
}
