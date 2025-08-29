/**
 * ===============================================
 * ARKA'S PORTFOLIO - MAIN JAVASCRIPT
 * ===============================================
 * Author: Arka Braja Prasad Nath
 * Description: Main JavaScript functionality for portfolio
 */

class Portfolio {
  constructor() {
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.initializeAnimations();
    this.setupThemeToggle();
    this.setupNavigation();
    this.setupScrollEffects();
    this.initializeSkillBars();
  }

  setupEventListeners() {
    // DOM Content Loaded
    document.addEventListener("DOMContentLoaded", () => {
      this.handleDOMReady();
    });

    // Window Load
    window.addEventListener("load", () => {
      this.handleWindowLoad();
    });

    // Scroll Events
    window.addEventListener("scroll", () => {
      this.handleScroll();
    });

    // Resize Events
    window.addEventListener("resize", () => {
      this.handleResize();
    });
  }

  handleDOMReady() {
    // Initialize theme from localStorage
    const savedTheme = localStorage.getItem("portfolio-theme") || "light";
    this.setTheme(savedTheme);

    // Initialize navigation
    this.updateActiveNavLink();
  }

  handleWindowLoad() {
    // Trigger skill bar animations
    this.animateSkillBars();

    // Initialize scroll animations
    this.initScrollAnimations();
  }

  handleScroll() {
    // Update navbar appearance
    this.updateNavbarOnScroll();

    // Update active navigation link
    this.updateActiveNavLink();

    // Trigger scroll animations
    this.triggerScrollAnimations();
  }

  handleResize() {
    // Handle mobile navigation
    if (window.innerWidth > 768) {
      this.closeMobileMenu();
    }
  }

