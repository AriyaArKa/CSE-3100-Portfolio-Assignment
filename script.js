// Smooth scroll for navigation links
document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const hamburger = document.querySelector(".hamburger");
  const navMenuWrapper = document.querySelector(".nav-menu-wrapper");
  const navCta = document.querySelector(".nav-cta");

  if (hamburger && navMenuWrapper) {
    hamburger.addEventListener("click", function () {
      hamburger.classList.toggle("active");
      navMenuWrapper.classList.toggle("active");
      if (navCta) {
        navCta.classList.toggle("active");
      }
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", function () {
        hamburger.classList.remove("active");
        navMenuWrapper.classList.remove("active");
        if (navCta) {
          navCta.classList.remove("active");
        }
      });
    });

    // Close mobile menu when clicking on contact button
    const navContactBtn = document.querySelector(".nav-contact-btn");
    if (navContactBtn) {
      navContactBtn.addEventListener("click", function () {
        hamburger.classList.remove("active");
        navMenuWrapper.classList.remove("active");
        if (navCta) {
          navCta.classList.remove("active");
        }
      });
    }
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        const headerOffset = 80;
        const elementPosition = target.getBoundingClientRect().top;
        const offsetPosition =
          elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        });
      }
    });
  });

  // Active navigation link highlighting
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".nav-link");

  function highlightNavigation() {
    let current = "";
    sections.forEach((section) => {
      const sectionTop = section.getBoundingClientRect().top;
      if (sectionTop <= 100) {
        current = section.getAttribute("id");
      }
    });

    navLinks.forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === `#${current}`) {
        link.classList.add("active");
      }
    });
  }

  window.addEventListener("scroll", highlightNavigation);

  // Animate skill bars on scroll
  const skillBars = document.querySelectorAll(".skill-progress");
  const animateSkillBars = () => {
    skillBars.forEach((bar) => {
      const rect = bar.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        const width = bar.style.width;
        bar.style.width = "0%";
        setTimeout(() => {
          bar.style.width = width;
        }, 100);
      }
    });
  };

  // Intersection Observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate");

        // Trigger skill bar animation
        if (entry.target.closest(".skills")) {
          animateSkillBars();
        }
      }
    });
  }, observerOptions);

  // Observe elements for animation
  const animateElements = document.querySelectorAll(
    ".project-card, .achievement-card, .testimonial-card, .skill-category"
  );
  animateElements.forEach((el) => {
    observer.observe(el);
  });

  // Contact form validation
  const contactForm = document.querySelector(".contact-form form");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      const name = this.querySelector('input[name="name"]').value.trim();
      const email = this.querySelector('input[name="email"]').value.trim();
      const message = this.querySelector(
        'textarea[name="message"]'
      ).value.trim();

      if (!name || !email || !message) {
        e.preventDefault();
        showAlert("Please fill in all fields.", "error");
        return;
      }

      if (!isValidEmail(email)) {
        e.preventDefault();
        showAlert("Please enter a valid email address.", "error");
        return;
      }
    });
  }

  // Email validation function
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Show alert function
  function showAlert(message, type) {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    const form = document.querySelector(".contact-form");
    if (form) {
      form.insertBefore(alertDiv, form.firstChild);

      // Remove alert after 5 seconds
      setTimeout(() => {
        alertDiv.remove();
      }, 5000);
    }
  }

  // Auto-hide alerts
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => {
        alert.remove();
      }, 300);
    }, 5000);
  });

  // Testimonials slider (simple implementation)
  const testimonialCards = document.querySelectorAll(".testimonial-card");
  let currentTestimonial = 0;

  function showTestimonial(index) {
    testimonialCards.forEach((card, i) => {
      card.style.display = i === index ? "block" : "none";
    });
  }

  function nextTestimonial() {
    currentTestimonial = (currentTestimonial + 1) % testimonialCards.length;
    showTestimonial(currentTestimonial);
  }

  // Auto-rotate testimonials every 5 seconds
  if (testimonialCards.length > 1) {
    // Auto-rotation removed - manual slider only
  } // Header background on scroll - Enhanced for centered navbar
  const header = document.querySelector(".header");
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  // Typing animation for hero title
  const heroTitle = document.querySelector(".hero-title");
  if (heroTitle) {
    const text = heroTitle.textContent;
    heroTitle.textContent = "";

    let i = 0;
    function typeWriter() {
      if (i < text.length) {
        heroTitle.textContent += text.charAt(i);
        i++;
        setTimeout(typeWriter, 100);
      }
    }

    // Start typing animation after page load
    setTimeout(typeWriter, 1000);
  }

  // Parallax effect for hero section
  window.addEventListener("scroll", () => {
    const hero = document.querySelector(".hero");
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.5;
  });

  // Add loading animation
  window.addEventListener("load", () => {
    document.body.classList.add("loaded");
  });

  // Skills category switching
  const categoryBtns = document.querySelectorAll(".category-btn");
  const skillCategories = document.querySelectorAll(".skill-category");

  categoryBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      // Remove active class from all buttons and categories
      categoryBtns.forEach((b) => b.classList.remove("active"));
      skillCategories.forEach((cat) => cat.classList.remove("active"));

      // Add active class to clicked button
      btn.classList.add("active");

      // Show corresponding category
      const categoryId = btn.getAttribute("data-category");
      const targetCategory = document.getElementById(categoryId);
      if (targetCategory) {
        targetCategory.classList.add("active");
      }
    });
  });
});

