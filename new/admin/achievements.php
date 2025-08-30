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
                $stmt = $conn->prepare("INSERT INTO achievements (title, category, description, date_achieved, position, organization, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['date_achieved'] ?: null,
                    $_POST['position'],
                    $_POST['organization'],
                    $_POST['image']
                ]);
                $success = "Achievement added successfully!";
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE achievements SET title=?, category=?, description=?, date_achieved=?, position=?, organization=?, image=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['date_achieved'] ?: null,
                    $_POST['position'],
                    $_POST['organization'],
                    $_POST['image'],
                    $_POST['id']
                ]);
                $success = "Achievement updated successfully!";
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM achievements WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Achievement deleted successfully!";
                break;
        }
    }
}

// Get all achievements
$stmt = $conn->prepare("SELECT * FROM achievements ORDER BY date_achieved DESC");
$stmt->execute();
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single record for editing
$edit_record = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM achievements WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/assets/css/admin.css/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link active" href="achievements.php">
                        <i class="icon-trophy"></i> Achievements
                    </a>
                    <a class="nav-link" href="projects.php">
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
                    <h2><i class="icon-trophy"></i> Achievements Management</h2>
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
                            <?php echo $edit_record ? 'Edit Achievement' : 'Add New Achievement'; ?>
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
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?php echo $edit_record ? $edit_record['title'] : ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="Competition" <?php echo ($edit_record && $edit_record['category'] == 'Competition') ? 'selected' : ''; ?>>Competition</option>
                                        <option value="Certification" <?php echo ($edit_record && $edit_record['category'] == 'Certification') ? 'selected' : ''; ?>>Certification</option>
                                        <option value="Award" <?php echo ($edit_record && $edit_record['category'] == 'Award') ? 'selected' : ''; ?>>Award</option>
                                        <option value="Achievement" <?php echo ($edit_record && $edit_record['category'] == 'Achievement') ? 'selected' : ''; ?>>Achievement</option>
                                        <option value="Recognition" <?php echo ($edit_record && $edit_record['category'] == 'Recognition') ? 'selected' : ''; ?>>Recognition</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="organization" class="form-label">Organization/Institution</label>
                                    <input type="text" class="form-control" id="organization" name="organization"
                                        value="<?php echo $edit_record ? $edit_record['organization'] : ''; ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="position" class="form-label">Position/Rank</label>
                                    <input type="text" class="form-control" id="position" name="position"
                                        value="<?php echo $edit_record ? $edit_record['position'] : ''; ?>"
                                        placeholder="e.g., 1st Place, Top 10">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="date_achieved" class="form-label">Date Achieved</label>
                                    <input type="date" class="form-control" id="date_achieved" name="date_achieved"
                                        value="<?php echo $edit_record ? $edit_record['date_achieved'] : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $edit_record ? $edit_record['description'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image URL (Optional)</label>
                                <input type="url" class="form-control" id="image" name="image"
                                    value="<?php echo $edit_record ? $edit_record['image'] : ''; ?>"
                                    placeholder="https://example.com/image.jpg">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-save"></i>
                                    <?php echo $edit_record ? 'Update' : 'Add'; ?> Achievement
                                </button>
                                <?php if ($edit_record): ?>
                                    <a href="achievements.php" class="btn btn-secondary">
                                        <i class="icon-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Achievements List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="icon-list"></i> All Achievements</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($achievements)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="icon-trophy fa-3x mb-3"></i>
                                <p>No achievements found. Add your first achievement above.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($achievements as $achievement): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($achievement['title']); ?></h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="icon-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="?edit=<?php echo $achievement['id']; ?>">
                                                                    <i class="icon-edit"></i> Edit
                                                                </a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form method="POST" style="display: inline;"
                                                                    onsubmit="return confirm('Are you sure you want to delete this achievement?')">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $achievement['id']; ?>">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="icon-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <?php if ($achievement['category']): ?>
                                                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($achievement['category']); ?></span>
                                                <?php endif; ?>

                                                <?php if ($achievement['organization']): ?>
                                                    <p class="card-text mb-1">
                                                        <strong>Organization:</strong> <?php echo htmlspecialchars($achievement['organization']); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($achievement['position']): ?>
                                                    <p class="card-text mb-1">
                                                        <strong>Position:</strong> <?php echo htmlspecialchars($achievement['position']); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($achievement['date_achieved']): ?>
                                                    <p class="card-text mb-2">
                                                        <small class="text-muted">
                                                            <i class="icon-calendar"></i>
                                                            <?php echo date('F j, Y', strtotime($achievement['date_achieved'])); ?>
                                                        </small>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($achievement['description']): ?>
                                                    <p class="card-text">
                                                        <?php echo htmlspecialchars(substr($achievement['description'], 0, 100)); ?>
                                                        <?php if (strlen($achievement['description']) > 100): ?>...<?php endif; ?>
                                                    </p>
                                                <?php endif; ?>
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
