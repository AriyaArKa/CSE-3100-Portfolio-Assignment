/**
 * Portfolio JavaScript - Main Script
 * Arka Braja Prasad Nath Portfolio
 * Features: Theme Toggle, Animations, Form Handling, Navigation
 */

// DOM Content Loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize all components
  initializeTheme();
  initializeNavigation();
  initializeAnimations();
  initializeTypingEffect();
  initializeScrollEffects();
  initializeStatCounters();
  initializeSkillBars();
  initializeContactForm();
  initializeLoadingScreen();
  initializeParticles();

  console.log("Portfolio loaded successfully!");
});

// Theme Management
function initializeTheme() {
  const themeToggle = document.getElementById("theme-toggle");
  const currentTheme =
    localStorage.getItem("theme") ||
    document.documentElement.getAttribute("data-theme") ||
    "light";

  // Set initial theme
  document.documentElement.setAttribute("data-theme", currentTheme);
  updateThemeCookie(currentTheme);

  // Theme toggle event
  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      const currentTheme = document.documentElement.getAttribute("data-theme");
      const newTheme = currentTheme === "dark" ? "light" : "dark";

      document.documentElement.setAttribute("data-theme", newTheme);
      localStorage.setItem("theme", newTheme);
      updateThemeCookie(newTheme);

      // Add transition effect
      document.body.style.transition =
        "background-color 0.3s ease, color 0.3s ease";
      setTimeout(() => {
        document.body.style.transition = "";
      }, 300);
    });
  }
}

function updateThemeCookie(theme) {
  const expires = new Date();
  expires.setTime(expires.getTime() + 30 * 24 * 60 * 60 * 1000); // 30 days
  document.cookie = `portfolio_theme=${theme}; expires=${expires.toUTCString()}; path=/`;
}

// Navigation Management
function initializeNavigation() {
  const navbar = document.getElementById("navbar");
  const navToggle = document.getElementById("nav-toggle");
  const navMenu = document.getElementById("nav-menu");
  const navLinks = document.querySelectorAll(".nav-link");

  // Navbar scroll effect
  window.addEventListener("scroll", function () {
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });

  // Mobile menu toggle
  if (navToggle && navMenu) {
    navToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");
      navToggle.classList.toggle("active");

      // Animate hamburger lines
      const lines = navToggle.querySelectorAll(".hamburger-line");
      lines.forEach((line, index) => {
        line.style.transform = navMenu.classList.contains("active")
          ? getHamburgerTransform(index)
          : "none";
      });
    });
  }

  // Smooth scroll navigation
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const targetSection = document.querySelector(targetId);

      if (targetSection) {
        const offsetTop = targetSection.offsetTop - 80;
        window.scrollTo({
          top: offsetTop,
          behavior: "smooth",
        });

        // Close mobile menu
        if (navMenu.classList.contains("active")) {
          navMenu.classList.remove("active");
          navToggle.classList.remove("active");
          const lines = navToggle.querySelectorAll(".hamburger-line");
          lines.forEach((line) => {
            line.style.transform = "none";
          });
        }

        // Update active link
        updateActiveNavLink(targetId);
      }
    });
  });

  // Update active navigation on scroll
  window.addEventListener("scroll", debounce(updateNavigationOnScroll, 10));
}

function getHamburgerTransform(index) {
  const transforms = [
    "rotate(45deg) translate(5px, 5px)",
    "opacity: 0",
    "rotate(-45deg) translate(7px, -6px)",
  ];
  return transforms[index] || "none";
}

function updateActiveNavLink(activeId) {
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach((link) => {
    link.classList.remove("active");
    if (link.getAttribute("href") === activeId) {
      link.classList.add("active");
    }
  });
}

function updateNavigationOnScroll() {
  const sections = document.querySelectorAll("section[id]");
  const scrollY = window.pageYOffset;

  sections.forEach((section) => {
    const sectionHeight = section.offsetHeight;
    const sectionTop = section.offsetTop - 100;
    const sectionId = section.getAttribute("id");

    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      updateActiveNavLink(`#${sectionId}`);
    }
  });
}

// Loading Screen
function initializeLoadingScreen() {
  const loadingScreen = document.getElementById("loading-screen");

  window.addEventListener("load", function () {
    setTimeout(() => {
      loadingScreen.style.opacity = "0";
      setTimeout(() => {
        loadingScreen.style.display = "none";
      }, 500);
    }, 1000);
  });
}

