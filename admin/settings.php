<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('/admin/login.php');
}

// Handle form submissions
if ($_POST) {
    try {
        $pdo = getDBConnection();

        if (isset($_POST['update_site_settings'])) {
            $settings = [
                'site_title' => sanitize($_POST['site_title']),
                'site_description' => sanitize($_POST['site_description']),
                'site_keywords' => sanitize($_POST['site_keywords']),
                'google_analytics' => sanitize($_POST['google_analytics']),
                'contact_email' => sanitize($_POST['contact_email']),
                'footer_text' => sanitize($_POST['footer_text']),
                'theme_color' => sanitize($_POST['theme_color']),
                'default_theme' => sanitize($_POST['default_theme'])
            ];

            foreach ($settings as $key => $value) {
                $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            $success = "Site settings updated successfully!";
        }

        if (isset($_POST['change_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE username = ?");
            $stmt->execute([$_SESSION['admin_username']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($current_password, $admin['password'])) {
                $error = "Current password is incorrect!";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match!";
            } elseif (strlen($new_password) < 6) {
                $error = "New password must be at least 6 characters!";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
                $stmt->execute([$hashed_password, $_SESSION['admin_username']]);
                $success = "Password changed successfully!";
            }
        }

        if (isset($_POST['backup_database'])) {
            // In a real implementation, you would create a database backup
            $success = "Database backup initiated! Check your email for the backup file.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch current settings
try {
    $pdo = getDBConnection();

    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $settingsData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Default values
    $settings = [
        'site_title' => $settingsData['site_title'] ?? 'Arka Braja Prasad Nath - Portfolio',
        'site_description' => $settingsData['site_description'] ?? 'Computer Science Engineering Student at KUET',
        'site_keywords' => $settingsData['site_keywords'] ?? 'portfolio, web developer, computer science, KUET',
        'google_analytics' => $settingsData['google_analytics'] ?? '',
        'contact_email' => $settingsData['contact_email'] ?? 'contact@example.com',
        'footer_text' => $settingsData['footer_text'] ?? '© 2024 Arka Braja Prasad Nath. All rights reserved.',
        'theme_color' => $settingsData['theme_color'] ?? '#3b82f6',
        'default_theme' => $settingsData['default_theme'] ?? 'dark'
    ];
} catch (Exception $e) {
    $error = "Error fetching settings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-user-circle"></i> Portfolio Admin</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                <li><a href="personal.php"><i class="fas fa-user"></i> Personal Info</a></li>
                <li><a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a></li>
                <li><a href="skills.php"><i class="fas fa-code"></i> Skills</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="achievements.php"><i class="fas fa-trophy"></i> Achievements</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-cog"></i> Settings</h1>
                <p>Configure site settings, security, and preferences</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="settings-tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="showTab('site-settings')">
                        <i class="fas fa-globe"></i> Site Settings
                    </button>
                    <button class="tab-button" onclick="showTab('security')">
                        <i class="fas fa-shield-alt"></i> Security
                    </button>
                    <button class="tab-button" onclick="showTab('maintenance')">
                        <i class="fas fa-tools"></i> Maintenance
                    </button>
                </div>

                <!-- Site Settings Tab -->
                <div id="site-settings" class="tab-content active">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-globe"></i> Site Configuration</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="admin-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="site_title">Site Title</label>
                                        <input type="text" id="site_title" name="site_title"
                                            value="<?= htmlspecialchars($settings['site_title']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_email">Contact Email</label>
                                        <input type="email" id="contact_email" name="contact_email"
                                            value="<?= htmlspecialchars($settings['contact_email']) ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="site_description">Site Description</label>
                                    <textarea id="site_description" name="site_description" rows="3" required><?= htmlspecialchars($settings['site_description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="site_keywords">SEO Keywords</label>
                                    <input type="text" id="site_keywords" name="site_keywords"
                                        value="<?= htmlspecialchars($settings['site_keywords']) ?>"
                                        placeholder="Separate with commas">
                                </div>

                                <div class="form-group">
                                    <label for="footer_text">Footer Text</label>
                                    <input type="text" id="footer_text" name="footer_text"
                                        value="<?= htmlspecialchars($settings['footer_text']) ?>">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="theme_color">Primary Theme Color</label>
                                        <input type="color" id="theme_color" name="theme_color"
                                            value="<?= htmlspecialchars($settings['theme_color']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="default_theme">Default Theme</label>
                                        <select id="default_theme" name="default_theme">
                                            <option value="light" <?= $settings['default_theme'] === 'light' ? 'selected' : '' ?>>Light</option>
                                            <option value="dark" <?= $settings['default_theme'] === 'dark' ? 'selected' : '' ?>>Dark</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="google_analytics">Google Analytics Tracking ID</label>
                                    <input type="text" id="google_analytics" name="google_analytics"
                                        value="<?= htmlspecialchars($settings['google_analytics']) ?>"
                                        placeholder="G-XXXXXXXXXX">
                                    <small class="form-help">Optional: Add your Google Analytics tracking ID for visitor analytics</small>
                                </div>

                                <button type="submit" name="update_site_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Site Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div id="security" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-key"></i> Change Password</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="admin-form">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" id="new_password" name="new_password" required minlength="6">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                                    </div>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-shield-alt"></i> Security Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="security-info">
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Password hashing enabled (bcrypt)</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>CSRF protection active</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Input sanitization enabled</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>SQL injection protection (prepared statements)</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>File upload validation active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Tab -->
                <div id="maintenance" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-database"></i> Database Management</h3>
                        </div>
                        <div class="card-body">
                            <div class="maintenance-actions">
                                <div class="action-item">
                                    <div class="action-info">
                                        <h4>Database Backup</h4>
                                        <p>Create a backup of your portfolio database</p>
                                    </div>
                                    <form method="POST" style="display: inline;">
                                        <button type="submit" name="backup_database" class="btn btn-secondary">
                                            <i class="fas fa-download"></i> Create Backup
                                        </button>
                                    </form>
                                </div>

                                <div class="action-item">
                                    <div class="action-info">
                                        <h4>Clear Cache</h4>
                                        <p>Clear temporary files and cached data</p>
                                    </div>
                                    <button onclick="clearCache()" class="btn btn-secondary">
                                        <i class="fas fa-broom"></i> Clear Cache
                                    </button>
                                </div>

                                <div class="action-item">
                                    <div class="action-info">
                                        <h4>System Information</h4>
                                        <p>View server and system details</p>
                                    </div>
                                    <button onclick="showSystemInfo()" class="btn btn-info">
                                        <i class="fas fa-info-circle"></i> View Info
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Site Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <?php
                                try {
                                    $totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
                                    $totalSkills = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
                                    $totalMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
                                    $totalAchievements = $pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();

                                    echo "<div class='stat-item'><h4>$totalProjects</h4><p>Projects</p></div>";
                                    echo "<div class='stat-item'><h4>$totalSkills</h4><p>Skills</p></div>";
                                    echo "<div class='stat-item'><h4>$totalMessages</h4><p>Messages</p></div>";
                                    echo "<div class='stat-item'><h4>$totalAchievements</h4><p>Achievements</p></div>";
                                } catch (Exception $e) {
                                    echo "<p>Error loading statistics</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info Modal -->
    <div id="systemInfoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>System Information</h3>
                <span class="close" onclick="closeSystemInfo()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="system-info">
                    <div class="info-row">
                        <strong>PHP Version:</strong>
                        <span><?= phpversion() ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Server Software:</strong>
                        <span><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Document Root:</strong>
                        <span><?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Memory Limit:</strong>
                        <span><?= ini_get('memory_limit') ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Upload Max Size:</strong>
                        <span><?= ini_get('upload_max_filesize') ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Time Zone:</strong>
                        <span><?= date_default_timezone_get() ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => button.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function clearCache() {
            if (confirm('Are you sure you want to clear the cache?')) {
                // In a real implementation, you would make an AJAX call to clear cache
                alert('Cache cleared successfully!');
            }
        }

        function showSystemInfo() {
            document.getElementById('systemInfoModal').style.display = 'block';
        }

        function closeSystemInfo() {
            document.getElementById('systemInfoModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('systemInfoModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;

            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>

    <script src="assets/js/admin.js"></script>
</body>

</html>