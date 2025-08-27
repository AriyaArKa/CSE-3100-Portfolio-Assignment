<?php
require_once '../config/config.php';
requireLogin();

// Get dashboard statistics
$stats = [];
$stats['projects'] = $db->fetchOne("SELECT COUNT(*) as count FROM projects")['count'];
$stats['achievements'] = $db->fetchOne("SELECT COUNT(*) as count FROM achievements")['count'];
$stats['skills'] = $db->fetchOne("SELECT COUNT(*) as count FROM skills")['count'];
$stats['messages'] = $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'];

// Get recent activities
$recentProjects = $db->fetchAll("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
$recentMessages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE[THEME_COOKIE_NAME] ?? DEFAULT_THEME; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Panel</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="personal-info.php" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>Personal Info</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="education.php" class="nav-link">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Education</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="skills.php" class="nav-link">
                            <i class="fas fa-code"></i>
                            <span>Skills</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="projects.php" class="nav-link">
                            <i class="fas fa-folder-open"></i>
                            <span>Projects</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="achievements.php" class="nav-link">
                            <i class="fas fa-trophy"></i>
                            <span>Achievements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="experience.php" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Experience</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="messages.php" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span>Messages</span>
                            <?php if ($stats['messages'] > 0): ?>
                                <span class="badge"><?php echo $stats['messages']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="../index.php" class="btn btn-outline btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    View Site
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>

                <div class="header-right">
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-sun sun-icon"></i>
                        <i class="fas fa-moon moon-icon"></i>
                    </button>

                    <div class="admin-profile">
                        <div class="profile-dropdown">
                            <button class="profile-btn">
                                <img src="../assets/images/admin-avatar.png" alt="Admin" class="profile-avatar">
                                <span>Admin</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="profile.php" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profile
                                </a>
                                <a href="settings.php" class="dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                                <hr class="dropdown-divider">
                                <a href="logout.php" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon projects">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $stats['projects']; ?></div>
                            <div class="stat-label">Projects</div>
                        </div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>12%</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon achievements">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $stats['achievements']; ?></div>
                            <div class="stat-label">Achievements</div>
                        </div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>5%</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon skills">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $stats['skills']; ?></div>
                            <div class="stat-label">Skills</div>
                        </div>
                        <div class="stat-trend neutral">
                            <i class="fas fa-minus"></i>
                            <span>0%</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon messages">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $stats['messages']; ?></div>
                            <div class="stat-label">New Messages</div>
                        </div>
                        <div class="stat-trend <?php echo $stats['messages'] > 0 ? 'positive' : 'neutral'; ?>">
                            <i class="fas fa-<?php echo $stats['messages'] > 0 ? 'arrow-up' : 'minus'; ?>"></i>
                            <span><?php echo $stats['messages'] > 0 ? 'New' : '0%'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="section-card">
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                        <p>Common tasks and shortcuts</p>
                    </div>

                    <div class="quick-actions">
                        <a href="projects.php?action=add" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <h3>Add Project</h3>
                                <p>Create a new project entry</p>
                            </div>
                        </a>

                        <a href="achievements.php?action=add" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="action-content">
                                <h3>Add Achievement</h3>
                                <p>Record a new achievement</p>
                            </div>
                        </a>

                        <a href="skills.php?action=add" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="action-content">
                                <h3>Add Skill</h3>
                                <p>Add a new skill or technology</p>
                            </div>
                        </a>

                        <a href="personal-info.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="action-content">
                                <h3>Update Profile</h3>
                                <p>Edit personal information</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid-layout">
                    <!-- Recent Projects -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2>Recent Projects</h2>
                            <a href="projects.php" class="btn btn-sm btn-outline">View All</a>
                        </div>

                        <div class="activity-list">
                            <?php if (empty($recentProjects)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p>No projects yet</p>
                                    <a href="projects.php?action=add" class="btn btn-primary btn-sm">Add First Project</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentProjects as $project): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-folder"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h4><?php echo sanitize($project['title']); ?></h4>
                                            <p><?php echo truncateText($project['description'], 60); ?></p>
                                            <span class="activity-date"><?php echo timeAgo($project['created_at']); ?></span>
                                        </div>
                                        <div class="activity-actions">
                                            <a href="projects.php?action=edit&id=<?php echo $project['id']; ?>" class="btn-icon">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2>Recent Messages</h2>
                            <a href="messages.php" class="btn btn-sm btn-outline">View All</a>
                        </div>

                        <div class="activity-list">
                            <?php if (empty($recentMessages)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-envelope"></i>
                                    <p>No messages yet</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentMessages as $message): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-envelope<?php echo $message['is_read'] ? '-open' : ''; ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h4><?php echo sanitize($message['name']); ?></h4>
                                            <p><?php echo sanitize($message['subject']); ?></p>
                                            <span class="activity-date"><?php echo timeAgo($message['created_at']); ?></span>
                                        </div>
                                        <div class="activity-actions">
                                            <?php if (!$message['is_read']): ?>
                                                <span class="badge badge-new">New</span>
                                            <?php endif; ?>
                                            <a href="messages.php?action=view&id=<?php echo $message['id']; ?>" class="btn-icon">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Site Analytics -->
                <div class="section-card">
                    <div class="section-header">
                        <h2>Portfolio Overview</h2>
                        <p>Overview of your portfolio content</p>
                    </div>

                    <div class="analytics-grid">
                        <div class="analytics-item">
                            <h3>Content Completion</h3>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 85%"></div>
                            </div>
                            <span class="progress-text">85% Complete</span>
                        </div>

                        <div class="analytics-item">
                            <h3>Skills Coverage</h3>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92%"></div>
                            </div>
                            <span class="progress-text">92% Complete</span>
                        </div>

                        <div class="analytics-item">
                            <h3>Project Portfolio</h3>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 78%"></div>
                            </div>
                            <span class="progress-text">78% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="assets/js/admin.js"></script>
</body>

</html>