  // Theme Management
  setupThemeToggle() {
    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
      themeToggle.addEventListener("click", () => {
        this.toggleTheme();
      });
    }
  }

  toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme");
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    this.setTheme(newTheme);
  }

  setTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
    localStorage.setItem("portfolio-theme", theme);

    // Update theme toggle icon
    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
      const icon = themeToggle.querySelector("i");
      if (icon) {
        icon.className = theme === "dark" ? "fas fa-sun" : "fas fa-moon";
      }
    }
  }

  // Navigation Management
  setupNavigation() {
    // Mobile menu toggle
    const navToggle = document.getElementById("nav-toggle");
    const navMenu = document.getElementById("nav-menu");

    if (navToggle && navMenu) {
      navToggle.addEventListener("click", () => {
        this.toggleMobileMenu();
      });
    }

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", (e) => {
        e.preventDefault();
        const target = document.querySelector(anchor.getAttribute("href"));
        if (target) {
          this.smoothScrollTo(target);
          this.closeMobileMenu();
        }
      });
    });
  }

  toggleMobileMenu() {
    const navMenu = document.getElementById("nav-menu");
    const navToggle = document.getElementById("nav-toggle");

    if (navMenu && navToggle) {
      navMenu.classList.toggle("active");

      // Update hamburger icon
      const icon = navToggle.querySelector("i");
      if (icon) {
        icon.className = navMenu.classList.contains("active")
          ? "fas fa-times"
          : "fas fa-bars";
      }
    }
  }

  closeMobileMenu() {
    const navMenu = document.getElementById("nav-menu");
    const navToggle = document.getElementById("nav-toggle");

    if (navMenu && navToggle) {
      navMenu.classList.remove("active");

      // Reset hamburger icon
      const icon = navToggle.querySelector("i");
      if (icon) {
        icon.className = "fas fa-bars";
      }
    }
  }

  smoothScrollTo(target) {
    const targetPosition = target.offsetTop - 100; // Account for fixed navbar
    window.scrollTo({
      top: targetPosition,
      behavior: "smooth",
    });
  }

  updateNavbarOnScroll() {
    const navbar = document.querySelector(".navbar");
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    }
  }

  updateActiveNavLink() {
    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll(".nav-link");

    let currentSection = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 150;
      const sectionHeight = section.offsetHeight;

      if (
        window.scrollY >= sectionTop &&
        window.scrollY < sectionTop + sectionHeight
      ) {
        currentSection = section.getAttribute("id");
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === `#${currentSection}`) {
        link.classList.add("active");
      }
    });
  }

  // Scroll Animations
  setupScrollEffects() {
    this.observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -100px 0px",
    };

    this.observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in-view");
        }
      });
    }, this.observerOptions);
  }

  initializeAnimations() {
    // Add animation classes to elements
    const animationElements = [".education-item", ".card", ".section-title"];

    animationElements.forEach((selector) => {
      document.querySelectorAll(selector).forEach((el, index) => {
        el.classList.add("animate-on-scroll");
        el.style.animationDelay = `${index * 0.1}s`;
      });
    });
  }

  initScrollAnimations() {
    // Observe elements for scroll animations
    document.querySelectorAll(".animate-on-scroll").forEach((el) => {
      this.observer.observe(el);
    });
  }

  triggerScrollAnimations() {
    // Additional scroll-triggered animations can be added here
  }

  // Skill Bar Animations
  setupSkillBars() {
    const skillObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            this.animateSkillBar(entry.target);
            skillObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.5 }
    );

    document.querySelectorAll(".skill-progress").forEach((skillBar) => {
      skillObserver.observe(skillBar);
    });
  }

  initializeSkillBars() {
    // Set initial width to 0
    document.querySelectorAll(".skill-progress-bar").forEach((bar) => {
      bar.style.width = "0%";
    });
  }

  animateSkillBars() {
    // This will be called when the skills section is in view
    document.querySelectorAll(".skill-progress").forEach((progress) => {
      this.animateSkillBar(progress);
    });
  }

  animateSkillBar(progressContainer) {
    const progressBar = progressContainer.querySelector(".skill-progress-bar");
    const targetWidth = progressBar.getAttribute("data-width") || "0";

    setTimeout(() => {
      progressBar.style.width = targetWidth + "%";
    }, 200);
  }

  // Utility Functions
  debounce(func, wait) {
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

  throttle(func, limit) {
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

  // Loading Animation
  showLoading() {
    const loader = document.createElement("div");
    loader.id = "page-loader";
    loader.innerHTML = `
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <p>Loading Portfolio...</p>
            </div>
        `;
    document.body.appendChild(loader);
  }

  hideLoading() {
    const loader = document.getElementById("page-loader");
    if (loader) {
      loader.style.opacity = "0";
      setTimeout(() => {
        loader.remove();
      }, 300);
    }
  }

  // Social Media Tracking (Optional)
  trackSocialClick(platform) {
    // Add analytics tracking here if needed
    console.log(`Social link clicked: ${platform}`);
  }

  // Contact Form Enhancement (if contact form is added)
  setupContactForm() {
    const contactForm = document.getElementById("contact-form");
    if (contactForm) {
      contactForm.addEventListener("submit", (e) => {
        e.preventDefault();
        this.handleContactSubmit(contactForm);
      });
    }
  }

  handleContactSubmit(form) {
    // Add form submission logic here
    const formData = new FormData(form);
    console.log("Contact form submitted:", Object.fromEntries(formData));

    // Show success message
    this.showNotification("Message sent successfully!", "success");
  }

  showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.classList.add("show");
    }, 100);

    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }

  // Performance Monitoring
  measurePerformance() {
    if ("performance" in window) {
      window.addEventListener("load", () => {
        setTimeout(() => {
          const perfData = performance.timing;
          const loadTime = perfData.loadEventEnd - perfData.navigationStart;
          console.log(`Page load time: ${loadTime}ms`);
        }, 0);
      });
    }
  }
}

// Initialize Portfolio
const portfolio = new Portfolio();

// Additional utility functions
function formatDate(date) {
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "long",
  }).format(new Date(date));
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    portfolio.showNotification("Copied to clipboard!", "success");
  });
}

// Export for potential module use
if (typeof module !== "undefined" && module.exports) {
  module.exports = Portfolio;
}
