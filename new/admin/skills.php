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
            case 'add_category':
                $stmt = $conn->prepare("INSERT INTO skill_categories (category_name, icon, sort_order) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['category_name'], $_POST['icon'], $_POST['sort_order']]);
                $success = "Skill category added successfully!";
                break;

            case 'add_skill':
                $stmt = $conn->prepare("INSERT INTO skills (category_id, skill_name, proficiency_level, icon) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['category_id'], $_POST['skill_name'], $_POST['proficiency_level'], $_POST['icon']]);
                $success = "Skill added successfully!";
                break;

            case 'update_skill':
                $stmt = $conn->prepare("UPDATE skills SET category_id=?, skill_name=?, proficiency_level=?, icon=? WHERE id=?");
                $stmt->execute([$_POST['category_id'], $_POST['skill_name'], $_POST['proficiency_level'], $_POST['icon'], $_POST['id']]);
                $success = "Skill updated successfully!";
                break;

            case 'delete_skill':
                $stmt = $conn->prepare("DELETE FROM skills WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Skill deleted successfully!";
                break;

            case 'delete_category':
                $stmt = $conn->prepare("DELETE FROM skill_categories WHERE id=?");
                $stmt->execute([$_POST['id']]);
                $success = "Category deleted successfully!";
                break;
        }
    }
}

// Insert default categories if none exist
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM skill_categories");
$stmt->execute();
$category_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($category_count == 0) {
    $default_categories = [
        ['Programming Languages', 'fas fa-code', 1],
        ['Frontend Development', 'fas fa-paint-brush', 2],
        ['Backend Development', 'fas fa-server', 3],
        ['Mobile App Development', 'fas fa-mobile-alt', 4],
        ['Databases', 'fas fa-database', 5],
        ['Machine Learning & AI', 'fas fa-brain', 6],
        ['Tools & Platforms', 'fas fa-tools', 7]
    ];

    foreach ($default_categories as $category) {
        $stmt = $conn->prepare("INSERT INTO skill_categories (category_name, icon, sort_order) VALUES (?, ?, ?)");
        $stmt->execute($category);
    }
}

// Get all categories
$stmt = $conn->prepare("SELECT * FROM skill_categories ORDER BY sort_order");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all skills with categories
$stmt = $conn->prepare("SELECT s.*, sc.category_name FROM skills s LEFT JOIN skill_categories sc ON s.category_id = sc.id ORDER BY sc.sort_order, s.skill_name");
$stmt->execute();
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single skill for editing
$edit_skill = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM skills WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_skill = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills Management - Admin Panel</title>
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

        .skill-progress {
            height: 8px;
            border-radius: 4px;
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
                    <a class="nav-link active" href="skills.php">
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
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-code"></i> Skills Management</h2>
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

                <!-- Add Category Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-plus"></i> Add New Skill Category</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="action" value="add_category">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="category_name" placeholder="Category Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="icon" placeholder="Font Awesome Icon (e.g., fas fa-code)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="sort_order" placeholder="Order" value="<?php echo count($categories) + 1; ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Add Category</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Add/Edit Skill Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-plus"></i>
                            <?php echo $edit_skill ? 'Edit Skill' : 'Add New Skill'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $edit_skill ? 'update_skill' : 'add_skill'; ?>">
                            <?php if ($edit_skill): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_skill['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"
                                                <?php echo ($edit_skill && $edit_skill['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="skill_name" class="form-label">Skill Name</label>
                                    <input type="text" class="form-control" id="skill_name" name="skill_name"
                                        value="<?php echo $edit_skill ? $edit_skill['skill_name'] : ''; ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="proficiency_level" class="form-label">Proficiency Level (%)</label>
                                    <input type="number" class="form-control" id="proficiency_level" name="proficiency_level"
                                        value="<?php echo $edit_skill ? $edit_skill['proficiency_level'] : '80'; ?>"
                                        min="0" max="100" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="icon" class="form-label">Icon (Optional)</label>
                                    <input type="text" class="form-control" id="icon" name="icon"
                                        value="<?php echo $edit_skill ? $edit_skill['icon'] : ''; ?>"
                                        placeholder="fab fa-html5">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $edit_skill ? 'Update' : 'Add'; ?> Skill
                                </button>
                                <?php if ($edit_skill): ?>
                                    <a href="skills.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Skills List by Category -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Skills Overview</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($skills)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-code fa-3x mb-3"></i>
                                <p>No skills found. Add your first skill above.</p>
                            </div>
                        <?php else: ?>
                            <?php
                            $current_category = '';
                            foreach ($skills as $skill):
                                if ($skill['category_name'] != $current_category):
                                    if ($current_category != '') echo '</div></div>';
                                    $current_category = $skill['category_name'];
                            ?>
                                    <div class="mb-4">
                                        <h6 class="border-bottom pb-2 mb-3">
                                            <?php if ($skill['category_name']): ?>
                                                <?php echo htmlspecialchars($skill['category_name']); ?>
                                            <?php else: ?>
                                                Uncategorized
                                            <?php endif; ?>
                                        </h6>
                                        <div class="row">
                                        <?php endif; ?>

                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span>
                                                    <?php if ($skill['icon']): ?>
                                                        <i class="<?php echo $skill['icon']; ?>"></i>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($skill['skill_name']); ?>
                                                </span>
                                                <div>
                                                    <span class="badge bg-primary"><?php echo $skill['proficiency_level']; ?>%</span>
                                                    <a href="?edit=<?php echo $skill['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this skill?')">
                                                        <input type="hidden" name="action" value="delete_skill">
                                                        <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="progress skill-progress">
                                                <div class="progress-bar" style="width: <?php echo $skill['proficiency_level']; ?>%"></div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                    <?php if (!empty($skills)): ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>