// Typing Effect
function initializeTypingEffect() {
  const typingElement = document.querySelector(".typing-text");

  if (!typingElement) return;

  const texts = JSON.parse(
    typingElement.getAttribute("data-text") || '["Developer"]'
  );
  let textIndex = 0;
  let charIndex = 0;
  let isDeleting = false;

  function typeText() {
    const currentText = texts[textIndex];

    if (isDeleting) {
      typingElement.textContent = currentText.substring(0, charIndex - 1);
      charIndex--;
    } else {
      typingElement.textContent = currentText.substring(0, charIndex + 1);
      charIndex++;
    }

    let typeSpeed = isDeleting ? 50 : 100;

    if (!isDeleting && charIndex === currentText.length) {
      typeSpeed = 2000;
      isDeleting = true;
    } else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      textIndex = (textIndex + 1) % texts.length;
      typeSpeed = 500;
    }

    setTimeout(typeText, typeSpeed);
  }

  setTimeout(typeText, 1000);
}

// Animations
function initializeAnimations() {
  // Initialize AOS (Animate On Scroll)
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      easing: "ease-out-cubic",
      once: true,
      offset: 100,
    });
  }
}

// Scroll Effects
function initializeScrollEffects() {
  const backToTopBtn = document.getElementById("backToTop");

  // Back to top button
  if (backToTopBtn) {
    window.addEventListener("scroll", function () {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    });

    backToTopBtn.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }

  // Parallax effect for hero section
  const heroSection = document.querySelector(".hero-section");
  if (heroSection) {
    window.addEventListener("scroll", function () {
      const scrolled = window.pageYOffset;
      const rate = scrolled * -0.5;

      const particles = heroSection.querySelector(".hero-particles");
      if (particles) {
        particles.style.transform = `translateY(${rate}px)`;
      }
    });
  }
}

// Stat Counters
function initializeStatCounters() {
  const statNumbers = document.querySelectorAll(".stat-number[data-count]");

  const observerOptions = {
    threshold: 0.7,
    rootMargin: "0px 0px -100px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const target = entry.target;
        const finalCount = parseInt(target.getAttribute("data-count"));
        animateCounter(target, finalCount);
        observer.unobserve(target);
      }
    });
  }, observerOptions);

  statNumbers.forEach((stat) => observer.observe(stat));
}

function animateCounter(element, target) {
  let current = 0;
  const increment = target / 50;
  const duration = 2000;
  const stepTime = duration / 50;

  const timer = setInterval(function () {
    current += increment;
    element.textContent = Math.floor(current);

    if (current >= target) {
      element.textContent = target;
      clearInterval(timer);
    }
  }, stepTime);
}

// Skill Bars Animation
function initializeSkillBars() {
  const skillBars = document.querySelectorAll(".skill-progress[data-progress]");

  const observerOptions = {
    threshold: 0.7,
    rootMargin: "0px 0px -100px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const skillBar = entry.target;
        const progress = skillBar.getAttribute("data-progress");

        setTimeout(() => {
          skillBar.style.width = progress + "%";
        }, 200);

        observer.unobserve(skillBar);
      }
    });
  }, observerOptions);

  skillBars.forEach((bar) => observer.observe(bar));
}

// Contact Form
function initializeContactForm() {
  const contactForm = document.getElementById("contactForm");

  if (!contactForm) return;

  contactForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitButton.disabled = true;

    // Send form data
    fetch("api/contact.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Message sent successfully!", "success");
          contactForm.reset();
        } else {
          showNotification(
            data.message || "Error sending message. Please try again.",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error sending message. Please try again.", "error");
      })
      .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
      });
  });

  // Form validation
  const inputs = contactForm.querySelectorAll("input, textarea");
  inputs.forEach((input) => {
    input.addEventListener("blur", validateInput);
    input.addEventListener("input", clearValidationError);
  });
}

function validateInput(e) {
  const input = e.target;
  const value = input.value.trim();

  // Clear previous errors
  clearValidationError(e);

  // Validation rules
  if (input.hasAttribute("required") && !value) {
    showInputError(input, "This field is required");
    return false;
  }

  if (input.type === "email" && value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      showInputError(input, "Please enter a valid email address");
      return false;
    }
  }

  return true;
}

function showInputError(input, message) {
  const formGroup = input.closest(".form-group");
  let errorElement = formGroup.querySelector(".error-message");

  if (!errorElement) {
    errorElement = document.createElement("span");
    errorElement.className = "error-message";
    errorElement.style.color = "var(--error-color, #ef4444)";
    errorElement.style.fontSize = "0.875rem";
    errorElement.style.marginTop = "0.25rem";
    formGroup.appendChild(errorElement);
  }

  errorElement.textContent = message;
  input.style.borderColor = "var(--error-color, #ef4444)";
}

