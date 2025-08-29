<?php
// Password Reset Utility
require_once 'config/database.php';

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        try {
            $database = new Database();
            $conn = $database->getConnection();

            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the admin password
            $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = 'arka_admin'");
            $result = $stmt->execute([$hashed_password]);

            if ($result) {
                $success = "Password updated successfully! You can now login with the new password.";
            } else {
                $error = "Failed to update password in database.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Passwords do not match!";
    }
}

// Quick fix option
if (isset($_GET['quick_fix']) && $_GET['quick_fix'] == 'admin123') {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Set password to admin123
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = 'arka_admin'");
        $result = $stmt->execute([$hashed_password]);

        if ($result) {
            $success = "Password reset to 'admin123' successfully!";
        } else {
            $error = "Failed to reset password.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Portfolio Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .reset-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .reset-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="reset-container">
                    <div class="reset-header p-4 text-center">
                        <h2><i class="fas fa-key"></i> Admin Password Reset</h2>
                        <p class="mb-0">Reset your admin panel password</p>
                    </div>
                    <div class="p-4">

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                <br><br>
                                <a href="admin/login.php" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Login Now
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Quick Fix Option -->
                        <div class="mb-4">
                            <h5><i class="fas fa-bolt"></i> Quick Fix</h5>
                            <p>Reset password to default "admin123":</p>
                            <a href="?quick_fix=admin123" class="btn btn-warning">
                                <i class="fas fa-magic"></i> Reset to "admin123"
                            </a>
                        </div>

                        <hr>

                        <!-- Custom Password Form -->
                        <h5><i class="fas fa-cog"></i> Set Custom Password</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock"></i> New Password
                                </label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirm Password
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                        </form>

                        <!-- Current Database Status -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6><i class="fas fa-info-circle"></i> Current Status</h6>
                            <?php
                            try {
                                $database = new Database();
                                $conn = $database->getConnection();

                                $stmt = $conn->prepare("SELECT username, created_at FROM admin WHERE username = 'arka_admin'");
                                $stmt->execute();
                                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($admin) {
                                    echo '<p class="mb-1"><strong>Username:</strong> ' . htmlspecialchars($admin['username']) . '</p>';
                                    echo '<p class="mb-0"><strong>Account Created:</strong> ' . htmlspecialchars($admin['created_at']) . '</p>';
                                } else {
                                    echo '<p class="text-danger">No admin user found in database!</p>';
                                }
                            } catch (Exception $e) {
                                echo '<p class="text-danger">Database connection error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                            }
                            ?>
                        </div>

                        <!-- Navigation -->
                        <div class="mt-4 d-flex gap-2">
                            <a href="test_connection.php" class="btn btn-info">
                                <i class="fas fa-vial"></i> Test Connection
                            </a>
                            <a href="admin/login.php" class="btn btn-success">
                                <i class="fas fa-sign-in-alt"></i> Login Page
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Portfolio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>