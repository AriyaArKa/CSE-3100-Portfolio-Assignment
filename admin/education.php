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

        if (isset($_POST['add_education'])) {
            $stmt = $pdo->prepare("INSERT INTO education (institution, degree, field_of_study, start_date, end_date, description, grade, location, is_current) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                sanitize($_POST['institution']),
                sanitize($_POST['degree']),
                sanitize($_POST['field_of_study']),
                $_POST['start_date'],
                $_POST['end_date'] ?: null,
                sanitize($_POST['description']),
                sanitize($_POST['grade']),
                sanitize($_POST['location']),
                isset($_POST['is_current']) ? 1 : 0
            ]);
            $success = "Education entry added successfully!";
        }

        if (isset($_POST['update_education'])) {
            $stmt = $pdo->prepare("UPDATE education SET institution = ?, degree = ?, field_of_study = ?, start_date = ?, end_date = ?, description = ?, grade = ?, location = ?, is_current = ? WHERE id = ?");
            $stmt->execute([
                sanitize($_POST['institution']),
                sanitize($_POST['degree']),
                sanitize($_POST['field_of_study']),
                $_POST['start_date'],
                $_POST['end_date'] ?: null,
                sanitize($_POST['description']),
                sanitize($_POST['grade']),
                sanitize($_POST['location']),
                isset($_POST['is_current']) ? 1 : 0,
                (int)$_POST['education_id']
            ]);
            $success = "Education entry updated successfully!";
        }

        if (isset($_POST['delete_education'])) {
            $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
            $stmt->execute([(int)$_POST['education_id']]);
            $success = "Education entry deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch education data
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM education ORDER BY start_date DESC");
    $educationList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching education data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Management - Admin Panel</title>
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
                <li><a href="education.php" class="active"><i class="fas fa-graduation-cap"></i> Education</a></li>
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
                <h1><i class="fas fa-graduation-cap"></i> Education Management</h1>
                <p>Manage your educational background and qualifications</p>
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
                <!-- Add New Education Form -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-plus"></i> Add New Education</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="admin-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="institution">Institution Name</label>
                                    <input type="text" id="institution" name="institution" required>
                                </div>
                                <div class="form-group">
                                    <label for="degree">Degree/Level</label>
                                    <input type="text" id="degree" name="degree" required placeholder="e.g., Bachelor's, Master's, High School">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="field_of_study">Field of Study</label>
                                    <input type="text" id="field_of_study" name="field_of_study" required placeholder="e.g., Computer Science Engineering">
                                </div>
                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input type="text" id="location" name="location" placeholder="City, Country">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date">
                                    <small class="form-help">Leave empty if currently studying</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="grade">Grade/GPA</label>
                                    <input type="text" id="grade" name="grade" placeholder="e.g., 3.8/4.0, First Class">
                                </div>
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_current">
                                        <span class="checkmark"></span>
                                        Currently Studying
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description/Activities</label>
                                <textarea id="description" name="description" rows="3" placeholder="Relevant coursework, activities, achievements, etc."></textarea>
                            </div>

                            <button type="submit" name="add_education" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Education
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Education List -->
                <div class="card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Education History</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($educationList)): ?>
                            <div class="education-timeline">
                                <?php foreach ($educationList as $education): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            <?= date('Y', strtotime($education['start_date'])) ?> -
                                            <?= $education['is_current'] ? 'Present' : date('Y', strtotime($education['end_date'])) ?>
                                            <?php if ($education['is_current']): ?>
                                                <span class="current-badge">Current</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="timeline-content">
                                            <div class="education-header">
                                                <h4><?= htmlspecialchars($education['degree']) ?> in <?= htmlspecialchars($education['field_of_study']) ?></h4>
                                                <div class="education-actions">
                                                    <button onclick="editEducation(<?= $education['id'] ?>)" class="btn-icon btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteEducation(<?= $education['id'] ?>)" class="btn-icon btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="education-institution">
                                                <i class="fas fa-university"></i>
                                                <?= htmlspecialchars($education['institution']) ?>
                                                <?php if ($education['location']): ?>
                                                    <span class="location"><?= htmlspecialchars($education['location']) ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($education['grade']): ?>
                                                <div class="education-grade">
                                                    <i class="fas fa-award"></i>
                                                    Grade: <?= htmlspecialchars($education['grade']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($education['description']): ?>
                                                <div class="education-description">
                                                    <?= nl2br(htmlspecialchars($education['description'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-graduation-cap"></i>
                                <h3>No Education Records Yet</h3>
                                <p>Start by adding your educational background using the form above.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Education Modal -->
    <div id="editEducationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Education</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="education_id" id="edit_education_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_institution">Institution Name</label>
                        <input type="text" id="edit_institution" name="institution" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_degree">Degree/Level</label>
                        <input type="text" id="edit_degree" name="degree" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_field_of_study">Field of Study</label>
                        <input type="text" id="edit_field_of_study" name="field_of_study" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_location">Location</label>
                        <input type="text" id="edit_location" name="location">
                    </div>
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
                        <label for="edit_grade">Grade/GPA</label>
                        <input type="text" id="edit_grade" name="grade">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_current" id="edit_is_current">
                            <span class="checkmark"></span>
                            Currently Studying
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_description">Description/Activities</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="update_education" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Education
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
            <p>Are you sure you want to delete this education record? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="education_id" id="delete_education_id">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="delete_education" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editEducation(educationId) {
            <?php if (!empty($educationList)): ?>
                const educationData = <?= json_encode($educationList) ?>;
                const education = educationData.find(e => e.id == educationId);

                if (education) {
                    document.getElementById('edit_education_id').value = education.id;
                    document.getElementById('edit_institution').value = education.institution;
                    document.getElementById('edit_degree').value = education.degree;
                    document.getElementById('edit_field_of_study').value = education.field_of_study;
                    document.getElementById('edit_location').value = education.location || '';
                    document.getElementById('edit_start_date').value = education.start_date;
                    document.getElementById('edit_end_date').value = education.end_date || '';
                    document.getElementById('edit_grade').value = education.grade || '';
                    document.getElementById('edit_description').value = education.description || '';
                    document.getElementById('edit_is_current').checked = education.is_current == 1;

                    document.getElementById('editEducationModal').style.display = 'block';
                }
            <?php endif; ?>
        }

        function deleteEducation(educationId) {
            document.getElementById('delete_education_id').value = educationId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editEducationModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editEducationModal');
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }

        // Handle current education checkbox
        document.getElementById('is_current').addEventListener('change', function() {
            const endDateField = document.getElementById('end_date');
            if (this.checked) {
                endDateField.value = '';
                endDateField.disabled = true;
            } else {
                endDateField.disabled = false;
            }
        });

        document.getElementById('edit_is_current').addEventListener('change', function() {
            const endDateField = document.getElementById('edit_end_date');
            if (this.checked) {
                endDateField.value = '';
                endDateField.disabled = true;
            } else {
                endDateField.disabled = false;
            }
        });
    </script>

    <script src="assets/js/admin.js"></script>
</body>

</html>