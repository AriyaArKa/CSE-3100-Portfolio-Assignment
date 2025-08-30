<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Get counts for dashboard stats
$stats = [];
$tables = ['education', '                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-share-alt text-dark"></i> Social Media Links</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Connect your social media profiles and contact information.</p>
                                            <a href="social_links.php" class="btn btn-sm btn-secondary">Manage</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>ts', 'projects', 'social_links', 'certificates', 'experience', 'gallery'];

foreach ($tables as $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
    $stmt->execute();
    $stats[$table] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get recent activities
$recent_activities = [];
try {
    $stmt = $conn->prepare("
        (SELECT 'education' as type, degree as title, created_at FROM education ORDER BY created_at DESC LIMIT 2)
        UNION ALL
        (SELECT 'achievement' as type, title, created_at FROM achievements ORDER BY created_at DESC LIMIT 2)
        UNION ALL
        (SELECT 'project' as type, title, created_at FROM projects ORDER BY created_at DESC LIMIT 2)
        ORDER BY created_at DESC LIMIT 6
    ");
    $stmt->execute();
    $recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle if tables don't exist yet
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Arka's Portfolio</title>
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            margin: 5px 0;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .stat-card {
            border-radius: 15px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center text-white mb-4">
                    <h4><i class="icon-user-cog"></i> Admin Panel</h4>
                    <p class="mb-0">Welcome, <?php echo $_SESSION['admin_username']; ?></p>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="icon-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="personal_info.php">
                        <i class="icon-user"></i> Personal Info
                    </a>
                    <a class="nav-link" href="education.php">
                        <i class="icon-graduation-cap"></i> Education
                    </a>
                    <a class="nav-link" href="skills.php">
                        <i class="icon-code"></i> Skills
                    </a>
                    <a class="nav-link" href="achievements.php">
                        <i class="icon-trophy"></i> Achievements
                    </a>
                    <a class="nav-link" href="projects.php">
                        <i class="icon-code-branch"></i> Projects
                    </a>
                    <a class="nav-link" href="social_links.php">
                        <i class="icon-share-alt"></i> Social Links
                    </a>
                    <a class="nav-link" href="certificates.php">
                        <i class="icon-certificate"></i> Certificates
                    </a>
                    <a class="nav-link" href="experience.php">
                        <i class="icon-briefcase"></i> Experience
                    </a>
                    <a class="nav-link" href="gallery.php">
                        <i class="icon-images"></i> Gallery
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="icon-eye"></i> View Portfolio
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="icon-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="icon-tachometer-alt"></i> Dashboard</h2>
                    <div class="text-muted">
                        <i class="icon-calendar"></i> <?php echo date('F j, Y'); ?>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-primary">
                            <i class="icon-graduation-cap" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['education']; ?></h3>
                            <p>Education</p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-success">
                            <i class="icon-code" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['skills']; ?></h3>
                            <p>Skills</p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-warning">
                            <i class="icon-trophy" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['achievements']; ?></h3>
                            <p>Achievements</p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-info">
                            <i class="icon-code-branch" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['projects']; ?></h3>
                            <p>Projects</p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-secondary">
                            <i class="icon-share-alt" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['social_links']; ?></h3>
                            <p>Social Links</p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="stat-card bg-dark">
                            <i class="icon-certificate" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                            <h3><?php echo $stats['certificates']; ?></h3>
                            <p>Certificates</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="icon-plus"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="education.php" class="btn btn-primary w-100" style="padding: 1.5rem;">
                                            <i class="icon-graduation-cap" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Education
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="skills.php" class="btn btn-success w-100" style="padding: 1.5rem;">
                                            <i class="icon-code" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Skill
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="projects.php" class="btn btn-info w-100" style="padding: 1.5rem;">
                                            <i class="icon-code-branch" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Project
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="achievements.php" class="btn btn-warning w-100" style="padding: 1.5rem;">
                                            <i class="icon-trophy" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Achievement
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="certificates.php" class="btn btn-secondary w-100" style="padding: 1.5rem;">
                                            <i class="icon-certificate" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Certificate
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="experience.php" class="btn btn-secondary w-100" style="padding: 1.5rem;">
                                            <i class="icon-briefcase" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Experience
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="social_links.php" class="btn btn-primary w-100" style="padding: 1.5rem;">
                                            <i class="icon-share-alt" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Social Links
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="gallery.php" class="btn btn-success w-100" style="padding: 1.5rem;">
                                            <i class="icon-images" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Add Gallery
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="icon-calendar"></i> Recent Activities</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_activities)): ?>
                                    <p class="text-muted text-center">No recent activities</p>
                                <?php else: ?>
                                    <div style="list-style: none; padding: 0;">
                                        <?php foreach ($recent_activities as $activity): ?>
                                            <div style="border-bottom: 1px solid #dee2e6; padding: 0.75rem 0;">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php
                                                        $icons = [
                                                            'education' => 'icon-graduation-cap text-primary',
                                                            'achievement' => 'icon-trophy text-warning',
                                                            'project' => 'icon-code-branch text-info'
                                                        ];
                                                        ?>
                                                        <i class="<?php echo $icons[$activity['type']] ?? 'icon-star'; ?>"></i>
                                                    </div>
                                                    <div style="flex-grow: 1;">
                                                        <h6 style="margin-bottom: 0.25rem; font-weight: 600;"><?php echo htmlspecialchars(substr($activity['title'], 0, 30)); ?></h6>
                                                        <small class="text-muted">
                                                            <?php echo ucfirst($activity['type']); ?> •
                                                            <?php echo date('M j', strtotime($activity['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Management Overview -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="icon-cog"></i> Portfolio Management</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-user text-primary"></i> Personal Information</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Manage your name, bio, contact details, and profile image.</p>
                                            <a href="personal_info.php" class="btn btn-sm btn-primary">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-graduation-cap text-success"></i> Education & Qualifications</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Add your academic background, degrees, and certifications.</p>
                                            <a href="education.php" class="btn btn-sm btn-success">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-code text-info"></i> Skills & Technologies</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Organize your technical skills by categories with proficiency levels.</p>
                                            <a href="skills.php" class="btn btn-sm btn-info">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-trophy text-warning"></i> Achievements & Awards</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Showcase your competitions, awards, and recognitions.</p>
                                            <a href="achievements.php" class="btn btn-sm btn-warning">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div style="border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem;">
                                            <h6><i class="icon-code-branch text-secondary"></i> Projects Portfolio</h6>
                                            <p class="text-muted" style="margin-bottom: 0.5rem;">Display your projects with descriptions, technologies, and links.</p>
                                            <a href="projects.php" class="btn btn-sm btn-secondary">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="icon-share-alt text-dark"></i> Social Media Links</h6>
                                            <p class="text-muted mb-2">Connect your social profiles and professional networks.</p>
                                            <a href="social_links.php" class="btn btn-sm btn-dark">Manage</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/assets/css/admin.css/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
