// ===== Universal Dark Mode Script =====
// This script automatically applies dark mode across all modules

(function() {
  // Apply dark mode immediately on page load (prevents flash)
  if (localStorage.getItem('darkMode') === 'enabled') {
    document.documentElement.classList.add('dark-mode');
    document.body.classList.add('dark-mode');
  }

  // Wait for DOM to be fully loaded
  document.addEventListener('DOMContentLoaded', function() {
    
    // Check if toggle switch exists on this page
    const toggleSwitch = document.getElementById('toggleDarkMode');
    
    if (toggleSwitch) {
      const toggleSlider = toggleSwitch.querySelector('.toggle-slider i');
      
      // Apply saved dark mode preference
      if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        toggleSwitch.classList.add('active');
        if (toggleSlider) {
          toggleSlider.className = 'fa-solid fa-moon';
        }
      }
      
      // Toggle dark mode on click
      toggleSwitch.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        toggleSwitch.classList.toggle('active');
        
        // Change icon and save preference
        if (document.body.classList.contains('dark-mode')) {
          if (toggleSlider) {
            toggleSlider.className = 'fa-solid fa-moon';
          }
          localStorage.setItem('darkMode', 'enabled');
        } else {
          if (toggleSlider) {
            toggleSlider.className = 'fa-solid fa-sun';
          }
          localStorage.setItem('darkMode', 'disabled');
        }
      });
    } else {
      // If no toggle switch on this page, just apply the saved preference
      if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
      }
    }
    
  });
})();