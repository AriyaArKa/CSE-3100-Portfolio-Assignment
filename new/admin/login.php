<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Arka's Portfolio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 400px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 2rem 1rem;
        }

        .login-header h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 2rem;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #666;
            font-size: 0.85rem;
        }

        .text-decoration-none {
            text-decoration: none;
            color: #667eea;
            transition: color 0.3s ease;
        }

        .text-decoration-none:hover {
            color: #764ba2;
        }

        .icon {
            margin-right: 0.5rem;
        }

        /* Font Awesome alternative - using Unicode symbols */
        .fa-user-shield::before {
            content: "🛡️";
        }

        .fa-exclamation-triangle::before {
            content: "⚠️";
        }

        .fa-user::before {
            content: "👤";
        }

        .fa-lock::before {
            content: "🔒";
        }

        .fa-sign-in-alt::before {
            content: "🔑";
        }

        .fa-arrow-left::before {
            content: "←";
        }

        @media (max-width: 480px) {
            .container {
                max-width: 350px;
            }

            .card-body {
                padding: 1.5rem;
            }

            .login-header {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <h3><span class="fa-user-shield"></span> Admin Login</h3>
                <p>Portfolio Admin Panel</p>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert">
                        <span class="fa-exclamation-triangle"></span> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <span class="fa-user"></span> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <span class="fa-lock"></span> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span class="fa-sign-in-alt"></span> Login
                    </button>
                </form>

                <div class="text-center" style="margin-top: 1rem;">
                    <small class="text-muted">
                        Default: arka_admin / admin123
                    </small>
                </div>

                <div class="text-center" style="margin-top: 1rem;">
                    <a href="../index.php" class="text-decoration-none">
                        <span class="fa-arrow-left"></span> Back to Portfolio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
