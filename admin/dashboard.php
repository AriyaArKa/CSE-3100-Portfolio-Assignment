<?php
session_start();
include '../config.php';
include 'auth.php';

requireLogin();

$pdo = getConnection();

// Get statistics
$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn(),
    'skills' => $pdo->query("SELECT COUNT(*) FROM skills WHERE status = 'active'")->fetchColumn(),
    'achievements' => $pdo->query("SELECT COUNT(*) FROM achievements WHERE status = 'active'")->fetchColumn(),
    'testimonials' => $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'active'")->fetchColumn(),
    'messages' => $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn()
];

// Recent messages
$stmt = $pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-layout">
        <?php echo getAdminNavigation('dashboard.php'); ?>

        <div class="admin-main">
            <?php echo getAdminHeader('Admin Dashboard'); ?>

            <div class="admin-container">
                <!-- Statistics Cards -->
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['projects']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Active Projects</p>
                    </div>

                    <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['skills']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Skills</p>
                    </div>

                    <div class="stat-card" style="background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['achievements']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Achievements</p>
                    </div>

                    <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['testimonials']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Testimonials</p>
                    </div>

                    <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['messages']; ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Unread Messages</p>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="admin-card">
                    <h2>Recent Messages</h2>
                    <?php if (empty($recent_messages)): ?>
                        <p>No messages yet.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_messages as $message): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $message['status'] === 'unread' ? 'warning' : 'success'; ?>">
                                                <?php echo ucfirst($message['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="messages.php?view=<?php echo $message['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="messages.php" class="btn btn-primary">View All Messages</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="admin-card">
                    <h2>Quick Actions</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <a href="projects.php?action=add" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-plus"></i><br>Add New Project
                        </a>
                        <a href="skills.php?action=add" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-plus"></i><br>Add New Skill
                        </a>
                        <a href="achievements.php?action=add" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-plus"></i><br>Add Achievement
                        </a>
                        <a href="testimonials.php?action=add" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-plus"></i><br>Add Testimonial
                        </a>
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

            .badge-warning {
                background: #ffc107;
                color: #212529;
            }

            .badge-success {
                background: #28a745;
                color: white;
            }

            .btn-sm {
                padding: 0.3rem 0.8rem;
                font-size: 0.8rem;
            }
        </style>

        <script src="../script.js"></script>
</body>

</html>