// Admin Panel JavaScript
if (window.location.pathname.includes("admin")) {
  document.addEventListener("DOMContentLoaded", function () {
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll(".btn-danger");
    deleteButtons.forEach((button) => {
      if (
        button.textContent.includes("Delete") ||
        button.textContent.includes("Remove")
      ) {
        button.addEventListener("click", function (e) {
          if (!confirm("Are you sure you want to delete this item?")) {
            e.preventDefault();
          }
        });
      }
    });

    // Form validation for admin forms
    const adminForms = document.querySelectorAll("form");
    adminForms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        const requiredFields = form.querySelectorAll(
          "input[required], textarea[required], select[required]"
        );
        let isValid = true;

        requiredFields.forEach((field) => {
          if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = "#dc3545";

            // Reset border color after user starts typing
            field.addEventListener(
              "input",
              function () {
                this.style.borderColor = "#ddd";
              },
              { once: true }
            );
          }
        });

        if (!isValid) {
          e.preventDefault();
          alert("Please fill in all required fields.");
        }
      });
    });

    // Auto-hide success/error messages
    const messages = document.querySelectorAll(".alert");
    messages.forEach((message) => {
      setTimeout(() => {
        message.style.opacity = "0";
        setTimeout(() => {
          message.remove();
        }, 300);
      }, 5000);
    });

    // Table row highlighting
    const tableRows = document.querySelectorAll(".admin-table tbody tr");
    tableRows.forEach((row) => {
      row.addEventListener("mouseenter", function () {
        this.style.backgroundColor = "#f8f9fa";
      });

      row.addEventListener("mouseleave", function () {
        this.style.backgroundColor = "";
      });
    });

    // Search functionality for tables
    const searchInputs = document.querySelectorAll('input[type="search"]');
    searchInputs.forEach((input) => {
      input.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase();
        const table = this.closest(".admin-card").querySelector(".admin-table");
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(searchTerm) ? "" : "none";
        });
      });
    });

    // Sort table columns
    const sortableHeaders = document.querySelectorAll(
      ".admin-table th[data-sort]"
    );
    sortableHeaders.forEach((header) => {
      header.style.cursor = "pointer";
      header.addEventListener("click", function () {
        const table = this.closest("table");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const column = this.dataset.sort;
        const isAscending = this.classList.contains("sort-asc");

        rows.sort((a, b) => {
          const aVal = a
            .querySelector(`td:nth-child(${this.cellIndex + 1})`)
            .textContent.trim();
          const bVal = b
            .querySelector(`td:nth-child(${this.cellIndex + 1})`)
            .textContent.trim();

          if (isAscending) {
            return bVal.localeCompare(aVal);
          } else {
            return aVal.localeCompare(bVal);
          }
        });

        // Remove existing sort classes
        sortableHeaders.forEach((h) =>
          h.classList.remove("sort-asc", "sort-desc")
        );

        // Add appropriate sort class
        this.classList.add(isAscending ? "sort-desc" : "sort-asc");

        // Append sorted rows
        rows.forEach((row) => tbody.appendChild(row));
      });
    });
  });
}

// Testimonials Slider Functionality
let currentTestimonialIndex = 0;

