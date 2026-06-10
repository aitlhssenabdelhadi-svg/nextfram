/**
 * HeroParticles Component
 * Generates and animates hero section particles for visual interest
 */
class HeroParticles {
  constructor() {
    this.particlesContainer = document.querySelector('.hero-particles');
    this.particleCount = 20;
    
    if (this.particlesContainer) {
      this.init();
    }
  }

  init() {
    this.generateParticles();
  }

  generateParticles() {
    for (let i = 0; i < this.particleCount; i++) {
      const particle = document.createElement('div');
      particle.className = 'particle';
      
      const size = Math.random() * 4 + 1;
      const duration = Math.random() * 15 + 10;
      const delay = Math.random() * 10;
      const left = Math.random() * 100;

      particle.style.cssText = `
        width: ${size}px;
        height: ${size}px;
        left: ${left}%;
        animation-duration: ${duration}s;
        animation-delay: ${delay}s;
        opacity: 0;
      `;

      this.particlesContainer.appendChild(particle);
    }
  }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new HeroParticles());
} else {
  new HeroParticles();
}
