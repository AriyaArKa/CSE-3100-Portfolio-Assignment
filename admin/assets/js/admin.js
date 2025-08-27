/**
 * Admin Panel JavaScript
 * Portfolio Management System
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeAdminPanel();
});

function initializeAdminPanel() {
  initializeSidebar();
  initializeTheme();
  initializeModals();
  initializeFileUpload();
  initializeForms();
  initializeDataTables();
  initializeNotifications();

  console.log("Admin panel initialized");
}

// Sidebar Management
function initializeSidebar() {
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".admin-sidebar");
  const overlay = document.createElement("div");

  overlay.className = "sidebar-overlay";
  overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    `;
  document.body.appendChild(overlay);

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      overlay.classList.toggle("active");

      if (sidebar.classList.contains("active")) {
        overlay.style.opacity = "1";
        overlay.style.visibility = "visible";
        document.body.style.overflow = "hidden";
      } else {
        overlay.style.opacity = "0";
        overlay.style.visibility = "hidden";
        document.body.style.overflow = "";
      }
    });

    overlay.addEventListener("click", function () {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
      overlay.style.opacity = "0";
      overlay.style.visibility = "hidden";
      document.body.style.overflow = "";
    });
  }

  // Active navigation highlighting
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll(".nav-link");

  navLinks.forEach((link) => {
    const linkPath = new URL(link.href).pathname;
    if (
      currentPath === linkPath ||
      (currentPath.includes(linkPath) && linkPath !== "/admin/")
    ) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

// Theme Management
function initializeTheme() {
  const themeToggle = document.getElementById("themeToggle");

  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      const currentTheme = document.documentElement.getAttribute("data-theme");
      const newTheme = currentTheme === "dark" ? "light" : "dark";

      document.documentElement.setAttribute("data-theme", newTheme);
      localStorage.setItem("admin_theme", newTheme);

      // Update cookie for frontend
      updateThemeCookie(newTheme);
    });
  }

  // Load saved theme
  const savedTheme = localStorage.getItem("admin_theme") || "light";
  document.documentElement.setAttribute("data-theme", savedTheme);
}

function updateThemeCookie(theme) {
  const expires = new Date();
  expires.setTime(expires.getTime() + 30 * 24 * 60 * 60 * 1000);
  document.cookie = `portfolio_theme=${theme}; expires=${expires.toUTCString()}; path=/`;
}

// Modal Management
function initializeModals() {
  // Initialize all modals
  const modals = document.querySelectorAll(".modal-overlay");

  modals.forEach((modal) => {
    const closeBtn = modal.querySelector(".modal-close");

    if (closeBtn) {
      closeBtn.addEventListener("click", () => closeModal(modal));
    }

    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        closeModal(modal);
      }
    });
  });

  // Initialize modal triggers
  const modalTriggers = document.querySelectorAll("[data-modal]");
  modalTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function (e) {
      e.preventDefault();
      const modalId = this.getAttribute("data-modal");
      const modal = document.getElementById(modalId);
      if (modal) {
        openModal(modal);
      }
    });
  });
}

function openModal(modal) {
  modal.classList.add("active");
  document.body.style.overflow = "hidden";

  // Focus management
  const focusableElements = modal.querySelectorAll(
    "input, textarea, select, button"
  );
  if (focusableElements.length > 0) {
    focusableElements[0].focus();
  }
}

function closeModal(modal) {
  modal.classList.remove("active");
  document.body.style.overflow = "";
}

// File Upload
function initializeFileUpload() {
  const fileUploadAreas = document.querySelectorAll(".file-upload-area");

  fileUploadAreas.forEach((area) => {
    const input = area.querySelector('input[type="file"]');
    const previewContainer = area.nextElementSibling;

    if (!input) return;

    // Click to select
    area.addEventListener("click", () => input.click());

    // Drag and drop
    area.addEventListener("dragover", function (e) {
      e.preventDefault();
      this.classList.add("dragover");
    });

    area.addEventListener("dragleave", function (e) {
      e.preventDefault();
      this.classList.remove("dragover");
    });

    area.addEventListener("drop", function (e) {
      e.preventDefault();
      this.classList.remove("dragover");

      const files = e.dataTransfer.files;
      if (files.length > 0) {
        input.files = files;
        handleFileSelection(input, previewContainer);
      }
    });

    // File selection
    input.addEventListener("change", function () {
      handleFileSelection(this, previewContainer);
    });
  });
}

function handleFileSelection(input, previewContainer) {
  const files = input.files;

  if (previewContainer) {
    previewContainer.innerHTML = "";

    Array.from(files).forEach((file) => {
      const preview = createFilePreview(file);
      previewContainer.appendChild(preview);
    });
  }
}

function createFilePreview(file) {
  const preview = document.createElement("div");
  preview.className = "file-preview";

  const isImage = file.type.startsWith("image/");
  const icon = isImage ? "fa-image" : "fa-file";

  preview.innerHTML = `
        <div class="file-preview-icon">
            <i class="fas ${icon}"></i>
        </div>
        <div class="file-preview-info">
            <div class="file-preview-name">${file.name}</div>
            <div class="file-preview-size">${formatFileSize(file.size)}</div>
        </div>
        <button type="button" class="file-preview-remove" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

  return preview;
}

function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes";

  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}

// Form Management
function initializeForms() {
  const forms = document.querySelectorAll("form[data-ajax]");

  forms.forEach((form) => {
    form.addEventListener("submit", handleAjaxForm);
  });

  // Auto-save functionality
  const autoSaveForms = document.querySelectorAll("form[data-autosave]");
  autoSaveForms.forEach((form) => {
    const inputs = form.querySelectorAll("input, textarea, select");
    inputs.forEach((input) => {
      input.addEventListener(
        "input",
        debounce(() => autoSaveForm(form), 2000)
      );
    });
  });

  // Character counters
  const textareas = document.querySelectorAll("textarea[data-max-length]");
  textareas.forEach((textarea) => {
    addCharacterCounter(textarea);
  });
}

function handleAjaxForm(e) {
  e.preventDefault();

  const form = e.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const formData = new FormData(form);

  // Show loading state
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
  submitBtn.disabled = true;

  fetch(form.action, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message || "Saved successfully!", "success");

        // Redirect if specified
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1000);
        }
      } else {
        showNotification(data.message || "Error saving data", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error saving data", "error");
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
}

function autoSaveForm(form) {
  const formData = new FormData(form);
  formData.append("auto_save", "1");

  fetch(form.action, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Auto-saved", "info", 2000);
      }
    })
    .catch((error) => {
      console.error("Auto-save error:", error);
    });
}

function addCharacterCounter(textarea) {
  const maxLength = parseInt(textarea.getAttribute("data-max-length"));
  const counter = document.createElement("div");
  counter.className = "character-counter";
  counter.style.cssText = `
        font-size: 0.875rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 0.25rem;
    `;

  textarea.parentNode.appendChild(counter);

  function updateCounter() {
    const remaining = maxLength - textarea.value.length;
    counter.textContent = `${remaining} characters remaining`;

    if (remaining < 0) {
      counter.style.color = "#ef4444";
    } else if (remaining < 50) {
      counter.style.color = "#f59e0b";
    } else {
      counter.style.color = "var(--text-muted)";
    }
  }

  textarea.addEventListener("input", updateCounter);
  updateCounter();
}

// Data Tables
function initializeDataTables() {
  const tables = document.querySelectorAll(".data-table");

  tables.forEach((table) => {
    addTableFeatures(table);
  });
}

function addTableFeatures(table) {
  // Add search functionality
  const searchInput = table.parentElement.querySelector(".table-search");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      filterTable(table, this.value);
    });
  }

  // Add sorting functionality
  const headers = table.querySelectorAll("th[data-sortable]");
  headers.forEach((header) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", function () {
      sortTable(table, this);
    });
  });

  // Add row actions
  const actionBtns = table.querySelectorAll("[data-action]");
  actionBtns.forEach((btn) => {
    btn.addEventListener("click", handleTableAction);
  });
}

function filterTable(table, searchTerm) {
  const rows = table.querySelectorAll("tbody tr");
  const term = searchTerm.toLowerCase();

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(term) ? "" : "none";
  });
}

function sortTable(table, header) {
  const columnIndex = Array.from(header.parentNode.children).indexOf(header);
  const rows = Array.from(table.querySelectorAll("tbody tr"));
  const isAscending = header.classList.contains("sort-asc");

  // Clear all sort classes
  table.querySelectorAll("th").forEach((th) => {
    th.classList.remove("sort-asc", "sort-desc");
  });

  // Add sort class to current header
  header.classList.add(isAscending ? "sort-desc" : "sort-asc");

  rows.sort((a, b) => {
    const aText = a.children[columnIndex].textContent.trim();
    const bText = b.children[columnIndex].textContent.trim();

    // Try to parse as numbers first
    const aNum = parseFloat(aText);
    const bNum = parseFloat(bText);

    if (!isNaN(aNum) && !isNaN(bNum)) {
      return isAscending ? bNum - aNum : aNum - bNum;
    }

    // Sort as strings
    return isAscending
      ? bText.localeCompare(aText)
      : aText.localeCompare(bText);
  });

  const tbody = table.querySelector("tbody");
  rows.forEach((row) => tbody.appendChild(row));
}

function handleTableAction(e) {
  e.preventDefault();

  const action = this.getAttribute("data-action");
  const id = this.getAttribute("data-id");
  const url = this.getAttribute("href") || this.getAttribute("data-url");

  switch (action) {
    case "delete":
      confirmDelete(id, url);
      break;
    case "toggle":
      toggleStatus(id, url);
      break;
    default:
      if (url) {
        window.location.href = url;
      }
  }
}

function confirmDelete(id, url) {
  if (confirm("Are you sure you want to delete this item?")) {
    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ action: "delete", id: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Item deleted successfully", "success");
          // Remove row from table
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) {
            row.remove();
          }
        } else {
          showNotification(data.message || "Error deleting item", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Error deleting item", "error");
      });
  }
}

function toggleStatus(id, url) {
  fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ action: "toggle", id: id }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Status updated", "success");
        // Update the UI to reflect the change
        location.reload();
      } else {
        showNotification(data.message || "Error updating status", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error updating status", "error");
    });
}

// Notifications
function initializeNotifications() {
  // Auto-hide existing notifications
  const notifications = document.querySelectorAll(".notification");
  notifications.forEach((notification) => {
    setTimeout(() => {
      hideNotification(notification);
    }, 5000);
  });
}

function showNotification(message, type = "info", duration = 5000) {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll(".notification");
  existingNotifications.forEach((notification) => notification.remove());

  const notification = document.createElement("div");
  notification.className = `notification ${type}`;

  const icon = getNotificationIcon(type);

  notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 400px;
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        color: var(--text-color);
    `;

  if (type === "success") {
    notification.style.borderLeftColor = "#10b981";
  } else if (type === "error") {
    notification.style.borderLeftColor = "#ef4444";
  } else if (type === "warning") {
    notification.style.borderLeftColor = "#f59e0b";
  } else {
    notification.style.borderLeftColor = "#3b82f6";
  }

  notification.querySelector(".notification-content").style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;

  notification.querySelector(".notification-close").style.cssText = `
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        margin-left: auto;
        padding: 0;
    `;

  document.body.appendChild(notification);

  // Animate in
  setTimeout(() => {
    notification.style.opacity = "1";
    notification.style.transform = "translateX(0)";
  }, 100);

  // Auto hide
  setTimeout(() => {
    hideNotification(notification);
  }, duration);
}

function getNotificationIcon(type) {
  switch (type) {
    case "success":
      return "fa-check-circle";
    case "error":
      return "fa-exclamation-circle";
    case "warning":
      return "fa-exclamation-triangle";
    case "info":
    default:
      return "fa-info-circle";
  }
}

function hideNotification(notification) {
  notification.style.opacity = "0";
  notification.style.transform = "translateX(100%)";
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 300);
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

// Keyboard shortcuts
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + S to save forms
  if ((e.ctrlKey || e.metaKey) && e.key === "s") {
    e.preventDefault();
    const form = document.querySelector("form[data-ajax]");
    if (form) {
      form.requestSubmit();
    }
  }

  // Escape to close modals
  if (e.key === "Escape") {
    const activeModal = document.querySelector(".modal-overlay.active");
    if (activeModal) {
      closeModal(activeModal);
    }
  }
});

// Print functionality
function printTable(tableId) {
  const table = document.getElementById(tableId);
  if (!table) return;

  const printWindow = window.open("", "_blank");
  printWindow.document.write(`
        <html>
            <head>
                <title>Print Table</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                ${table.outerHTML}
            </body>
        </html>
    `);
  printWindow.document.close();
  printWindow.print();
}

// Export functionality
function exportTableToCSV(tableId, filename = "export.csv") {
  const table = document.getElementById(tableId);
  if (!table) return;

  const rows = table.querySelectorAll("tr");
  const csvContent = Array.from(rows)
    .map((row) => {
      const cells = row.querySelectorAll("th, td");
      return Array.from(cells)
        .map((cell) => {
          const text = cell.textContent.trim();
          return `"${text.replace(/"/g, '""')}"`;
        })
        .join(",");
    })
    .join("\n");

  const blob = new Blob([csvContent], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  window.URL.revokeObjectURL(url);
}

// Global functions for inline usage
window.openModal = openModal;
window.closeModal = closeModal;
window.showNotification = showNotification;
window.printTable = printTable;
window.exportTableToCSV = exportTableToCSV;
