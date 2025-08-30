<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if personal info exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM personal_info");
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($count > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE personal_info SET name=?, title=?, bio=?, profile_image=?, phone=?, email=?, location=? WHERE id = 1");
        $stmt->execute([
            $_POST['name'],
            $_POST['title'],
            $_POST['bio'],
            $_POST['profile_image'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['location']
        ]);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO personal_info (name, title, bio, profile_image, phone, email, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['title'],
            $_POST['bio'],
            $_POST['profile_image'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['location']
        ]);
    }

    $success = "Personal information updated successfully!";
}

// Get personal info
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal_info = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Info Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/assets/css/admin.css/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .preview-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
        }

        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center text-white mb-4">
                    <h4><i class="icon-user-cog"></i> Admin Panel</h4>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="icon-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="personal_info.php">
                        <i class="icon-user"></i> Personal Info
                    </a>
                    <a class="nav-link" href="education.php">
                        <i class="icon-graduation-cap"></i> Education
                    </a>
                    <a class="nav-link" href="skills.php">
                        <i class="icon-code"></i> Skills
                    </a>
                    <a class="nav-link" href="achievements.php">
                        <i class="icon-trophy"></i> Achievements
                    </a>
                    <a class="nav-link" href="projects.php">
                        <i class="icon-project-diagram"></i> Projects
                    </a>
                    <a class="nav-link" href="social_links.php">
                        <i class="icon-share-alt"></i> Social Links
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="logout.php">
                        <i class="icon-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="icon-user"></i> Personal Information</h2>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="icon-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="icon-check"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Form -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="icon-edit"></i> Edit Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="<?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?php echo $personal_info ? $personal_info['email'] : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Professional Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="<?php echo $personal_info ? $personal_info['title'] : 'Computer Science & Engineering Student | Full Stack Developer'; ?>"
                                            placeholder="e.g., Full Stack Developer, Software Engineer">
                                    </div>

                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio/Description</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"
                                            placeholder="Tell visitors about yourself, your experience, and what you're passionate about..."><?php echo $personal_info ? $personal_info['bio'] : 'Passionate Computer Science & Engineering student at KUET with expertise in full-stack development, machine learning, and mobile app development. Experienced in multiple programming languages and frameworks.'; ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="<?php echo $personal_info ? $personal_info['phone'] : ''; ?>"
                                                placeholder="+880 1234 567890">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control" id="location" name="location"
                                                value="<?php echo $personal_info ? $personal_info['location'] : 'Khulna, Bangladesh'; ?>"
                                                placeholder="City, Country">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Profile Image URL</label>
                                        <input type="url" class="form-control" id="profile_image" name="profile_image"
                                            value="<?php echo $personal_info ? $personal_info['profile_image'] : ''; ?>"
                                            placeholder="https://example.com/profile-image.jpg"
                                            onchange="updatePreview()">
                                        <div class="form-text">Leave empty to use default avatar</div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="icon-eye"></i> Preview</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="preview-card text-center">
                                    <div class="mb-3">
                                        <?php if ($personal_info && $personal_info['profile_image']): ?>
                                            <img src="<?php echo $personal_info['profile_image']; ?>"
                                                alt="Profile Image" class="profile-preview" id="profilePreview">
                                        <?php else: ?>
                                            <div class="profile-preview mx-auto d-flex align-items-center justify-content-center bg-light text-dark"
                                                id="profilePreview" style="font-size: 3rem;">
                                                <i class="icon-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <h4 id="namePreview">
                                        <?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>
                                    </h4>

                                    <p class="mb-2" id="titlePreview">
                                        <?php echo $personal_info ? $personal_info['title'] : 'Computer Science & Engineering Student | Full Stack Developer'; ?>
                                    </p>

                                    <?php if ($personal_info && $personal_info['location']): ?>
                                        <p class="mb-2" id="locationPreview">
                                            <i class="icon-map-marker-alt"></i>
                                            <?php echo $personal_info['location']; ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($personal_info && $personal_info['email']): ?>
                                        <p class="mb-0" id="emailPreview">
                                            <i class="icon-envelope"></i>
                                            <?php echo $personal_info['email']; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <?php if ($personal_info && $personal_info['bio']): ?>
                                    <div class="p-3">
                                        <h6>Bio:</h6>
                                        <p class="text-muted" id="bioPreview">
                                            <?php echo htmlspecialchars($personal_info['bio']); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h6><i class="icon-info-circle"></i> Quick Tips</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="icon-lightbulb text-warning"></i>
                                        Use a professional profile image for better impression
                                    </li>
                                    <li class="mb-2">
                                        <i class="icon-lightbulb text-warning"></i>
                                        Keep your bio concise but informative
                                    </li>
                                    <li class="mb-0">
                                        <i class="icon-lightbulb text-warning"></i>
                                        Update your information regularly
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/assets/css/admin.css/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePreview() {
            // Update name
            const name = document.getElementById('name').value || 'Arka Braja Prasad Nath';
            document.getElementById('namePreview').textContent = name;

            // Update title
            const title = document.getElementById('title').value || 'Computer Science & Engineering Student';
            document.getElementById('titlePreview').textContent = title;

            // Update bio
            const bio = document.getElementById('bio').value;
            const bioPreview = document.getElementById('bioPreview');
            if (bioPreview) {
                bioPreview.textContent = bio;
            }

            // Update location
            const location = document.getElementById('location').value;
            const locationPreview = document.getElementById('locationPreview');
            if (locationPreview) {
                locationPreview.innerHTML = '<i class="icon-map-marker-alt"></i> ' + location;
            }

            // Update email
            const email = document.getElementById('email').value;
            const emailPreview = document.getElementById('emailPreview');
            if (emailPreview) {
                emailPreview.innerHTML = '<i class="icon-envelope"></i> ' + email;
            }

            // Update profile image
            const profileImage = document.getElementById('profile_image').value;
            const profilePreview = document.getElementById('profilePreview');
            if (profileImage) {
                profilePreview.innerHTML = '<img src="' + profileImage + '" alt="Profile Image" class="profile-preview">';
            } else {
                profilePreview.innerHTML = '<i class="icon-user"></i>';
                profilePreview.className = 'profile-preview mx-auto d-flex align-items-center justify-content-center bg-light text-dark';
                profilePreview.style.fontSize = '3rem';
            }
        }

        // Add event listeners for real-time preview
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('bio').addEventListener('input', updatePreview);
        document.getElementById('location').addEventListener('input', updatePreview);
        document.getElementById('email').addEventListener('input', updatePreview);
        document.getElementById('profile_image').addEventListener('input', updatePreview);
    </script>
</body>

</html>
