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
        $date_achieved = $_POST['date_achieved'];
        $category = trim($_POST['category']);

        if (!empty($title) && !empty($description) && !empty($date_achieved)) {
            $stmt = $pdo->prepare("INSERT INTO achievements (title, description, date_achieved, category) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $date_achieved, $category])) {
                $message = 'Achievement added successfully!';
            } else {
                $error = 'Error adding achievement.';
            }
        } else {
            $error = 'Title, description, and date are required.';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date_achieved = $_POST['date_achieved'];
        $category = trim($_POST['category']);
        $status = $_POST['status'];

        if (!empty($title) && !empty($description) && !empty($date_achieved)) {
            $stmt = $pdo->prepare("UPDATE achievements SET title = ?, description = ?, date_achieved = ?, category = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $date_achieved, $category, $status, $id])) {
                $message = 'Achievement updated successfully!';
            } else {
                $error = 'Error updating achievement.';
            }
        } else {
            $error = 'Title, description, and date are required.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Achievement deleted successfully!';
        } else {
            $error = 'Error deleting achievement.';
        }
    }
}

// Get achievement for editing
$edit_achievement = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_achievement = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all achievements
$stmt = $pdo->prepare("SELECT * FROM achievements ORDER BY date_achieved DESC");
$stmt->execute();
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct categories
$stmt = $pdo->prepare("SELECT DISTINCT category FROM achievements WHERE category IS NOT NULL ORDER BY category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../achievements.css">
</head>

<body>
    <div class="admin-layout">
        <?php echo getAdminNavigation('achievements.php'); ?>

        <div class="admin-main">
            <?php echo getAdminHeader('Manage Achievements'); ?>

            <div class="admin-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Add/Edit Achievement Form -->
                <div class="admin-card">
                    <h2><?php echo $edit_achievement ? 'Edit Achievement' : 'Add New Achievement'; ?></h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $edit_achievement ? 'edit' : 'add'; ?>">
                        <?php if ($edit_achievement): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_achievement['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Achievement Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required
                                value="<?php echo $edit_achievement ? htmlspecialchars($edit_achievement['title']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $edit_achievement ? htmlspecialchars($edit_achievement['description']) : ''; ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="date_achieved">Date Achieved *</label>
                                <input type="date" id="date_achieved" name="date_achieved" class="form-control" required
                                    value="<?php echo $edit_achievement ? $edit_achievement['date_achieved'] : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" class="form-control"
                                    list="categories"
                                    placeholder="e.g., Competition, Programming, Machine Learning"
                                    value="<?php echo $edit_achievement ? htmlspecialchars($edit_achievement['category']) : ''; ?>">
                                <datalist id="categories">
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>">
                                        <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <?php if ($edit_achievement): ?>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="active" <?php echo $edit_achievement['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $edit_achievement['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_achievement ? 'Update Achievement' : 'Add Achievement'; ?>
                            </button>
                            <?php if ($edit_achievement): ?>
                                <a href="achievements.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Achievements List -->
                <div class="admin-card">
                    <h2>All Achievements (<?php echo count($achievements); ?>)</h2>

                    <?php if (empty($achievements)): ?>
                        <p>No achievements found. Add your first achievement above!</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Date Achieved</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achievements as $achievement): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($achievement['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($achievement['description'], 0, 100)) . (strlen($achievement['description']) > 100 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($achievement['category']): ?>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($achievement['category']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($achievement['date_achieved'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $achievement['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($achievement['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="achievements.php?edit=<?php echo $achievement['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this achievement?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $achievement['id']; ?>">
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