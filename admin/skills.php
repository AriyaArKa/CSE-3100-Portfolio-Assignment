<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('/admin/login.php');
}

// Handle form submissions
if ($_POST) {
    try {
        $pdo = getDBConnection();

        if (isset($_POST['add_skill'])) {
            $stmt = $pdo->prepare("INSERT INTO skills (name, category, proficiency, description, icon, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                sanitize($_POST['name']),
                sanitize($_POST['category']),
                (int)$_POST['proficiency'],
                sanitize($_POST['description']),
                sanitize($_POST['icon']),
                isset($_POST['is_featured']) ? 1 : 0
            ]);
            $success = "Skill added successfully!";
        }

        if (isset($_POST['update_skill'])) {
            $stmt = $pdo->prepare("UPDATE skills SET name = ?, category = ?, proficiency = ?, description = ?, icon = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([
                sanitize($_POST['name']),
                sanitize($_POST['category']),
                (int)$_POST['proficiency'],
                sanitize($_POST['description']),
                sanitize($_POST['icon']),
                isset($_POST['is_featured']) ? 1 : 0,
                (int)$_POST['skill_id']
            ]);
            $success = "Skill updated successfully!";
        }

        if (isset($_POST['delete_skill'])) {
            $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
            $stmt->execute([(int)$_POST['skill_id']]);
            $success = "Skill deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch skills data
try {
    $pdo = getDBConnection();

    // Get all skills grouped by category
    $stmt = $pdo->query("SELECT * FROM skills ORDER BY category, proficiency DESC");
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group skills by category
    $skillsByCategory = [];
    foreach ($skills as $skill) {
        $skillsByCategory[$skill['category']][] = $skill;
    }

    // Get skill categories for the form
    $categories = [
        'Programming Languages',
        'Web Technologies',
        'Frameworks & Libraries',
        'Databases',
        'Tools & Software',
        'Other'
    ];
} catch (Exception $e) {
    $error = "Error fetching skills: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills Management - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-user-circle"></i> Portfolio Admin</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                <li><a href="personal.php"><i class="fas fa-user"></i> Personal Info</a></li>
                <li><a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a></li>
                <li><a href="skills.php" class="active"><i class="fas fa-code"></i> Skills</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="achievements.php"><i class="fas fa-trophy"></i> Achievements</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-code"></i> Skills Management</h1>
                <p>Manage your technical skills and proficiency levels</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="content-grid">
                <!-- Add New Skill Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-plus"></i> Add New Skill</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Skill Name</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category ?>"><?= $category ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="proficiency">Proficiency Level (1-100)</label>
                                    <input type="range" id="proficiency" name="proficiency" min="1" max="100" value="50" oninput="updateProficiencyValue(this.value)">
                                    <span id="proficiency-value">50%</span>
                                </div>
                                <div class="form-group">
                                    <label for="icon">Icon Class (Font Awesome)</label>
                                    <input type="text" id="icon" name="icon" placeholder="e.g., fab fa-js-square">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description (Optional)</label>
                                <textarea id="description" name="description" rows="2"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_featured">
                                    <span class="checkmark"></span>
                                    Featured Skill (Show prominently)
                                </label>
                            </div>

                            <button type="submit" name="add_skill" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Skill
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Skills List -->
                <div class="card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Current Skills</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($skillsByCategory)): ?>
                            <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                                <div class="skill-category">
                                    <h4 class="category-title"><?= htmlspecialchars($category) ?></h4>
                                    <div class="skills-grid">
                                        <?php foreach ($categorySkills as $skill): ?>
                                            <div class="skill-item <?= $skill['is_featured'] ? 'featured' : '' ?>">
                                                <div class="skill-header">
                                                    <div class="skill-name">
                                                        <?php if ($skill['icon']): ?>
                                                            <i class="<?= htmlspecialchars($skill['icon']) ?>"></i>
                                                        <?php endif; ?>
                                                        <span><?= htmlspecialchars($skill['name']) ?></span>
                                                        <?php if ($skill['is_featured']): ?>
                                                            <span class="featured-badge">Featured</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="skill-actions">
                                                        <button onclick="editSkill(<?= $skill['id'] ?>)" class="btn-icon btn-edit" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteSkill(<?= $skill['id'] ?>)" class="btn-icon btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="skill-proficiency">
                                                    <div class="proficiency-bar">
                                                        <div class="proficiency-fill" style="width: <?= $skill['proficiency'] ?>%"></div>
                                                    </div>
                                                    <span class="proficiency-text"><?= $skill['proficiency'] ?>%</span>
                                                </div>

                                                <?php if ($skill['description']): ?>
                                                    <p class="skill-description"><?= htmlspecialchars($skill['description']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-code"></i>
                                <h3>No Skills Added Yet</h3>
                                <p>Start by adding your first skill using the form above.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Skill Modal -->
    <div id="editSkillModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Skill</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="skill_id" id="edit_skill_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_name">Skill Name</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Category</label>
                        <select id="edit_category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category ?>"><?= $category ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_proficiency">Proficiency Level (1-100)</label>
                        <input type="range" id="edit_proficiency" name="proficiency" min="1" max="100" value="50" oninput="updateEditProficiencyValue(this.value)">
                        <span id="edit-proficiency-value">50%</span>
                    </div>
                    <div class="form-group">
                        <label for="edit_icon">Icon Class (Font Awesome)</label>
                        <input type="text" id="edit_icon" name="icon" placeholder="e.g., fab fa-js-square">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_description">Description (Optional)</label>
                    <textarea id="edit_description" name="description" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" id="edit_is_featured">
                        <span class="checkmark"></span>
                        Featured Skill (Show prominently)
                    </label>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="update_skill" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Skill
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Delete</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <p>Are you sure you want to delete this skill? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="skill_id" id="delete_skill_id">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="delete_skill" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateProficiencyValue(value) {
            document.getElementById('proficiency-value').textContent = value + '%';
        }

        function updateEditProficiencyValue(value) {
            document.getElementById('edit-proficiency-value').textContent = value + '%';
        }

        function editSkill(skillId) {
            // In a real implementation, you would fetch the skill data via AJAX
            // For now, we'll just show the modal
            <?php if (!empty($skills)): ?>
                const skills = <?= json_encode($skills) ?>;
                const skill = skills.find(s => s.id == skillId);

                if (skill) {
                    document.getElementById('edit_skill_id').value = skill.id;
                    document.getElementById('edit_name').value = skill.name;
                    document.getElementById('edit_category').value = skill.category;
                    document.getElementById('edit_proficiency').value = skill.proficiency;
                    document.getElementById('edit-proficiency-value').textContent = skill.proficiency + '%';
                    document.getElementById('edit_icon').value = skill.icon || '';
                    document.getElementById('edit_description').value = skill.description || '';
                    document.getElementById('edit_is_featured').checked = skill.is_featured == 1;

                    document.getElementById('editSkillModal').style.display = 'block';
                }
            <?php endif; ?>
        }

        function deleteSkill(skillId) {
            document.getElementById('delete_skill_id').value = skillId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editSkillModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editSkillModal');
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }
    </script>

    <script src="assets/js/admin.js"></script>
</body>

</html>