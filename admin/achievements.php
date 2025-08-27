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

        if (isset($_POST['add_achievement'])) {
            $stmt = $pdo->prepare("INSERT INTO achievements (title, description, organization, date_achieved, category, certificate_url, image_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                sanitize($_POST['title']),
                sanitize($_POST['description']),
                sanitize($_POST['organization']),
                $_POST['date_achieved'],
                sanitize($_POST['category']),
                sanitize($_POST['certificate_url']),
                sanitize($_POST['image_url']),
                isset($_POST['is_featured']) ? 1 : 0
            ]);
            $success = "Achievement added successfully!";
        }

        if (isset($_POST['update_achievement'])) {
            $stmt = $pdo->prepare("UPDATE achievements SET title = ?, description = ?, organization = ?, date_achieved = ?, category = ?, certificate_url = ?, image_url = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([
                sanitize($_POST['title']),
                sanitize($_POST['description']),
                sanitize($_POST['organization']),
                $_POST['date_achieved'],
                sanitize($_POST['category']),
                sanitize($_POST['certificate_url']),
                sanitize($_POST['image_url']),
                isset($_POST['is_featured']) ? 1 : 0,
                (int)$_POST['achievement_id']
            ]);
            $success = "Achievement updated successfully!";
        }

        if (isset($_POST['delete_achievement'])) {
            $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
            $stmt->execute([(int)$_POST['achievement_id']]);
            $success = "Achievement deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch achievements data
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM achievements ORDER BY date_achieved DESC");
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group achievements by category
    $achievementsByCategory = [];
    foreach ($achievements as $achievement) {
        $achievementsByCategory[$achievement['category']][] = $achievement;
    }

    // Achievement categories
    $categories = [
        'Competition',
        'Certification',
        'Award',
        'Recognition',
        'Course Completion',
        'Other'
    ];
} catch (Exception $e) {
    $error = "Error fetching achievements: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements Management - Admin Panel</title>
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
                <li><a href="skills.php"><i class="fas fa-code"></i> Skills</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="achievements.php" class="active"><i class="fas fa-trophy"></i> Achievements</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-trophy"></i> Achievements Management</h1>
                <p>Manage your awards, certifications, and accomplishments</p>
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
                <!-- Add New Achievement Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-plus"></i> Add New Achievement</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="title">Achievement Title</label>
                                    <input type="text" id="title" name="title" required>
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
                                    <label for="organization">Organization/Issuer</label>
                                    <input type="text" id="organization" name="organization" required>
                                </div>
                                <div class="form-group">
                                    <label for="date_achieved">Date Achieved</label>
                                    <input type="date" id="date_achieved" name="date_achieved" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="3" placeholder="Describe the achievement and its significance"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="certificate_url">Certificate URL</label>
                                    <input type="url" id="certificate_url" name="certificate_url" placeholder="Link to certificate or verification">
                                </div>
                                <div class="form-group">
                                    <label for="image_url">Image URL</label>
                                    <input type="url" id="image_url" name="image_url" placeholder="Achievement image or certificate image">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_featured">
                                    <span class="checkmark"></span>
                                    Featured Achievement (Show prominently)
                                </label>
                            </div>

                            <button type="submit" name="add_achievement" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Achievement
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Achievements List -->
                <div class="card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> My Achievements</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($achievementsByCategory)): ?>
                            <?php foreach ($achievementsByCategory as $category => $categoryAchievements): ?>
                                <div class="achievement-category">
                                    <h4 class="category-title">
                                        <i class="fas fa-trophy"></i>
                                        <?= htmlspecialchars($category) ?>
                                    </h4>
                                    <div class="achievements-grid">
                                        <?php foreach ($categoryAchievements as $achievement): ?>
                                            <div class="achievement-card <?= $achievement['is_featured'] ? 'featured' : '' ?>">
                                                <?php if ($achievement['image_url']): ?>
                                                    <div class="achievement-image">
                                                        <img src="<?= htmlspecialchars($achievement['image_url']) ?>" alt="<?= htmlspecialchars($achievement['title']) ?>" loading="lazy">
                                                    </div>
                                                <?php endif; ?>

                                                <div class="achievement-content">
                                                    <div class="achievement-header">
                                                        <h5><?= htmlspecialchars($achievement['title']) ?></h5>
                                                        <?php if ($achievement['is_featured']): ?>
                                                            <span class="featured-badge">Featured</span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="achievement-meta">
                                                        <div class="achievement-org">
                                                            <i class="fas fa-building"></i>
                                                            <?= htmlspecialchars($achievement['organization']) ?>
                                                        </div>
                                                        <div class="achievement-date">
                                                            <i class="fas fa-calendar"></i>
                                                            <?= date('M Y', strtotime($achievement['date_achieved'])) ?>
                                                        </div>
                                                    </div>

                                                    <?php if ($achievement['description']): ?>
                                                        <p class="achievement-description">
                                                            <?= htmlspecialchars($achievement['description']) ?>
                                                        </p>
                                                    <?php endif; ?>

                                                    <div class="achievement-actions">
                                                        <?php if ($achievement['certificate_url']): ?>
                                                            <a href="<?= htmlspecialchars($achievement['certificate_url']) ?>" target="_blank" class="btn-link" title="View Certificate">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <button onclick="editAchievement(<?= $achievement['id'] ?>)" class="btn-icon btn-edit" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button onclick="deleteAchievement(<?= $achievement['id'] ?>)" class="btn-icon btn-delete" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-trophy"></i>
                                <h3>No Achievements Added Yet</h3>
                                <p>Start by adding your first achievement using the form above.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Achievement Modal -->
    <div id="editAchievementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Achievement</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="achievement_id" id="edit_achievement_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_title">Achievement Title</label>
                        <input type="text" id="edit_title" name="title" required>
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
                        <label for="edit_organization">Organization/Issuer</label>
                        <input type="text" id="edit_organization" name="organization" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_date_achieved">Date Achieved</label>
                        <input type="date" id="edit_date_achieved" name="date_achieved" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_certificate_url">Certificate URL</label>
                        <input type="url" id="edit_certificate_url" name="certificate_url">
                    </div>
                    <div class="form-group">
                        <label for="edit_image_url">Image URL</label>
                        <input type="url" id="edit_image_url" name="image_url">
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" id="edit_is_featured">
                        <span class="checkmark"></span>
                        Featured Achievement (Show prominently)
                    </label>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="update_achievement" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Achievement
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
            <p>Are you sure you want to delete this achievement? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="achievement_id" id="delete_achievement_id">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="delete_achievement" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editAchievement(achievementId) {
            <?php if (!empty($achievements)): ?>
                const achievements = <?= json_encode($achievements) ?>;
                const achievement = achievements.find(a => a.id == achievementId);

                if (achievement) {
                    document.getElementById('edit_achievement_id').value = achievement.id;
                    document.getElementById('edit_title').value = achievement.title;
                    document.getElementById('edit_category').value = achievement.category;
                    document.getElementById('edit_organization').value = achievement.organization;
                    document.getElementById('edit_date_achieved').value = achievement.date_achieved;
                    document.getElementById('edit_description').value = achievement.description || '';
                    document.getElementById('edit_certificate_url').value = achievement.certificate_url || '';
                    document.getElementById('edit_image_url').value = achievement.image_url || '';
                    document.getElementById('edit_is_featured').checked = achievement.is_featured == 1;

                    document.getElementById('editAchievementModal').style.display = 'block';
                }
            <?php endif; ?>
        }

        function deleteAchievement(achievementId) {
            document.getElementById('delete_achievement_id').value = achievementId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editAchievementModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editAchievementModal');
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