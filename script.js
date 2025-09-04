// Smooth scroll for navigation links
document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const hamburger = document.querySelector(".hamburger");
  const navMenuWrapper = document.querySelector(".nav-menu-wrapper");

  if (hamburger && navMenuWrapper) {
    hamburger.addEventListener("click", function () {
      hamburger.classList.toggle("active");
      navMenuWrapper.classList.toggle("active");
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", function () {
        hamburger.classList.remove("active");
        navMenuWrapper.classList.remove("active");
      });
    });
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
});
