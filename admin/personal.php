<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('/admin/login.php');
}

// Handle form submission
if ($_POST) {
    try {
        $pdo = getDBConnection();

        if (isset($_POST['update_personal'])) {
            $stmt = $pdo->prepare("UPDATE personal_info SET 
                full_name = ?, 
                title = ?, 
                bio = ?, 
                email = ?, 
                phone = ?, 
                location = ?, 
                linkedin = ?, 
                github = ?, 
                twitter = ?,
                resume_url = ?
                WHERE id = 1");

            $stmt->execute([
                sanitize($_POST['full_name']),
                sanitize($_POST['title']),
                sanitize($_POST['bio']),
                sanitize($_POST['email']),
                sanitize($_POST['phone']),
                sanitize($_POST['location']),
                sanitize($_POST['linkedin']),
                sanitize($_POST['github']),
                sanitize($_POST['twitter']),
                sanitize($_POST['resume_url'])
            ]);

            $success = "Personal information updated successfully!";
        }

        if (isset($_POST['update_hero'])) {
            $stmt = $pdo->prepare("UPDATE hero_section SET 
                headline = ?, 
                subtitle = ?, 
                description = ?, 
                cta_text = ?, 
                cta_link = ?
                WHERE id = 1");

            $stmt->execute([
                sanitize($_POST['headline']),
                sanitize($_POST['subtitle']),
                sanitize($_POST['description']),
                sanitize($_POST['cta_text']),
                sanitize($_POST['cta_link'])
            ]);

            $success = "Hero section updated successfully!";
        }
    } catch (Exception $e) {
        $error = "Error updating information: " . $e->getMessage();
    }
}

// Fetch current data
try {
    $pdo = getDBConnection();

    // Get personal info
    $stmt = $pdo->query("SELECT * FROM personal_info WHERE id = 1");
    $personal = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get hero section
    $stmt = $pdo->query("SELECT * FROM hero_section WHERE id = 1");
    $hero = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information - Admin Panel</title>
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
                <li><a href="personal.php" class="active"><i class="fas fa-user"></i> Personal Info</a></li>
                <li><a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a></li>
                <li><a href="skills.php"><i class="fas fa-code"></i> Skills</a></li>
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
                <h1><i class="fas fa-user"></i> Personal Information</h1>
                <p>Manage your personal details and hero section content</p>
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
                <!-- Personal Information Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Personal Information</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name"
                                        value="<?= htmlspecialchars($personal['full_name'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="title">Professional Title</label>
                                    <input type="text" id="title" name="title"
                                        value="<?= htmlspecialchars($personal['title'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bio">Bio/About</label>
                                <textarea id="bio" name="bio" rows="4" required><?= htmlspecialchars($personal['bio'] ?? '') ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?= htmlspecialchars($personal['email'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?= htmlspecialchars($personal['phone'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" id="location" name="location"
                                    value="<?= htmlspecialchars($personal['location'] ?? '') ?>">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="linkedin">LinkedIn URL</label>
                                    <input type="url" id="linkedin" name="linkedin"
                                        value="<?= htmlspecialchars($personal['linkedin'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="github">GitHub URL</label>
                                    <input type="url" id="github" name="github"
                                        value="<?= htmlspecialchars($personal['github'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="twitter">Twitter URL</label>
                                    <input type="url" id="twitter" name="twitter"
                                        value="<?= htmlspecialchars($personal['twitter'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="resume_url">Resume URL</label>
                                    <input type="url" id="resume_url" name="resume_url"
                                        value="<?= htmlspecialchars($personal['resume_url'] ?? '') ?>">
                                </div>
                            </div>

                            <button type="submit" name="update_personal" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Personal Info
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Hero Section Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-home"></i> Hero Section</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-group">
                                <label for="headline">Headline</label>
                                <input type="text" id="headline" name="headline"
                                    value="<?= htmlspecialchars($hero['headline'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="subtitle">Subtitle</label>
                                <input type="text" id="subtitle" name="subtitle"
                                    value="<?= htmlspecialchars($hero['subtitle'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="3" required><?= htmlspecialchars($hero['description'] ?? '') ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cta_text">Call-to-Action Text</label>
                                    <input type="text" id="cta_text" name="cta_text"
                                        value="<?= htmlspecialchars($hero['cta_text'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cta_link">Call-to-Action Link</label>
                                    <input type="text" id="cta_link" name="cta_link"
                                        value="<?= htmlspecialchars($hero['cta_link'] ?? '') ?>">
                                </div>
                            </div>

                            <button type="submit" name="update_hero" class="btn btn-secondary">
                                <i class="fas fa-save"></i> Update Hero Section
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-eye"></i> Preview</h3>
                </div>
                <div class="card-body">
                    <div class="preview-box">
                        <div class="hero-preview">
                            <h1><?= htmlspecialchars($hero['headline'] ?? 'Your Headline') ?></h1>
                            <h2><?= htmlspecialchars($hero['subtitle'] ?? 'Your Subtitle') ?></h2>
                            <p><?= htmlspecialchars($hero['description'] ?? 'Your description') ?></p>
                            <?php if (!empty($hero['cta_text'])): ?>
                                <button class="btn btn-primary"><?= htmlspecialchars($hero['cta_text']) ?></button>
                            <?php endif; ?>
                        </div>

                        <div class="about-preview">
                            <h3>About <?= htmlspecialchars($personal['full_name'] ?? 'Your Name') ?></h3>
                            <p><strong><?= htmlspecialchars($personal['title'] ?? 'Your Title') ?></strong></p>
                            <p><?= htmlspecialchars($personal['bio'] ?? 'Your bio') ?></p>

                            <div class="contact-info">
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($personal['email'] ?? 'your-email@example.com') ?></p>
                                <?php if (!empty($personal['phone'])): ?>
                                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($personal['phone']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($personal['location'])): ?>
                                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($personal['location']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>

</html>