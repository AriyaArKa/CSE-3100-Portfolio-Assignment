<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Setup - Complete Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .setup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .setup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .step {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .step:last-child {
            border-bottom: none;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            margin: 1rem 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="setup-container">
                    <div class="setup-header">
                        <h1><i class="fas fa-rocket"></i> Arka's Dynamic Portfolio Setup</h1>
                        <p class="mb-0">Complete installation and configuration guide</p>
                    </div>

                    <div class="p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Welcome!</strong> This setup will help you configure your dynamic portfolio with admin panel.
                        </div>

                        <!-- Step 1: Prerequisites -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3">1</div>
                                <div class="flex-grow-1">
                                    <h5>Prerequisites Check</h5>
                                    <p>Make sure you have the following installed and running:</p>
                                    <ul>
                                        <li><strong>XAMPP</strong> - Apache and MySQL services started</li>
                                        <li><strong>PHP 7.4+</strong> - Required for the application</li>
                                        <li><strong>MySQL</strong> - Database server</li>
                                    </ul>

                                    <?php
                                    // Check PHP version
                                    if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
                                        echo '<div class="success"><i class="fas fa-check"></i> PHP version ' . PHP_VERSION . ' ✓</div>';
                                    } else {
                                        echo '<div class="error"><i class="fas fa-times"></i> PHP version ' . PHP_VERSION . ' (Required: 7.4+)</div>';
                                    }

                                    // Check if PDO is available
                                    if (extension_loaded('pdo')) {
                                        echo '<div class="success"><i class="fas fa-check"></i> PDO extension available ✓</div>';
                                    } else {
                                        echo '<div class="error"><i class="fas fa-times"></i> PDO extension not found</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Database Setup -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3">2</div>
                                <div class="flex-grow-1">
                                    <h5>Database Configuration</h5>
                                    <p>Choose one of the following methods to set up your database:</p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><i class="fas fa-magic"></i> Option 1: Automatic Setup</h6>
                                            <p>Click the button below to automatically create the database and tables:</p>
                                            <a href="?auto_setup=1" class="btn btn-primary">
                                                <i class="fas fa-play"></i> Run Auto Setup
                                            </a>

                                            <?php
                                            if (isset($_GET['auto_setup'])) {
                                                try {
                                                    require_once 'config/database.php';
                                                    $database = new Database();

                                                    // Create database
                                                    if ($database->createDatabase()) {
                                                        echo '<div class="success mt-3"><i class="fas fa-check"></i> Database created successfully!</div>';

                                                        // Get connection and run setup
                                                        $conn = $database->getConnection();
                                                        if ($conn) {
                                                            // Read and execute the SQL file
                                                            $sql = file_get_contents('database_setup.sql');
                                                            if ($sql) {
                                                                // Split SQL into individual queries
                                                                $queries = explode(';', $sql);
                                                                $successful = 0;

                                                                foreach ($queries as $query) {
                                                                    $query = trim($query);
                                                                    if (!empty($query) && !preg_match('/^--/', $query)) {
                                                                        try {
                                                                            $conn->exec($query);
                                                                            $successful++;
                                                                        } catch (Exception $e) {
                                                                            // Skip errors for existing data
                                                                        }
                                                                    }
                                                                }

                                                                echo '<div class="success"><i class="fas fa-check"></i> Database setup completed! (' . $successful . ' queries executed)</div>';
                                                                echo '<div class="alert alert-success"><strong>Setup Complete!</strong> You can now proceed to step 3.</div>';
                                                            }
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<div class="error"><i class="fas fa-times"></i> Error: ' . $e->getMessage() . '</div>';
                                                    echo '<div class="alert alert-warning">Please try the manual setup method below.</div>';
                                                }
                                            }
                                            ?>
                                        </div>

                                        <div class="col-md-6">
                                            <h6><i class="fas fa-code"></i> Option 2: Manual Setup</h6>
                                            <p>If automatic setup fails, follow these steps:</p>
                                            <ol>
                                                <li>Open phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
                                                <li>Create database: <code>portfolio_db</code></li>
                                                <li>Import the SQL file: <code>database_setup.sql</code></li>
                                            </ol>
                                            <a href="database_setup.sql" download class="btn btn-outline-secondary">
                                                <i class="fas fa-download"></i> Download SQL File
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Configuration -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3">3</div>
                                <div class="flex-grow-1">
                                    <h5>Database Configuration</h5>
                                    <p>Make sure your database credentials are correct in <code>config/database.php</code>:</p>
                                    <div class="code-block">
                                        <strong>Database Host:</strong> localhost<br>
                                        <strong>Database Name:</strong> portfolio_db<br>
                                        <strong>Username:</strong> root<br>
                                        <strong>Password:</strong> arka
                                    </div>
                                    <p class="text-muted">If your MySQL password is different, update it in the database.php file.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Admin Access -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3">4</div>
                                <div class="flex-grow-1">
                                    <h5>Admin Panel Access</h5>
                                    <p>Your default admin credentials:</p>
                                    <div class="code-block">
                                        <strong>Username:</strong> arka_admin<br>
                                        <strong>Password:</strong> admin123
                                    </div>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Security:</strong> Change the default password after first login!
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5: Access Links -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3">5</div>
                                <div class="flex-grow-1">
                                    <h5>Access Your Portfolio</h5>
                                    <p>After setup is complete, you can access:</p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-eye fa-2x text-primary mb-3"></i>
                                                    <h6>Portfolio Website</h6>
                                                    <a href="index.php" class="btn btn-primary btn-sm" target="_blank">
                                                        <i class="fas fa-external-link-alt"></i> View Portfolio
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-cog fa-2x text-success mb-3"></i>
                                                    <h6>Admin Panel</h6>
                                                    <a href="admin/login.php" class="btn btn-success btn-sm" target="_blank">
                                                        <i class="fas fa-sign-in-alt"></i> Admin Login
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features Overview -->
                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number me-3"><i class="fas fa-star"></i></div>
                                <div class="flex-grow-1">
                                    <h5>Portfolio Features</h5>
                                    <p>Your portfolio includes all these management sections:</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Personal Information</li>
                                                <li><i class="fas fa-check text-success"></i> Education Management</li>
                                                <li><i class="fas fa-check text-success"></i> Skills & Categories</li>
                                                <li><i class="fas fa-check text-success"></i> Achievements</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Projects Portfolio</li>
                                                <li><i class="fas fa-check text-success"></i> Social Links</li>
                                                <li><i class="fas fa-check text-success"></i> Certificates</li>
                                                <li><i class="fas fa-check text-success"></i> Work Experience</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Gallery Management</li>
                                                <li><i class="fas fa-check text-success"></i> Responsive Design</li>
                                                <li><i class="fas fa-check text-success"></i> Database Security</li>
                                                <li><i class="fas fa-check text-success"></i> Admin Dashboard</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Support -->
                        <div class="step bg-light">
                            <div class="text-center">
                                <h5><i class="fas fa-life-ring text-primary"></i> Need Help?</h5>
                                <p>If you encounter any issues during setup:</p>
                                <ol class="text-start">
                                    <li>Make sure XAMPP Apache and MySQL are running</li>
                                    <li>Check that your MySQL password is 'arka' or update config/database.php</li>
                                    <li>Ensure PHP version is 7.4 or higher</li>
                                    <li>Try the manual database setup if automatic fails</li>
                                </ol>
                                <div class="mt-3">
                                    <a href="README.md" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-book"></i> View Documentation
                                    </a>
                                    <a href="database_setup.sql" download class="btn btn-outline-secondary">
                                        <i class="fas fa-download"></i> Download SQL
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>