<?php
session_start();
include '../config.php';
include 'auth.php';

requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_POST) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category = trim($_POST['category']);
        $skill_name = trim($_POST['skill_name']);
        $proficiency = intval($_POST['proficiency']);

        if (!empty($category) && !empty($skill_name) && $proficiency >= 0 && $proficiency <= 100) {
            $stmt = $pdo->prepare("INSERT INTO skills (category, skill_name, proficiency) VALUES (?, ?, ?)");
            if ($stmt->execute([$category, $skill_name, $proficiency])) {
                $message = 'Skill added successfully!';
            } else {
                $error = 'Error adding skill.';
            }
        } else {
            $error = 'All fields are required and proficiency must be between 0-100.';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $category = trim($_POST['category']);
        $skill_name = trim($_POST['skill_name']);
        $proficiency = intval($_POST['proficiency']);
        $status = $_POST['status'];

        if (!empty($category) && !empty($skill_name) && $proficiency >= 0 && $proficiency <= 100) {
            $stmt = $pdo->prepare("UPDATE skills SET category = ?, skill_name = ?, proficiency = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$category, $skill_name, $proficiency, $status, $id])) {
                $message = 'Skill updated successfully!';
            } else {
                $error = 'Error updating skill.';
            }
        } else {
            $error = 'All fields are required and proficiency must be between 0-100.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Skill deleted successfully!';
        } else {
            $error = 'Error deleting skill.';
        }
    }
}

// Get skill for editing
$edit_skill = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_skill = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all skills grouped by category
$stmt = $pdo->prepare("SELECT * FROM skills ORDER BY category, skill_name");
$stmt->execute();
$all_skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group skills by category
$skills_by_category = [];
foreach ($all_skills as $skill) {
    $skills_by_category[$skill['category']][] = $skill;
}

// Get distinct categories for the form
$stmt = $pdo->prepare("SELECT DISTINCT category FROM skills ORDER BY category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-layout">
        <?php echo getAdminNavigation('skills.php'); ?>

        <div class="admin-main">
            <?php echo getAdminHeader('Manage Skills'); ?>

            <div class="admin-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Add/Edit Skill Form -->
                <div class="admin-card">
                    <h2><?php echo $edit_skill ? 'Edit Skill' : 'Add New Skill'; ?></h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $edit_skill ? 'edit' : 'add'; ?>">
                        <?php if ($edit_skill): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_skill['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="category">Category *</label>
                                <input type="text" id="category" name="category" class="form-control" required
                                    list="categories"
                                    value="<?php echo $edit_skill ? htmlspecialchars($edit_skill['category']) : ''; ?>">
                                <datalist id="categories">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>">
                                        <?php endforeach; ?>
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label for="skill_name">Skill Name *</label>
                                <input type="text" id="skill_name" name="skill_name" class="form-control" required
                                    value="<?php echo $edit_skill ? htmlspecialchars($edit_skill['skill_name']) : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="proficiency">Proficiency (%) *</label>
                                <input type="range" id="proficiency" name="proficiency" class="form-control"
                                    min="0" max="100" step="5" required
                                    value="<?php echo $edit_skill ? $edit_skill['proficiency'] : '80'; ?>"
                                    oninput="document.getElementById('proficiency-value').textContent = this.value + '%'">
                                <div style="text-align: center; margin-top: 0.5rem;">
                                    <span id="proficiency-value"><?php echo $edit_skill ? $edit_skill['proficiency'] : '80'; ?>%</span>
                                </div>
                            </div>

                            <?php if ($edit_skill): ?>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="active" <?php echo $edit_skill['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $edit_skill['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_skill ? 'Update Skill' : 'Add Skill'; ?>
                            </button>
                            <?php if ($edit_skill): ?>
                                <a href="skills.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Skills List by Category -->
                <?php foreach ($skills_by_category as $category => $skills): ?>
                    <div class="admin-card">
                        <h2><?php echo htmlspecialchars($category); ?> (<?php echo count($skills); ?>)</h2>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Skill Name</th>
                                    <th>Proficiency</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skills as $skill): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <div style="flex: 1; height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;">
                                                    <div style="width: <?php echo $skill['proficiency']; ?>%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                                                </div>
                                                <span style="font-weight: bold;"><?php echo $skill['proficiency']; ?>%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $skill['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($skill['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($skill['created_at'])); ?></td>
                                        <td>
                                            <a href="skills.php?edit=<?php echo $skill['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this skill?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($skills_by_category)): ?>
                    <div class="admin-card">
                        <p>No skills found. Add your first skill above!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
        .badge {
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        input[type="range"] {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            width: 20px;
            height: 20px;
            background: #4a90e2;
            border-radius: 50%;
            cursor: pointer;
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #4a90e2;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }
    </style>

    <script src="../script.js"></script>
</body>

</html>