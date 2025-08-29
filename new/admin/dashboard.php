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
$tables = ['education', 'skills', 'achievements', 'projects', 'social_links', 'certificates', 'experience', 'gallery'];

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                    <h4><i class="fas fa-user-cog"></i> Admin Panel</h4>
                    <p class="mb-0">Welcome, <?php echo $_SESSION['admin_username']; ?></p>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="personal_info.php">
                        <i class="fas fa-user"></i> Personal Info
                    </a>
                    <a class="nav-link" href="education.php">
                        <i class="fas fa-graduation-cap"></i> Education
                    </a>
                    <a class="nav-link" href="skills.php">
                        <i class="fas fa-code"></i> Skills
                    </a>
                    <a class="nav-link" href="achievements.php">
                        <i class="fas fa-trophy"></i> Achievements
                    </a>
                    <a class="nav-link" href="projects.php">
                        <i class="fas fa-project-diagram"></i> Projects
                    </a>
                    <a class="nav-link" href="social_links.php">
                        <i class="fas fa-share-alt"></i> Social Links
                    </a>
                    <a class="nav-link" href="certificates.php">
                        <i class="fas fa-certificate"></i> Certificates
                    </a>
                    <a class="nav-link" href="experience.php">
                        <i class="fas fa-briefcase"></i> Experience
                    </a>
                    <a class="nav-link" href="gallery.php">
                        <i class="fas fa-images"></i> Gallery
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-eye"></i> View Portfolio
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                    <div class="text-muted">
                        <i class="fas fa-calendar"></i> <?php echo date('F j, Y'); ?>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                                <h3><?php echo $stats['education']; ?></h3>
                                <p class="mb-0">Education</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-code fa-2x mb-2"></i>
                                <h3><?php echo $stats['skills']; ?></h3>
                                <p class="mb-0">Skills</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-trophy fa-2x mb-2"></i>
                                <h3><?php echo $stats['achievements']; ?></h3>
                                <p class="mb-0">Achievements</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-project-diagram fa-2x mb-2"></i>
                                <h3><?php echo $stats['projects']; ?></h3>
                                <p class="mb-0">Projects</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-secondary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-share-alt fa-2x mb-2"></i>
                                <h3><?php echo $stats['social_links']; ?></h3>
                                <p class="mb-0">Social Links</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <div class="card stat-card bg-dark text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-certificate fa-2x mb-2"></i>
                                <h3><?php echo $stats['certificates']; ?></h3>
                                <p class="mb-0">Certificates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="education.php" class="btn btn-outline-primary w-100 p-3">
                                            <i class="fas fa-graduation-cap fa-2x mb-2"></i><br>
                                            Add Education
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="skills.php" class="btn btn-outline-success w-100 p-3">
                                            <i class="fas fa-code fa-2x mb-2"></i><br>
                                            Add Skill
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="projects.php" class="btn btn-outline-info w-100 p-3">
                                            <i class="fas fa-project-diagram fa-2x mb-2"></i><br>
                                            Add Project
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="achievements.php" class="btn btn-outline-warning w-100 p-3">
                                            <i class="fas fa-trophy fa-2x mb-2"></i><br>
                                            Add Achievement
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="certificates.php" class="btn btn-outline-dark w-100 p-3">
                                            <i class="fas fa-certificate fa-2x mb-2"></i><br>
                                            Add Certificate
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="experience.php" class="btn btn-outline-secondary w-100 p-3">
                                            <i class="fas fa-briefcase fa-2x mb-2"></i><br>
                                            Add Experience
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="social_links.php" class="btn btn-outline-primary w-100 p-3">
                                            <i class="fas fa-share-alt fa-2x mb-2"></i><br>
                                            Social Links
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <a href="gallery.php" class="btn btn-outline-success w-100 p-3">
                                            <i class="fas fa-images fa-2x mb-2"></i><br>
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
                                <h5><i class="fas fa-clock"></i> Recent Activities</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_activities)): ?>
                                    <p class="text-muted text-center">No recent activities</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recent_activities as $activity): ?>
                                            <div class="list-group-item border-0 px-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php
                                                        $icons = [
                                                            'education' => 'fas fa-graduation-cap text-primary',
                                                            'achievement' => 'fas fa-trophy text-warning',
                                                            'project' => 'fas fa-project-diagram text-info'
                                                        ];
                                                        ?>
                                                        <i class="<?php echo $icons[$activity['type']] ?? 'fas fa-circle'; ?>"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars(substr($activity['title'], 0, 30)); ?></h6>
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
                                <h5><i class="fas fa-cogs"></i> Portfolio Management</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-user text-primary"></i> Personal Information</h6>
                                            <p class="text-muted mb-2">Manage your name, bio, contact details, and profile image.</p>
                                            <a href="personal_info.php" class="btn btn-sm btn-primary">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-graduation-cap text-success"></i> Education & Qualifications</h6>
                                            <p class="text-muted mb-2">Add your academic background, degrees, and certifications.</p>
                                            <a href="education.php" class="btn btn-sm btn-success">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-code text-info"></i> Skills & Technologies</h6>
                                            <p class="text-muted mb-2">Organize your technical skills by categories with proficiency levels.</p>
                                            <a href="skills.php" class="btn btn-sm btn-info">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-trophy text-warning"></i> Achievements & Awards</h6>
                                            <p class="text-muted mb-2">Showcase your competitions, awards, and recognitions.</p>
                                            <a href="achievements.php" class="btn btn-sm btn-warning">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-project-diagram text-secondary"></i> Projects Portfolio</h6>
                                            <p class="text-muted mb-2">Display your projects with descriptions, technologies, and links.</p>
                                            <a href="projects.php" class="btn btn-sm btn-secondary">Manage</a>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <h6><i class="fas fa-share-alt text-dark"></i> Social Media Links</h6>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>