function showTestimonialSlide(index) {
  const slides = document.querySelectorAll(".testimonial-slide");
  const dots = document.querySelectorAll(".slider-dots .dot");

  // Hide all slides
  slides.forEach((slide) => {
    slide.classList.remove("active");
  });

  // Remove active class from all dots
  dots.forEach((dot) => {
    dot.classList.remove("active");
  });

  // Show current slide
  if (slides[index]) {
    slides[index].classList.add("active");
  }

  // Activate current dot
  if (dots[index]) {
    dots[index].classList.add("active");
  }
}

function changeTestimonialSlide(direction) {
  const slides = document.querySelectorAll(".testimonial-slide");

  if (slides.length === 0) return;

  currentTestimonialIndex += direction;

  if (currentTestimonialIndex >= slides.length) {
    currentTestimonialIndex = 0;
  } else if (currentTestimonialIndex < 0) {
    currentTestimonialIndex = slides.length - 1;
  }

  showTestimonialSlide(currentTestimonialIndex);
}

function currentTestimonialSlide(index) {
  currentTestimonialIndex = index - 1;
  showTestimonialSlide(currentTestimonialIndex);
}

// Initialize testimonials slider when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelectorAll(".testimonial-slide");

  if (slides.length > 0) {
    showTestimonialSlide(0);
    // Auto-play removed - manual navigation only
  }

  // Initialize projects slider
  initializeProjectsSlider();
});

// Projects Slider Functionality
let currentProjectIndex = 0;

function initializeProjectsSlider() {
  const slides = document.querySelectorAll(".project-slide");
  if (slides.length === 0) return;

  currentProjectIndex = Math.floor(slides.length / 2); // Start with middle slide
  updateProjectsSlider();
}

function updateProjectsSlider() {
  const slides = document.querySelectorAll(".project-slide");
  const dots = document.querySelectorAll(".slider-dot");
  const totalSlides = slides.length;

  if (totalSlides === 0) return;

  slides.forEach((slide, index) => {
    slide.classList.remove("center");
    slide.style.display = "block";

    const position = index - currentProjectIndex;

    if (position === 0) {
      // Center slide
      slide.classList.add("center");
      slide.style.transform = "translateX(-50%) scale(1)";
      slide.style.opacity = "1";
      slide.style.zIndex = "3";
      slide.style.filter = "blur(0)";
    } else if (position === -1) {
      // Left adjacent
      slide.style.transform = "translateX(-150%) scale(0.8)";
      slide.style.opacity = "0.6";
      slide.style.zIndex = "2";
      slide.style.filter = "blur(1px)";
    } else if (position === 1) {
      // Right adjacent
      slide.style.transform = "translateX(50%) scale(0.8)";
      slide.style.opacity = "0.6";
      slide.style.zIndex = "2";
      slide.style.filter = "blur(1px)";
    } else if (position === -2) {
      // Far left
      slide.style.transform = "translateX(-250%) scale(0.7)";
      slide.style.opacity = "0.3";
      slide.style.zIndex = "1";
      slide.style.filter = "blur(2px)";
    } else if (position === 2) {
      // Far right
      slide.style.transform = "translateX(150%) scale(0.7)";
      slide.style.opacity = "0.3";
      slide.style.zIndex = "1";
      slide.style.filter = "blur(2px)";
    } else {
      // Hidden slides
      slide.style.display = "none";
    }
  });

  // Update dots
  dots.forEach((dot, index) => {
    dot.classList.toggle("active", index === currentProjectIndex);
  });
}

function nextSlide() {
  const slides = document.querySelectorAll(".project-slide");
  if (slides.length === 0) return;

  currentProjectIndex = (currentProjectIndex + 1) % slides.length;
  updateProjectsSlider();
}

function previousSlide() {
  const slides = document.querySelectorAll(".project-slide");
  if (slides.length === 0) return;

  currentProjectIndex =
    currentProjectIndex === 0 ? slides.length - 1 : currentProjectIndex - 1;
  updateProjectsSlider();
}

function goToSlide(index) {
  currentProjectIndex = index;
  updateProjectsSlider();
}

// Cookie Management System
class CookieManager {
  constructor() {
    this.cookieConsent = document.getElementById("cookieConsent");
    this.cookieModal = document.getElementById("cookieModal");
    this.init();
  }

