<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $conn->prepare("INSERT INTO projects (title, category, description, technologies, github_url, live_url, image, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['technologies'],
                    $_POST['github_url'],
                    $_POST['live_url'],
                    $_POST['image'],
                    $_POST['status'],
                    isset($_POST['featured']) ? 1 : 0
                ]);
                $success = "Project added successfully!";
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE projects SET title=?, category=?, description=?, technologies=?, github_url=?, live_url=?, image=?, status=?, featured=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['technologies'],
                    $_POST['github_url'],
                    $_POST['live_url'],
                    $_POST['image'],
                    $_POST['status'],
                    isset($_POST['featured']) ? 1 : 0,
                    $_POST['id']
                ]);
                $success = "Project updated successfully!";
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM projects WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Project deleted successfully!";
                break;
        }
    }
}

// Get all projects
$stmt = $conn->prepare("SELECT * FROM projects ORDER BY featured DESC, created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single record for editing
$edit_record = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management - Admin Panel</title>
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center text-white mb-4">
                    <h4><i class="icon-user-cog"></i> Admin Panel</h4>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
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
                    <a class="nav-link active" href="projects.php">
                        <i class="icon-project-diagram"></i> Projects
                    </a>
                    <a class="nav-link" href="social_links.php">
                        <i class="icon-share-alt"></i> Social Links
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php">
                        <i class="icon-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="icon-project-diagram"></i> Projects Management</h2>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="icon-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="icon-check"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="icon-plus"></i>
                            <?php echo $edit_record ? 'Edit Project' : 'Add New Project'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $edit_record ? 'update' : 'add'; ?>">
                            <?php if ($edit_record): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_record['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Project Title *</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?php echo $edit_record ? $edit_record['title'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="Web Development" <?php echo ($edit_record && $edit_record['category'] == 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                                        <option value="Mobile App" <?php echo ($edit_record && $edit_record['category'] == 'Mobile App') ? 'selected' : ''; ?>>Mobile App</option>
                                        <option value="Desktop App" <?php echo ($edit_record && $edit_record['category'] == 'Desktop App') ? 'selected' : ''; ?>>Desktop App</option>
                                        <option value="Machine Learning" <?php echo ($edit_record && $edit_record['category'] == 'Machine Learning') ? 'selected' : ''; ?>>Machine Learning</option>
                                        <option value="Game Development" <?php echo ($edit_record && $edit_record['category'] == 'Game Development') ? 'selected' : ''; ?>>Game Development</option>
                                        <option value="Data Science" <?php echo ($edit_record && $edit_record['category'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                        <option value="API Development" <?php echo ($edit_record && $edit_record['category'] == 'API Development') ? 'selected' : ''; ?>>API Development</option>
                                        <option value="Other" <?php echo ($edit_record && $edit_record['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $edit_record ? $edit_record['description'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="technologies" class="form-label">Technologies Used</label>
                                <input type="text" class="form-control" id="technologies" name="technologies"
                                    value="<?php echo $edit_record ? $edit_record['technologies'] : ''; ?>"
                                    placeholder="e.g., PHP, JavaScript, MySQL, Bootstrap">
                                <div class="form-text">Separate multiple technologies with commas</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="github_url" class="form-label">GitHub URL</label>
                                    <input type="url" class="form-control" id="github_url" name="github_url"
                                        value="<?php echo $edit_record ? $edit_record['github_url'] : ''; ?>"
                                        placeholder="https://github.com/username/repo">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="live_url" class="form-label">Live Demo URL</label>
                                    <input type="url" class="form-control" id="live_url" name="live_url"
                                        value="<?php echo $edit_record ? $edit_record['live_url'] : ''; ?>"
                                        placeholder="https://example.com">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">Project Image URL</label>
                                    <input type="url" class="form-control" id="image" name="image"
                                        value="<?php echo $edit_record ? $edit_record['image'] : ''; ?>"
                                        placeholder="https://example.com/image.jpg">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Project Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="completed" <?php echo ($edit_record && $edit_record['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="in_progress" <?php echo ($edit_record && $edit_record['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="planning" <?php echo ($edit_record && $edit_record['status'] == 'planning') ? 'selected' : ''; ?>>Planning</option>
                                        <option value="on_hold" <?php echo ($edit_record && $edit_record['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured"
                                    <?php echo ($edit_record && $edit_record['featured']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">
                                    Featured Project (Show at top)
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-save"></i>
                                    <?php echo $edit_record ? 'Update' : 'Add'; ?> Project
                                </button>
                                <?php if ($edit_record): ?>
                                    <a href="projects.php" class="btn btn-secondary">
                                        <i class="icon-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="icon-list"></i> All Projects</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($projects)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="icon-project-diagram fa-3x mb-3"></i>
                                <p>No projects found. Add your first project above.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($projects as $project): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card h-100">
                                            <?php if ($project['featured']): ?>
                                                <div class="badge bg-warning position-absolute" style="top: 1rem; right: 1rem; z-index: 10;">
                                                    <i class="icon-star"></i> Featured
                                                </div>
                                            <?php endif; ?>

                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($project['title']); ?></h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="icon-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="?edit=<?php echo $project['id']; ?>">
                                                                    <i class="icon-edit"></i> Edit
                                                                </a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form method="POST" style="display: inline;"
                                                                    onsubmit="return confirm('Are you sure you want to delete this project?')">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="icon-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <?php if ($project['category']): ?>
                                                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($project['category']); ?></span>
                                                    <?php endif; ?>

                                                    <?php
                                                    $status_colors = [
                                                        'completed' => 'success',
                                                        'in_progress' => 'warning',
                                                        'planning' => 'info',
                                                        'on_hold' => 'secondary'
                                                    ];
                                                    $status_color = $status_colors[$project['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?php echo $status_color; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                                    </span>
                                                </div>

                                                <?php if ($project['description']): ?>
                                                    <p class="card-text flex-grow-1">
                                                        <?php echo htmlspecialchars(substr($project['description'], 0, 120)); ?>
                                                        <?php if (strlen($project['description']) > 120): ?>...<?php endif; ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($project['technologies']): ?>
                                                    <p class="card-text">
                                                        <strong>Technologies:</strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($project['technologies']); ?></small>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="mt-auto">
                                                    <div class="d-flex gap-2">
                                                        <?php if ($project['github_url']): ?>
                                                            <a href="<?php echo $project['github_url']; ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                                                                <i class="fab fa-github"></i> Code
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($project['live_url']): ?>
                                                            <a href="<?php echo $project['live_url']; ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                                <i class="icon-external-link-alt"></i> Demo
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/assets/css/admin.css/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
