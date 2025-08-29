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
                $stmt = $conn->prepare("INSERT INTO certificates (title, issuer, issue_date, expiry_date, credential_id, credential_url, image, skills_gained, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['issuer'],
                    $_POST['issue_date'] ?: null,
                    $_POST['expiry_date'] ?: null,
                    $_POST['credential_id'],
                    $_POST['credential_url'],
                    $_POST['image'],
                    $_POST['skills_gained'],
                    isset($_POST['is_active']) ? 1 : 0
                ]);
                $success = "Certificate added successfully!";
                break;

            case 'update':
                $stmt = $conn->prepare("UPDATE certificates SET title=?, issuer=?, issue_date=?, expiry_date=?, credential_id=?, credential_url=?, image=?, skills_gained=?, is_active=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['issuer'],
                    $_POST['issue_date'] ?: null,
                    $_POST['expiry_date'] ?: null,
                    $_POST['credential_id'],
                    $_POST['credential_url'],
                    $_POST['image'],
                    $_POST['skills_gained'],
                    isset($_POST['is_active']) ? 1 : 0,
                    $_POST['id']
                ]);
                $success = "Certificate updated successfully!";
                break;

            case 'delete':
                $stmt = $conn->prepare("DELETE FROM certificates WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Certificate deleted successfully!";
                break;
        }
    }
}

// Get all certificates
$stmt = $conn->prepare("SELECT * FROM certificates ORDER BY issue_date DESC");
$stmt->execute();
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single record for editing
$edit_record = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM certificates WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_record = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates Management - Admin Panel</title>
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
                    <a class="nav-link" href="social_links.php">
                        <i class="fas fa-share-alt"></i> Social Links
                    </a>
                    <a class="nav-link active" href="certificates.php">
                        <i class="fas fa-certificate"></i> Certificates
                    </a>
                    <a class="nav-link" href="experience.php">
                        <i class="fas fa-briefcase"></i> Experience
                    </a>
                    <a class="nav-link" href="gallery.php">
                        <i class="fas fa-images"></i> Gallery
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
                    <h2><i class="fas fa-certificate"></i> Certificates Management</h2>
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

                <!-- Add/Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-plus"></i>
                            <?php echo $edit_record ? 'Edit Certificate' : 'Add New Certificate'; ?>
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
                                    <label for="title" class="form-label">Certificate Title *</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="<?php echo $edit_record ? $edit_record['title'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="issuer" class="form-label">Issuing Organization</label>
                                    <input type="text" class="form-control" id="issuer" name="issuer"
                                        value="<?php echo $edit_record ? $edit_record['issuer'] : ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="issue_date" class="form-label">Issue Date</label>
                                    <input type="date" class="form-control" id="issue_date" name="issue_date"
                                        value="<?php echo $edit_record ? $edit_record['issue_date'] : ''; ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" id="expiry_date" name="expiry_date"
                                        value="<?php echo $edit_record ? $edit_record['expiry_date'] : ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="credential_id" class="form-label">Credential ID</label>
                                    <input type="text" class="form-control" id="credential_id" name="credential_id"
                                        value="<?php echo $edit_record ? $edit_record['credential_id'] : ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="credential_url" class="form-label">Credential URL</label>
                                    <input type="url" class="form-control" id="credential_url" name="credential_url"
                                        value="<?php echo $edit_record ? $edit_record['credential_url'] : ''; ?>"
                                        placeholder="https://example.com/certificate">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">Certificate Image URL</label>
                                    <input type="url" class="form-control" id="image" name="image"
                                        value="<?php echo $edit_record ? $edit_record['image'] : ''; ?>"
                                        placeholder="https://example.com/certificate.jpg">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="skills_gained" class="form-label">Skills Gained</label>
                                <textarea class="form-control" id="skills_gained" name="skills_gained" rows="3"
                                    placeholder="List the skills or knowledge gained from this certification..."><?php echo $edit_record ? $edit_record['skills_gained'] : ''; ?></textarea>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    <?php echo (!$edit_record || $edit_record['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (Show on portfolio)
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $edit_record ? 'Update' : 'Add'; ?> Certificate
                                </button>
                                <?php if ($edit_record): ?>
                                    <a href="certificates.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Certificates List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> All Certificates</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($certificates)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-certificate fa-3x mb-3"></i>
                                <p>No certificates found. Add your first certificate above.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($certificates as $cert): ?>
                                    <div class="col-lg-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?php echo htmlspecialchars($cert['title']); ?></h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="?edit=<?php echo $cert['id']; ?>">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <form method="POST" style="display: inline;"
                                                                    onsubmit="return confirm('Are you sure you want to delete this certificate?')">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $cert['id']; ?>">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <?php if ($cert['issuer']): ?>
                                                    <p class="card-text mb-1">
                                                        <strong>Issuer:</strong> <?php echo htmlspecialchars($cert['issuer']); ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($cert['issue_date']): ?>
                                                    <p class="card-text mb-1">
                                                        <strong>Issued:</strong> <?php echo date('F j, Y', strtotime($cert['issue_date'])); ?>
                                                        <?php if ($cert['expiry_date']): ?>
                                                            <br><strong>Expires:</strong> <?php echo date('F j, Y', strtotime($cert['expiry_date'])); ?>
                                                        <?php endif; ?>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($cert['credential_id']): ?>
                                                    <p class="card-text mb-2">
                                                        <strong>ID:</strong> <code><?php echo htmlspecialchars($cert['credential_id']); ?></code>
                                                    </p>
                                                <?php endif; ?>

                                                <?php if ($cert['skills_gained']): ?>
                                                    <p class="card-text mb-2">
                                                        <strong>Skills:</strong> <?php echo htmlspecialchars(substr($cert['skills_gained'], 0, 100)); ?>
                                                        <?php if (strlen($cert['skills_gained']) > 100): ?>...<?php endif; ?>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <?php if ($cert['is_active']): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <?php if ($cert['credential_url']): ?>
                                                            <a href="<?php echo $cert['credential_url']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-external-link-alt"></i> View
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>