function clearValidationError(e) {
  const input = e.target;
  const formGroup = input.closest(".form-group");
  const errorElement = formGroup.querySelector(".error-message");

  if (errorElement) {
    errorElement.remove();
  }

  input.style.borderColor = "";
}

// Notifications
function showNotification(message, type = "info") {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll(".notification");
  existingNotifications.forEach((notification) => notification.remove());

  // Create notification element
  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

  // Add styles
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${
          type === "success"
            ? "#10b981"
            : type === "error"
            ? "#ef4444"
            : "#3b82f6"
        };
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 400px;
    `;

  notification.querySelector(".notification-content").style.cssText = `
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    `;

  notification.querySelector(".notification-close").style.cssText = `
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1rem;
        padding: 0;
    `;

  document.body.appendChild(notification);

  // Animate in
  setTimeout(() => {
    notification.style.opacity = "1";
    notification.style.transform = "translateX(0)";
  }, 100);

  // Auto remove after 5 seconds
  setTimeout(() => {
    notification.style.opacity = "0";
    notification.style.transform = "translateX(100%)";
    setTimeout(() => notification.remove(), 300);
  }, 5000);
}

// Particles Animation
function initializeParticles() {
  const particlesContainer = document.querySelector(".hero-particles");

  if (!particlesContainer) return;

  // Create floating particles
  for (let i = 0; i < 50; i++) {
    const particle = document.createElement("div");
    particle.className = "floating-particle";
    particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 4 + 2}px;
            height: ${Math.random() * 4 + 2}px;
            background: var(--primary-color);
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            opacity: ${Math.random() * 0.5 + 0.1};
            animation: particle-float ${
              Math.random() * 10 + 10
            }s infinite linear;
        `;
    particlesContainer.appendChild(particle);
  }

  // Add particle animation CSS
  if (!document.querySelector("#particle-styles")) {
    const style = document.createElement("style");
    style.id = "particle-styles";
    style.textContent = `
            @keyframes particle-float {
                0% { transform: translateY(100vh) rotate(0deg); }
                100% { transform: translateY(-100px) rotate(360deg); }
            }
        `;
    document.head.appendChild(style);
  }
}

// Utility Functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function throttle(func, limit) {
  let inThrottle;
  return function () {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => (inThrottle = false), limit);
    }
  };
}

// Image lazy loading
function initializeLazyLoading() {
  const images = document.querySelectorAll('img[loading="lazy"]');

  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src || img.src;
          img.classList.remove("lazy");
          imageObserver.unobserve(img);
        }
      });
    });

    images.forEach((img) => imageObserver.observe(img));
  }
}

// Smooth scroll polyfill for older browsers
function smoothScrollPolyfill() {
  if (!("scrollBehavior" in document.documentElement.style)) {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));

        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      });
    });
  }
}

// Initialize smooth scroll polyfill
smoothScrollPolyfill();

// Keyboard navigation
document.addEventListener("keydown", function (e) {
  // ESC key to close mobile menu
  if (e.key === "Escape") {
    const navMenu = document.getElementById("nav-menu");
    const navToggle = document.getElementById("nav-toggle");

    if (navMenu && navMenu.classList.contains("active")) {
      navMenu.classList.remove("active");
      navToggle.classList.remove("active");
    }
  }
});

// Print functionality
function initializePrint() {
  window.addEventListener("beforeprint", function () {
    // Hide unnecessary elements before printing
    const elementsToHide = document.querySelectorAll(
      ".navbar, .back-to-top, .contact-form"
    );
    elementsToHide.forEach((el) => (el.style.display = "none"));
  });

  window.addEventListener("afterprint", function () {
    // Restore elements after printing
    const elementsToShow = document.querySelectorAll(
      ".navbar, .back-to-top, .contact-form"
    );
    elementsToShow.forEach((el) => (el.style.display = ""));
  });
}

initializePrint();

// Service Worker Registration (for PWA features)
if ("serviceWorker" in navigator) {
  window.addEventListener("load", function () {
    navigator.serviceWorker
      .register("/sw.js")
      .then((registration) => console.log("SW registered: ", registration))
      .catch((registrationError) =>
        console.log("SW registration failed: ", registrationError)
      );
  });
}

// Performance monitoring
function initializePerformanceMonitoring() {
  if ("performance" in window) {
    window.addEventListener("load", function () {
      setTimeout(() => {
        const perfData = performance.getEntriesByType("navigation")[0];
        console.log(
          "Page Load Time:",
          perfData.loadEventEnd - perfData.loadEventStart
        );
      }, 0);
    });
  }
}

initializePerformanceMonitoring();
