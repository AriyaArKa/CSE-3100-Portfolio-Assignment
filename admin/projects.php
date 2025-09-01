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
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $technologies = trim($_POST['technologies']);
        $github_link = trim($_POST['github_link']);
        $demo_link = trim($_POST['demo_link']);
        $image = trim($_POST['image']);

        if (!empty($title) && !empty($description)) {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, technologies, github_link, demo_link, image) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $technologies, $github_link, $demo_link, $image])) {
                $message = 'Project added successfully!';
            } else {
                $error = 'Error adding project.';
            }
        } else {
            $error = 'Title and description are required.';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $technologies = trim($_POST['technologies']);
        $github_link = trim($_POST['github_link']);
        $demo_link = trim($_POST['demo_link']);
        $image = trim($_POST['image']);
        $status = $_POST['status'];

        if (!empty($title) && !empty($description)) {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, technologies = ?, github_link = ?, demo_link = ?, image = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $technologies, $github_link, $demo_link, $image, $status, $id])) {
                $message = 'Project updated successfully!';
            } else {
                $error = 'Error updating project.';
            }
        } else {
            $error = 'Title and description are required.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Project deleted successfully!';
        } else {
            $error = 'Error deleting project.';
        }
    }
}

// Get project for editing
$edit_project = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_project = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all projects
$stmt = $pdo->prepare("SELECT * FROM projects ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../projects.css">
</head>

<body>
    <div class="admin-layout">
        <?php echo getAdminNavigation('projects.php'); ?>

        <div class="admin-main">
            <?php echo getAdminHeader('Manage Projects'); ?>

            <div class="admin-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Add/Edit Project Form -->
                <div class="admin-card">
                    <h2><?php echo $edit_project ? 'Edit Project' : 'Add New Project'; ?></h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $edit_project ? 'edit' : 'add'; ?>">
                        <?php if ($edit_project): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Project Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required
                                value="<?php echo $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $edit_project ? htmlspecialchars($edit_project['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="technologies">Technologies</label>
                            <input type="text" id="technologies" name="technologies" class="form-control"
                                placeholder="e.g., PHP, JavaScript, MySQL"
                                value="<?php echo $edit_project ? htmlspecialchars($edit_project['technologies']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="github_link">GitHub Link</label>
                            <input type="url" id="github_link" name="github_link" class="form-control"
                                value="<?php echo $edit_project ? htmlspecialchars($edit_project['github_link']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="demo_link">Demo Link</label>
                            <input type="url" id="demo_link" name="demo_link" class="form-control"
                                value="<?php echo $edit_project ? htmlspecialchars($edit_project['demo_link']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="image">Image URL</label>
                            <input type="url" id="image" name="image" class="form-control"
                                value="<?php echo $edit_project ? htmlspecialchars($edit_project['image']) : ''; ?>">
                        </div>

                        <?php if ($edit_project): ?>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="active" <?php echo $edit_project['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $edit_project['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_project ? 'Update Project' : 'Add Project'; ?>
                            </button>
                            <?php if ($edit_project): ?>
                                <a href="projects.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Projects List -->
                <div class="admin-card">
                    <h2>All Projects (<?php echo count($projects); ?>)</h2>

                    <?php if (empty($projects)): ?>
                        <p>No projects found.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Technologies</th>
                                    <th>Links</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($project['title']); ?></strong><br>
                                            <small><?php echo htmlspecialchars(substr($project['description'], 0, 100)) . '...'; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($project['technologies']); ?></td>
                                        <td>
                                            <?php if ($project['github_link']): ?>
                                                <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" class="btn btn-sm" style="background: #333; color: white; margin-right: 0.5rem;">
                                                    <i class="fab fa-github"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($project['demo_link']): ?>
                                                <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $project['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($project['created_at'])); ?></td>
                                        <td>
                                            <a href="projects.php?edit=<?php echo $project['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    

    <script src="../script.js"></script>
</body>

</html>