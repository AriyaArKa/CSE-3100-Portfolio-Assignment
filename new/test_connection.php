<?php
// Database Connection Test Page
require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .test-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .test-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .status-success {
            color: #28a745;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .status-error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }

        .status-warning {
            color: #856404;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="test-container">
                    <div class="test-header p-4 text-center">
                        <h2><i class="fas fa-database"></i> Database Connection Test</h2>
                        <p class="mb-0">Testing portfolio database connectivity and setup</p>
                    </div>
                    <div class="p-4">

                        <!-- Test 1: Basic Database Connection -->
                        <h4><i class="fas fa-plug"></i> 1. Database Connection Test</h4>
                        <?php
                        try {
                            $database = new Database();
                            $conn = $database->getConnection();

                            if ($conn) {
                                echo '<div class="status-success">';
                                echo '<i class="fas fa-check-circle"></i> <strong>SUCCESS:</strong> Database connection established successfully!<br>';
                                echo '<small>Connected to: localhost/portfolio_db</small>';
                                echo '</div>';
                            } else {
                                echo '<div class="status-error">';
                                echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> Failed to connect to database';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<div class="status-error">';
                            echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . $e->getMessage();
                            echo '</div>';
                        }
                        ?>

                        <!-- Test 2: Database Exists -->
                        <h4><i class="fas fa-database"></i> 2. Database Existence Check</h4>
                        <?php
                        try {
                            $testConn = new PDO("mysql:host=localhost", "root", "arka");
                            $testConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stmt = $testConn->prepare("SHOW DATABASES LIKE 'portfolio_db'");
                            $stmt->execute();
                            $result = $stmt->fetch();

                            if ($result) {
                                echo '<div class="status-success">';
                                echo '<i class="fas fa-check-circle"></i> <strong>SUCCESS:</strong> Database "portfolio_db" exists';
                                echo '</div>';
                            } else {
                                echo '<div class="status-warning">';
                                echo '<i class="fas fa-exclamation-triangle"></i> <strong>WARNING:</strong> Database "portfolio_db" does not exist<br>';
                                echo '<small>You need to run the setup first. <a href="complete_setup.php">Click here to run setup</a></small>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<div class="status-error">';
                            echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . $e->getMessage();
                            echo '</div>';
                        }
                        ?>

                        <!-- Test 3: Admin Table Check -->
                        <h4><i class="fas fa-table"></i> 3. Admin Table Check</h4>
                        <?php
                        try {
                            if ($conn) {
                                $stmt = $conn->prepare("SHOW TABLES LIKE 'admin'");
                                $stmt->execute();
                                $result = $stmt->fetch();

                                if ($result) {
                                    echo '<div class="status-success">';
                                    echo '<i class="fas fa-check-circle"></i> <strong>SUCCESS:</strong> Admin table exists';
                                    echo '</div>';

                                    // Check if admin user exists
                                    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM admin WHERE username = 'arka_admin'");
                                    $stmt->execute();
                                    $adminResult = $stmt->fetch();

                                    if ($adminResult['count'] > 0) {
                                        echo '<div class="status-success">';
                                        echo '<i class="fas fa-user-check"></i> <strong>SUCCESS:</strong> Admin user "arka_admin" exists';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="status-warning">';
                                        echo '<i class="fas fa-user-times"></i> <strong>WARNING:</strong> Admin user "arka_admin" not found<br>';
                                        echo '<small>You may need to run the database setup. <a href="complete_setup.php">Click here to run setup</a></small>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="status-warning">';
                                    echo '<i class="fas fa-exclamation-triangle"></i> <strong>WARNING:</strong> Admin table does not exist<br>';
                                    echo '<small>Database tables are missing. <a href="complete_setup.php">Click here to run setup</a></small>';
                                    echo '</div>';
                                }
                            }
                        } catch (Exception $e) {
                            echo '<div class="status-error">';
                            echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . $e->getMessage();
                            echo '</div>';
                        }
                        ?>

                        <!-- Test 4: MySQL Service Check -->
                        <h4><i class="fas fa-server"></i> 4. MySQL Service Status</h4>
                        <?php
                        try {
                            $testConn = new PDO("mysql:host=localhost", "root", "arka");
                            echo '<div class="status-success">';
                            echo '<i class="fas fa-check-circle"></i> <strong>SUCCESS:</strong> MySQL service is running';
                            echo '</div>';
                        } catch (Exception $e) {
                            echo '<div class="status-error">';
                            echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> MySQL service issue: ' . $e->getMessage();
                            echo '<br><small>Make sure XAMPP MySQL service is started</small>';
                            echo '</div>';
                        }
                        ?>

                        <!-- Test 5: Password Hash Check -->
                        <h4><i class="fas fa-key"></i> 5. Password Hash Verification</h4>
                        <?php
                        if ($conn) {
                            try {
                                $stmt = $conn->prepare("SELECT password FROM admin WHERE username = 'arka_admin'");
                                $stmt->execute();
                                $admin = $stmt->fetch();

                                if ($admin) {
                                    $testPassword = 'admin123';
                                    if (password_verify($testPassword, $admin['password'])) {
                                        echo '<div class="status-success">';
                                        echo '<i class="fas fa-check-circle"></i> <strong>SUCCESS:</strong> Password hash verification works<br>';
                                        echo '<small>Default password "admin123" should work</small>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="status-warning">';
                                        echo '<i class="fas fa-exclamation-triangle"></i> <strong>WARNING:</strong> Password hash mismatch<br>';
                                        echo '<small>The stored password hash doesn\'t match "admin123"</small>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="status-warning">';
                                    echo '<i class="fas fa-user-times"></i> <strong>WARNING:</strong> No admin user found';
                                    echo '</div>';
                                }
                            } catch (Exception $e) {
                                echo '<div class="status-error">';
                                echo '<i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . $e->getMessage();
                                echo '</div>';
                            }
                        }
                        ?>

                        <!-- Quick Actions -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5><i class="fas fa-tools"></i> Quick Actions</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="complete_setup.php" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Run Complete Setup
                                </a>
                                <a href="admin/login.php" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Try Login Again
                                </a>
                                <a href="index.php" class="btn btn-info">
                                    <i class="fas fa-home"></i> View Portfolio
                                </a>
                            </div>
                        </div>

                        <!-- Credentials Info -->
                        <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded">
                            <h6><i class="fas fa-info-circle"></i> Default Login Credentials</h6>
                            <p class="mb-1"><strong>Username:</strong> arka_admin</p>
                            <p class="mb-0"><strong>Password:</strong> admin123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>