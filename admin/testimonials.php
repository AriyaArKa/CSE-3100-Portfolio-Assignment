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
        $name = trim($_POST['name']);
        $position = trim($_POST['position']);
        $company = trim($_POST['company']);
        $message_text = trim($_POST['message']);
        $image = trim($_POST['image']);
        $rating = intval($_POST['rating']);

        if (!empty($name) && !empty($message_text) && $rating >= 1 && $rating <= 5) {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, position, company, message, image, rating) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $position, $company, $message_text, $image, $rating])) {
                $message = 'Testimonial added successfully!';
            } else {
                $error = 'Error adding testimonial.';
            }
        } else {
            $error = 'Name, message, and valid rating are required.';
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $position = trim($_POST['position']);
        $company = trim($_POST['company']);
        $message_text = trim($_POST['message']);
        $image = trim($_POST['image']);
        $rating = intval($_POST['rating']);
        $status = $_POST['status'];

        if (!empty($name) && !empty($message_text) && $rating >= 1 && $rating <= 5) {
            $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, position = ?, company = ?, message = ?, image = ?, rating = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$name, $position, $company, $message_text, $image, $rating, $status, $id])) {
                $message = 'Testimonial updated successfully!';
            } else {
                $error = 'Error updating testimonial.';
            }
        } else {
            $error = 'Name, message, and valid rating are required.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Testimonial deleted successfully!';
        } else {
            $error = 'Error deleting testimonial.';
        }
    }
}

// Get testimonial for editing
$edit_testimonial = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all testimonials
$stmt = $pdo->prepare("SELECT * FROM testimonials ORDER BY created_at DESC");
$stmt->execute();
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-layout">
        <?php echo getAdminNavigation('testimonials.php'); ?>

        <div class="admin-main">
            <?php echo getAdminHeader('Manage Testimonials'); ?>

            <div class="admin-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Add/Edit Testimonial Form -->
                <div class="admin-card">
                    <h2><?php echo $edit_testimonial ? 'Edit Testimonial' : 'Add New Testimonial'; ?></h2>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="<?php echo $edit_testimonial ? 'edit' : 'add'; ?>">
                        <?php if ($edit_testimonial): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_testimonial['id']; ?>">
                        <?php endif; ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" id="name" name="name" class="form-control" required
                                    value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" id="position" name="position" class="form-control"
                                    placeholder="e.g., Senior Developer"
                                    value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['position']) : ''; ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="company">Company</label>
                                <input type="text" id="company" name="company" class="form-control"
                                    placeholder="e.g., TechCorp Solutions"
                                    value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['company']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="image">Image URL</label>
                                <input type="url" id="image" name="image" class="form-control"
                                    placeholder="https://example.com/image.jpg"
                                    value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['image']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">Testimonial Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="4" required
                                placeholder="Write the testimonial message here..."><?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['message']) : ''; ?></textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label for="rating">Rating *</label>
                                <select id="rating" name="rating" class="form-control" required>
                                    <option value="">Select Rating</option>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($edit_testimonial && $edit_testimonial['rating'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <?php if ($edit_testimonial): ?>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="active" <?php echo $edit_testimonial['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $edit_testimonial['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_testimonial ? 'Update Testimonial' : 'Add Testimonial'; ?>
                            </button>
                            <?php if ($edit_testimonial): ?>
                                <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Testimonials List -->
                <div class="admin-card">
                    <h2>All Testimonials (<?php echo count($testimonials); ?>)</h2>

                    <?php if (empty($testimonials)): ?>
                        <p>No testimonials found. Add your first testimonial above!</p>
                    <?php else: ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
                            <?php foreach ($testimonials as $testimonial): ?>
                                <div class="testimonial-admin-card" style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; border: 1px solid #dee2e6;">
                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                        <div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; background: #e9ecef;">
                                            <?php if ($testimonial['image']): ?>
                                                <img src="<?php echo htmlspecialchars($testimonial['image']); ?>"
                                                    alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #4a90e2; color: white; font-weight: bold;">
                                                    <?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <h4 style="margin: 0; color: #333;"><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                            <?php if ($testimonial['position'] || $testimonial['company']): ?>
                                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                                    <?php echo htmlspecialchars($testimonial['position']); ?>
                                                    <?php if ($testimonial['position'] && $testimonial['company']): ?> at <?php endif; ?>
                                                    <?php echo htmlspecialchars($testimonial['company']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <div style="margin-top: 0.3rem;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star" style="color: <?php echo $i <= $testimonial['rating'] ? '#ffd700' : '#e0e0e0'; ?>; font-size: 0.8rem;"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge badge-<?php echo $testimonial['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($testimonial['status']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <p style="margin-bottom: 1rem; font-style: italic; color: #555; line-height: 1.5;">
                                        "<?php echo htmlspecialchars($testimonial['message']); ?>"
                                    </p>

                                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #dee2e6; padding-top: 1rem;">
                                        <small style="color: #666;">
                                            Added: <?php echo date('M j, Y', strtotime($testimonial['created_at'])); ?>
                                        </small>
                                        <div>
                                            <a href="testimonials.php?edit=<?php echo $testimonial['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
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
    </style>

    <script src="../script.js"></script>
</body>

</html>