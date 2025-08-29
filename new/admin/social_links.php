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
                $stmt = $conn->prepare("INSERT INTO social_links (platform, url, icon, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['platform'],
                    $_POST['url'],
                    $_POST['icon'],
                    isset($_POST['is_active']) ? 1 : 0
                ]);
                $success = "Social link added successfully!";
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE social_links SET platform=?, url=?, icon=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['platform'],
                    $_POST['url'],
                    $_POST['icon'],
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['id']
                ]);
                $success = "Social link updated successfully!";
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM social_links WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Social link deleted successfully!";
                break;
        }
    }
}

// Get all social links
$stmt = $conn->prepare("SELECT * FROM social_links ORDER BY created_at DESC");
$stmt->execute();
$social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single record for editing
$edit_record = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM social_links WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Common social platforms with their icons
$common_platforms = [
    'GitHub' => 'fab fa-github',
    'LinkedIn' => 'fab fa-linkedin',
    'Twitter' => 'fab fa-twitter',
    'Facebook' => 'fab fa-facebook',
    'Instagram' => 'fab fa-instagram',
    'YouTube' => 'fab fa-youtube',
    'Kaggle' => 'fab fa-kaggle',
    'Stack Overflow' => 'fab fa-stack-overflow',
    'Medium' => 'fab fa-medium',
    'Dev.to' => 'fab fa-dev',
    'Behance' => 'fab fa-behance',
    'Dribbble' => 'fab fa-dribbble',
    'CodePen' => 'fab fa-codepen',
    'Website' => 'fas fa-globe',
    'Email' => 'fas fa-envelope'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Links Management - Admin Panel</title>
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

        .social-preview {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .social-preview:hover {
            background: #e9ecef;
            color: #333;
            transform: translateY(-2px);
        }

        .platform-selector {
            cursor: pointer;
            transition: all 0.3s;
        }

        .platform-selector:hover {
            background: #e9ecef;
        }

        .platform-selector.selected {
            background: #007bff;
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
                    <h4><i class="fas fa-user-cog"></i> Admin Panel</h4>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
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
                    <a class="nav-link active" href="social_links.php">
                        <i class="fas fa-share-alt"></i> Social Links
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-share-alt"></i> Social Links Management</h2>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Platform Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-mouse-pointer"></i> Quick Platform Selection</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <?php foreach ($common_platforms as $platform => $icon): ?>
                                <div class="col-md-3">
                                    <div class="platform-selector p-2 border rounded text-center"
                                        onclick="selectPlatform('<?php echo $platform; ?>', '<?php echo $icon; ?>')">
                                        <i class="<?php echo $icon; ?> fa-lg mb-1"></i><br>
                                        <small><?php echo $platform; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-plus"></i>
                            <?php echo $edit_record ? 'Edit Social Link' : 'Add New Social Link'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="socialForm">
                            <input type="hidden" name="action" value="<?php echo $edit_record ? 'update' : 'add'; ?>">
                            <?php if ($edit_record): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_record['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="platform" class="form-label">Platform Name</label>
                                    <input type="text" class="form-control" id="platform" name="platform"
                                        value="<?php echo $edit_record ? $edit_record['platform'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="url" class="form-label">URL</label>
                                    <input type="url" class="form-control" id="url" name="url"
                                        value="<?php echo $edit_record ? $edit_record['url'] : ''; ?>"
                                        placeholder="https://example.com" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="icon" class="form-label">Icon Class</label>
                                    <input type="text" class="form-control" id="icon" name="icon"
                                        value="<?php echo $edit_record ? $edit_record['icon'] : ''; ?>"
                                        placeholder="fab fa-github">
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    <?php echo (!$edit_record || $edit_record['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (Show on portfolio)
                                </label>
                            </div>

                            <!-- Preview -->
                            <div class="mb-3">
                                <label class="form-label">Preview:</label>
                                <div id="socialPreview" class="social-preview">
                                    <i id="previewIcon" class="<?php echo $edit_record ? $edit_record['icon'] : 'fas fa-link'; ?>"></i>
                                    <span id="previewText"><?php echo $edit_record ? $edit_record['platform'] : 'Platform Name'; ?></span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $edit_record ? 'Update' : 'Add'; ?> Social Link
                                </button>
                                <?php if ($edit_record): ?>
                                    <a href="social_links.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Social Links List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Current Social Links</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($social_links)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-share-alt fa-3x mb-3"></i>
                                <p>No social links found. Add your first social link above.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($social_links as $link): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($link['icon']): ?>
                                                            <i class="<?php echo $link['icon']; ?> fa-2x me-3"></i>
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($link['platform']); ?></h6>
                                                            <small class="text-muted"><?php echo htmlspecialchars($link['url']); ?></small>
                                                            <br>
                                                            <?php if ($link['is_active']): ?>
                                                                <span class="badge bg-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Inactive</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        <a href="<?php echo $link['url']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        <a href="?edit=<?php echo $link['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;"
                                                            onsubmit="return confirm('Are you sure you want to delete this social link?')">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo $link['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPlatform(platform, icon) {
            document.getElementById('platform').value = platform;
            document.getElementById('icon').value = icon;
            updatePreview();

            // Visual feedback
            document.querySelectorAll('.platform-selector').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        function updatePreview() {
            const platform = document.getElementById('platform').value || 'Platform Name';
            const icon = document.getElementById('icon').value || 'fas fa-link';

            document.getElementById('previewText').textContent = platform;
            document.getElementById('previewIcon').className = icon;
        }

        // Update preview on input change
        document.getElementById('platform').addEventListener('input', updatePreview);
        document.getElementById('icon').addEventListener('input', updatePreview);

        // Initialize preview
        updatePreview();
    </script>
</body>

</html>