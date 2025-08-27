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

        if (isset($_POST['add_project'])) {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, long_description, tech_stack, project_url, github_url, image_url, start_date, end_date, status, category, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                sanitize($_POST['title']),
                sanitize($_POST['description']),
                sanitize($_POST['long_description']),
                sanitize($_POST['tech_stack']),
                sanitize($_POST['project_url']),
                sanitize($_POST['github_url']),
                sanitize($_POST['image_url']),
                $_POST['start_date'],
                $_POST['end_date'] ?: null,
                sanitize($_POST['status']),
                sanitize($_POST['category']),
                isset($_POST['is_featured']) ? 1 : 0
            ]);
            $success = "Project added successfully!";
        }

        if (isset($_POST['update_project'])) {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, long_description = ?, tech_stack = ?, project_url = ?, github_url = ?, image_url = ?, start_date = ?, end_date = ?, status = ?, category = ?, is_featured = ? WHERE id = ?");
            $stmt->execute([
                sanitize($_POST['title']),
                sanitize($_POST['description']),
                sanitize($_POST['long_description']),
                sanitize($_POST['tech_stack']),
                sanitize($_POST['project_url']),
                sanitize($_POST['github_url']),
                sanitize($_POST['image_url']),
                $_POST['start_date'],
                $_POST['end_date'] ?: null,
                sanitize($_POST['status']),
                sanitize($_POST['category']),
                isset($_POST['is_featured']) ? 1 : 0,
                (int)$_POST['project_id']
            ]);
            $success = "Project updated successfully!";
        }

        if (isset($_POST['delete_project'])) {
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([(int)$_POST['project_id']]);
            $success = "Project deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch projects data
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY start_date DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Project categories and statuses
    $categories = [
        'Web Development',
        'Mobile App',
        'Desktop Application',
        'API/Backend',
        'Data Science',
        'Machine Learning',
        'Game Development',
        'Other'
    ];

    $statuses = [
        'Completed',
        'In Progress',
        'On Hold',
        'Planned'
    ];
} catch (Exception $e) {
    $error = "Error fetching projects: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management - Admin Panel</title>
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
                <li><a href="projects.php" class="active"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="achievements.php"><i class="fas fa-trophy"></i> Achievements</a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-project-diagram"></i> Projects Management</h1>
                <p>Manage your portfolio projects and showcase your work</p>
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
                <!-- Add New Project Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-plus"></i> Add New Project</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="title">Project Title</label>
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

                            <div class="form-group">
                                <label for="description">Short Description</label>
                                <textarea id="description" name="description" rows="2" required placeholder="Brief description for project cards"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="long_description">Detailed Description</label>
                                <textarea id="long_description" name="long_description" rows="4" placeholder="Detailed project description, features, challenges, etc."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tech_stack">Technology Stack</label>
                                <input type="text" id="tech_stack" name="tech_stack" placeholder="e.g., React, Node.js, MongoDB, Express" required>
                                <small class="form-help">Separate technologies with commas</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="project_url">Live Project URL</label>
                                    <input type="url" id="project_url" name="project_url" placeholder="https://your-project.com">
                                </div>
                                <div class="form-group">
                                    <label for="github_url">GitHub Repository URL</label>
                                    <input type="url" id="github_url" name="github_url" placeholder="https://github.com/username/repo">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="image_url">Project Image URL</label>
                                <input type="url" id="image_url" name="image_url" placeholder="Project screenshot or demo image">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date">
                                    <small class="form-help">Leave empty if ongoing</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="status">Project Status</label>
                                    <select id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?= $status ?>"><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_featured">
                                        <span class="checkmark"></span>
                                        Featured Project (Show prominently)
                                    </label>
                                </div>
                            </div>

                            <button type="submit" name="add_project" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Project
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Projects List -->
                <div class="card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> My Projects</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($projects)): ?>
                            <div class="projects-grid">
                                <?php foreach ($projects as $project): ?>
                                    <div class="project-card <?= $project['is_featured'] ? 'featured' : '' ?>">
                                        <?php if ($project['image_url']): ?>
                                            <div class="project-image">
                                                <img src="<?= htmlspecialchars($project['image_url']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" loading="lazy">
                                                <div class="project-overlay">
                                                    <?php if ($project['project_url']): ?>
                                                        <a href="<?= htmlspecialchars($project['project_url']) ?>" target="_blank" class="btn-overlay" title="View Live">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($project['github_url']): ?>
                                                        <a href="<?= htmlspecialchars($project['github_url']) ?>" target="_blank" class="btn-overlay" title="View Code">
                                                            <i class="fab fa-github"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="project-content">
                                            <div class="project-header">
                                                <h4><?= htmlspecialchars($project['title']) ?></h4>
                                                <div class="project-actions">
                                                    <button onclick="editProject(<?= $project['id'] ?>)" class="btn-icon btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteProject(<?= $project['id'] ?>)" class="btn-icon btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="project-meta">
                                                <span class="project-category"><?= htmlspecialchars($project['category']) ?></span>
                                                <span class="project-status status-<?= strtolower(str_replace(' ', '-', $project['status'])) ?>">
                                                    <?= htmlspecialchars($project['status']) ?>
                                                </span>
                                                <?php if ($project['is_featured']): ?>
                                                    <span class="featured-badge">Featured</span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="project-description">
                                                <?= htmlspecialchars($project['description']) ?>
                                            </p>

                                            <div class="project-tech">
                                                <?php foreach (explode(',', $project['tech_stack']) as $tech): ?>
                                                    <span class="tech-tag"><?= trim(htmlspecialchars($tech)) ?></span>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="project-dates">
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M Y', strtotime($project['start_date'])) ?> -
                                                <?= $project['end_date'] ? date('M Y', strtotime($project['end_date'])) : 'Present' ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-project-diagram"></i>
                                <h3>No Projects Added Yet</h3>
                                <p>Start by adding your first project using the form above.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Project</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="project_id" id="edit_project_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_title">Project Title</label>
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

                <div class="form-group">
                    <label for="edit_description">Short Description</label>
                    <textarea id="edit_description" name="description" rows="2" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_long_description">Detailed Description</label>
                    <textarea id="edit_long_description" name="long_description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_tech_stack">Technology Stack</label>
                    <input type="text" id="edit_tech_stack" name="tech_stack" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_project_url">Live Project URL</label>
                        <input type="url" id="edit_project_url" name="project_url">
                    </div>
                    <div class="form-group">
                        <label for="edit_github_url">GitHub Repository URL</label>
                        <input type="url" id="edit_github_url" name="github_url">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_image_url">Project Image URL</label>
                    <input type="url" id="edit_image_url" name="image_url">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_start_date">Start Date</label>
                        <input type="date" id="edit_start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_end_date">End Date</label>
                        <input type="date" id="edit_end_date" name="end_date">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_status">Project Status</label>
                        <select id="edit_status" name="status" required>
                            <option value="">Select Status</option>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>"><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" id="edit_is_featured">
                            <span class="checkmark"></span>
                            Featured Project (Show prominently)
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="update_project" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Project
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
            <p>Are you sure you want to delete this project? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="project_id" id="delete_project_id">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="delete_project" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editProject(projectId) {
            <?php if (!empty($projects)): ?>
                const projects = <?= json_encode($projects) ?>;
                const project = projects.find(p => p.id == projectId);

                if (project) {
                    document.getElementById('edit_project_id').value = project.id;
                    document.getElementById('edit_title').value = project.title;
                    document.getElementById('edit_category').value = project.category;
                    document.getElementById('edit_description').value = project.description;
                    document.getElementById('edit_long_description').value = project.long_description || '';
                    document.getElementById('edit_tech_stack').value = project.tech_stack;
                    document.getElementById('edit_project_url').value = project.project_url || '';
                    document.getElementById('edit_github_url').value = project.github_url || '';
                    document.getElementById('edit_image_url').value = project.image_url || '';
                    document.getElementById('edit_start_date').value = project.start_date;
                    document.getElementById('edit_end_date').value = project.end_date || '';
                    document.getElementById('edit_status').value = project.status;
                    document.getElementById('edit_is_featured').checked = project.is_featured == 1;

                    document.getElementById('editProjectModal').style.display = 'block';
                }
            <?php endif; ?>
        }

        function deleteProject(projectId) {
            document.getElementById('delete_project_id').value = projectId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editProjectModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editProjectModal');
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