  init() {
    // Check if user has already made a choice
    if (!this.getCookie("cookieConsent")) {
      this.showCookieBanner();
    }

    // Event listeners
    document
      .getElementById("acceptCookies")
      ?.addEventListener("click", () => this.acceptAllCookies());
    document
      .getElementById("declineCookies")
      ?.addEventListener("click", () => this.declineAllCookies());
    document
      .getElementById("cookieSettings")
      ?.addEventListener("click", () => this.showCookieModal());
    document
      .getElementById("closeCookieModal")
      ?.addEventListener("click", () => this.hideCookieModal());
    document
      .getElementById("saveSettings")
      ?.addEventListener("click", () => this.saveCustomSettings());
    document
      .getElementById("acceptAllModal")
      ?.addEventListener("click", () => this.acceptAllFromModal());

    // Close modal when clicking outside
    this.cookieModal?.addEventListener("click", (e) => {
      if (e.target === this.cookieModal) {
        this.hideCookieModal();
      }
    });
  }

  showCookieBanner() {
    this.cookieConsent?.classList.add("show");
  }

  hideCookieBanner() {
    this.cookieConsent?.classList.remove("show");
  }

  showCookieModal() {
    this.cookieModal?.classList.add("show");
    this.loadCurrentSettings();
  }

  hideCookieModal() {
    this.cookieModal?.classList.remove("show");
  }

  loadCurrentSettings() {
    const settings = this.getCookieSettings();
    document.getElementById("essential").checked = true; // Always true
    document.getElementById("analytics").checked = settings.analytics || false;
    document.getElementById("marketing").checked = settings.marketing || false;
  }

  acceptAllCookies() {
    const settings = {
      essential: true,
      analytics: true,
      marketing: true,
    };
    this.setCookieSettings(settings);
    this.setCookie("cookieConsent", "accepted", 365);
    this.hideCookieBanner();
    this.loadCookies(settings);
  }

  declineAllCookies() {
    const settings = {
      essential: true,
      analytics: false,
      marketing: false,
    };
    this.setCookieSettings(settings);
    this.setCookie("cookieConsent", "declined", 365);
    this.hideCookieBanner();
    this.loadCookies(settings);
  }

  acceptAllFromModal() {
    this.acceptAllCookies();
    this.hideCookieModal();
  }

  saveCustomSettings() {
    const settings = {
      essential: true,
      analytics: document.getElementById("analytics").checked,
      marketing: document.getElementById("marketing").checked,
    };
    this.setCookieSettings(settings);
    this.setCookie("cookieConsent", "custom", 365);
    this.hideCookieBanner();
    this.hideCookieModal();
    this.loadCookies(settings);
  }

  loadCookies(settings) {
    // Load essential cookies (always loaded)
    this.loadEssentialCookies();

    // Load analytics cookies if accepted
    if (settings.analytics) {
      this.loadAnalyticsCookies();
    }

    // Load marketing cookies if accepted
    if (settings.marketing) {
      this.loadMarketingCookies();
    }
  }

  loadEssentialCookies() {
    // These are necessary for the website to function
    console.log("Essential cookies loaded");
  }

  loadAnalyticsCookies() {
    // Load Google Analytics or other analytics
    console.log("Analytics cookies loaded");
    // Example: Google Analytics
    // gtag('config', 'GA_TRACKING_ID');
  }

  loadMarketingCookies() {
    // Load marketing/advertising cookies
    console.log("Marketing cookies loaded");
    // Example: Facebook Pixel, Google Ads, etc.
  }

  setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
  }

  getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  setCookieSettings(settings) {
    this.setCookie("cookieSettings", JSON.stringify(settings), 365);
  }

  getCookieSettings() {
    const settings = this.getCookie("cookieSettings");
    return settings
      ? JSON.parse(settings)
      : {
          essential: true,
          analytics: false,
          marketing: false,
        };
  }

  deleteCookie(name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
  }
}

// Initialize cookie manager when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  new CookieManager();
});

// Utility function to check if specific cookie type is allowed
function isCookieAllowed(type) {
  const cookieManager = new CookieManager();
  const settings = cookieManager.getCookieSettings();
  return settings[type] || false;
}

// Function to add cookie to track user preferences
function setUserPreference(key, value) {
  if (isCookieAllowed("essential")) {
    localStorage.setItem("userPref_" + key, value);
  }
}

function getUserPreference(key) {
  if (isCookieAllowed("essential")) {
    return localStorage.getItem("userPref_" + key);
  }
  return null;
}
