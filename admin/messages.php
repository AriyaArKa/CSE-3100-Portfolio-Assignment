<?php
session_start();
include '../config.php';
include 'auth.php';

requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle actions
if ($_POST) {
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Message marked as read.';
        } else {
            $error = 'Error updating message status.';
        }
    } elseif ($action === 'mark_unread') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE messages SET status = 'unread' WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Message marked as unread.';
        } else {
            $error = 'Error updating message status.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Message deleted successfully.';
        } else {
            $error = 'Error deleting message.';
        }
    }
}

// Get message for viewing
$view_message = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$_GET['view']]);
    $view_message = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mark as read when viewing
    if ($view_message && $view_message['status'] === 'unread') {
        $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$_GET['view']]);
        $view_message['status'] = 'read';
    }
}

// Get all messages
$filter = $_GET['filter'] ?? 'all';
$where_clause = '';
if ($filter === 'unread') {
    $where_clause = "WHERE status = 'unread'";
} elseif ($filter === 'read') {
    $where_clause = "WHERE status = 'read'";
}

$stmt = $pdo->prepare("SELECT * FROM messages $where_clause ORDER BY created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$total_messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();
$read_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'read'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php echo getAdminHeader('Manage Messages'); ?>
    <?php echo getAdminNavigation('messages.php'); ?>

    <div class="admin-container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Message Statistics -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                <h3 style="margin: 0; font-size: 2rem;"><?php echo $total_messages; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Total Messages</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                <h3 style="margin: 0; font-size: 2rem;"><?php echo $unread_messages; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Unread Messages</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                <h3 style="margin: 0; font-size: 2rem;"><?php echo $read_messages; ?></h3>
                <p style="margin: 0; opacity: 0.9;">Read Messages</p>
            </div>
        </div>

        <?php if ($view_message): ?>
            <!-- View Message Detail -->
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Message Details</h2>
                    <a href="messages.php" class="btn btn-secondary">← Back to Messages</a>
                </div>

                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; border-left: 4px solid #4a90e2;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #333;">From:</h4>
                            <p style="margin: 0; font-size: 1.1rem;"><strong><?php echo htmlspecialchars($view_message['name']); ?></strong></p>
                            <p style="margin: 0; color: #666;"><?php echo htmlspecialchars($view_message['email']); ?></p>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #333;">Received:</h4>
                            <p style="margin: 0;"><?php echo date('F j, Y \a\t g:i A', strtotime($view_message['created_at'])); ?></p>
                            <span class="badge badge-<?php echo $view_message['status'] === 'unread' ? 'warning' : 'success'; ?>">
                                <?php echo ucfirst($view_message['status']); ?>
                            </span>
                        </div>
                    </div>

                    <h4 style="margin: 0 0 1rem 0; color: #333;">Message:</h4>
                    <div style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #dee2e6;">
                        <p style="margin: 0; line-height: 1.6; white-space: pre-wrap;"><?php echo htmlspecialchars($view_message['message']); ?></p>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                        <?php if ($view_message['status'] === 'unread'): ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="id" value="<?php echo $view_message['id']; ?>">
                                <button type="submit" class="btn btn-success">Mark as Read</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="action" value="mark_unread">
                                <input type="hidden" name="id" value="<?php echo $view_message['id']; ?>">
                                <button type="submit" class="btn btn-warning">Mark as Unread</button>
                            </form>
                        <?php endif; ?>

                        <a href="mailto:<?php echo htmlspecialchars($view_message['email']); ?>?subject=Re: Your message from portfolio" class="btn btn-primary">
                            <i class="fas fa-reply"></i> Reply via Email
                        </a>

                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $view_message['id']; ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Messages List -->
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>All Messages</h2>
                    <div style="display: flex; gap: 1rem;">
                        <a href="messages.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">
                            All (<?php echo $total_messages; ?>)
                        </a>
                        <a href="messages.php?filter=unread" class="btn <?php echo $filter === 'unread' ? 'btn-primary' : 'btn-secondary'; ?>">
                            Unread (<?php echo $unread_messages; ?>)
                        </a>
                        <a href="messages.php?filter=read" class="btn <?php echo $filter === 'read' ? 'btn-primary' : 'btn-secondary'; ?>">
                            Read (<?php echo $read_messages; ?>)
                        </a>
                    </div>
                </div>

                <?php if (empty($messages)): ?>
                    <p>No messages found<?php echo $filter !== 'all' ? ' for the selected filter' : ''; ?>.</p>
                <?php else: ?>
                    <div style="display: grid; gap: 1rem;">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-card" style="background: <?php echo $msg['status'] === 'unread' ? '#fff3cd' : '#f8f9fa'; ?>; padding: 1.5rem; border-radius: 10px; border-left: 4px solid <?php echo $msg['status'] === 'unread' ? '#ffc107' : '#28a745'; ?>;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 0.5rem 0; color: #333;">
                                            <?php echo htmlspecialchars($msg['name']); ?>
                                            <span class="badge badge-<?php echo $msg['status'] === 'unread' ? 'warning' : 'success'; ?>" style="margin-left: 1rem;">
                                                <?php echo ucfirst($msg['status']); ?>
                                            </span>
                                        </h4>
                                        <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                            <span style="margin-left: 1rem;">
                                                <i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <p style="margin: 0; color: #555; line-height: 1.5;">
                                    <?php echo htmlspecialchars(substr($msg['message'], 0, 200)) . (strlen($msg['message']) > 200 ? '...' : ''); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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

        .badge-warning {
            background: #ffc107;
            color: #212529;
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

        .message-card {
            transition: transform 0.2s ease;
        }

        .message-card:hover {
            transform: translateY(-2px);
        }
    </style>

    <script src="../script.js"></script>
</body>

</html>