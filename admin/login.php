<?php
require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

if ($_POST) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Get user from database
        $user = $db->fetchOne(
            "SELECT * FROM admin_users WHERE username = ? AND is_active = 1",
            [$username]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];

            // Update last login
            $db->update(
                'admin_users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = ?',
                [$user['id']]
            );

            redirect(ADMIN_URL . '/index.php');
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            background: var(--surface-color);
            border-radius: 16px;
            padding: 3rem;
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--border-color);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }

        .login-title {
            margin: 0 0 0.5rem 0;
            color: var(--text-color);
        }

        .login-subtitle {
            color: var(--text-secondary);
            margin: 0;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .form-input {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--bg-color);
            color: var(--text-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .input-group .form-input {
            padding-left: 2.5rem;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 0.75rem;
            color: #dc2626;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        [data-theme="dark"] .error-message {
            background: #451a1a;
            border-color: #dc2626;
            color: #fca5a5;
        }

        .login-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        [data-theme="dark"] .theme-toggle {
            background: rgba(0, 0, 0, 0.9);
        }

        .theme-toggle:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
    </style>
</head>

<body>
    <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-sun sun-icon"></i>
        <i class="fas fa-moon moon-icon" style="display: none;"></i>
    </button>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">Access your portfolio dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo sanitize($error); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        placeholder="Enter your username"
                        value="<?php echo sanitize($_POST['username'] ?? ''); ?>"
                        required
                        autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i>
                Back to Portfolio
            </a>
        </div>
    </div>

    <script>
        // Theme toggle
        document.getElementById('themeToggle').addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin_theme', newTheme);

            // Update icons
            const sunIcon = this.querySelector('.sun-icon');
            const moonIcon = this.querySelector('.moon-icon');

            if (newTheme === 'dark') {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        });

        // Load saved theme
        const savedTheme = localStorage.getItem('admin_theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        if (savedTheme === 'dark') {
            document.querySelector('.sun-icon').style.display = 'none';
            document.querySelector('.moon-icon').style.display = 'block';
        }

        // Form validation
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                alert('Please enter both username and password');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            submitBtn.disabled = true;
        });

        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>

</html>