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
                $stmt = $conn->prepare("INSERT INTO education (degree, institution, duration, gpa, description, year_start, year_end, is_current) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['degree'],
                    $_POST['institution'],
                    $_POST['duration'],
                    $_POST['gpa'],
                    $_POST['description'],
                    $_POST['year_start'],
                    $_POST['year_end'],
                    isset($_POST['is_current']) ? 1 : 0
                ]);
                $success = "Education record added successfully!";
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE education SET degree=?, institution=?, duration=?, gpa=?, description=?, year_start=?, year_end=?, is_current=? WHERE id=?");
                $stmt->execute([
                    $_POST['degree'],
                    $_POST['institution'],
                    $_POST['duration'],
                    $_POST['gpa'],
                    $_POST['description'],
                    $_POST['year_start'],
                    $_POST['year_end'],
                    isset($_POST['is_current']) ? 1 : 0,
                    $_POST['id']
                ]);
                $success = "Education record updated successfully!";
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM education WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Education record deleted successfully!";
                break;
        }
    }
}

// Get all education records
$stmt = $conn->prepare("SELECT * FROM education ORDER BY year_start DESC");
$stmt->execute();
$education_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single record for editing
$edit_record = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM education WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Management - Admin Panel</title>
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
                    <a class="nav-link active" href="education.php">
                        <i class="icon-graduation-cap"></i> Education
                    </a>
                    <a class="nav-link" href="skills.php">
                        <i class="icon-code"></i> Skills
                    </a>
                    <a class="nav-link" href="achievements.php">
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
                    <h2><i class="icon-graduation-cap"></i> Education Management</h2>
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
                            <?php echo $edit_record ? 'Edit Education Record' : 'Add New Education Record'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $edit_record ? 'update' : 'add'; ?>">
                            <?php if ($edit_record): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_record['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="degree" class="form-label">Degree/Certificate</label>
                                    <input type="text" class="form-control" id="degree" name="degree"
                                        value="<?php echo $edit_record ? $edit_record['degree'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="institution" class="form-label">Institution</label>
                                    <input type="text" class="form-control" id="institution" name="institution"
                                        value="<?php echo $edit_record ? $edit_record['institution'] : ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="year_start" class="form-label">Start Year</label>
                                    <input type="number" class="form-control" id="year_start" name="year_start"
                                        value="<?php echo $edit_record ? $edit_record['year_start'] : ''; ?>" min="1990" max="2030">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="year_end" class="form-label">End Year</label>
                                    <input type="number" class="form-control" id="year_end" name="year_end"
                                        value="<?php echo $edit_record ? $edit_record['year_end'] : ''; ?>" min="1990" max="2030">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="gpa" class="form-label">GPA/Grade</label>
                                    <input type="text" class="form-control" id="gpa" name="gpa"
                                        value="<?php echo $edit_record ? $edit_record['gpa'] : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    value="<?php echo $edit_record ? $edit_record['duration'] : ''; ?>"
                                    placeholder="e.g., 2023 - Present">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_record ? $edit_record['description'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_current" name="is_current"
                                    <?php echo ($edit_record && $edit_record['is_current']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_current">
                                    Currently studying here
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-save"></i>
                                    <?php echo $edit_record ? 'Update' : 'Add'; ?> Education
                                </button>
                                <?php if ($edit_record): ?>
                                    <a href="education.php" class="btn btn-secondary">
                                        <i class="icon-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Education Records List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="icon-list"></i> Education Records</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($education_records)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="icon-graduation-cap fa-3x mb-3"></i>
                                <p>No education records found. Add your first education record above.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Degree</th>
                                            <th>Institution</th>
                                            <th>Duration</th>
                                            <th>GPA</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($education_records as $record): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['degree']); ?></td>
                                                <td><?php echo htmlspecialchars($record['institution']); ?></td>
                                                <td><?php echo htmlspecialchars($record['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($record['gpa']); ?></td>
                                                <td>
                                                    <?php if ($record['is_current']): ?>
                                                        <span class="badge bg-success">Current</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Completed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="icon-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this education record?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
