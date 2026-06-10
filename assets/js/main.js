/**
 * Nextfram — main.js
 * Entry point that loads all modular components
 * Each component handles its own initialization
 */

// ✨ Load all components (they auto-initialize)
// Components are modular and independently manage their functionality

// Navigation & scroll handling
const navbar = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/Navbar.js';
  document.head.appendChild(script);
};

// Scroll reveal animations
const scrollAnimations = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/ScrollAnimations.js';
  document.head.appendChild(script);
};

// Portfolio filtering & lightbox
const portfolio = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/Portfolio.js';
  document.head.appendChild(script);
};

// Form validation & upload handling
const forms = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/Forms.js';
  document.head.appendChild(script);
};

// General interactions (modals, buttons, etc)
const interactions = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/Interactions.js';
  document.head.appendChild(script);
};

// Hero particles animation
const heroParticles = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/HeroParticles.js';
  document.head.appendChild(script);
};

// Page loader fade out
const pageLoader = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/PageLoader.js';
  document.head.appendChild(script);
};

// Booking helper (service pre-selection)
const bookingHelper = () => {
  const script = document.createElement('script');
  script.src = '/nextfram/assets/js/components/BookingHelper.js';
  document.head.appendChild(script);
};

// ── INITIALIZE ALL COMPONENTS ──
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    navbar();
    scrollAnimations();
    portfolio();
    forms();
    interactions();
    heroParticles();
    pageLoader();
    bookingHelper();
  });
} else {
  // DOM already loaded
  navbar();
  scrollAnimations();
  portfolio();
  forms();
  interactions();
  heroParticles();
  pageLoader();
  bookingHelper();
}
