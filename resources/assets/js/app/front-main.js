/**
 * Main - Front Pages
 */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const navbar = document.querySelector('.landing-navbar');

  const handleNavbar = () => {
    if (window.scrollY > 30) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  handleNavbar();

  window.addEventListener('scroll', handleNavbar);
});
