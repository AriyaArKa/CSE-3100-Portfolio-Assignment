<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('/admin/login.php');
}

// Handle actions
if ($_POST) {
    try {
        $pdo = getDBConnection();

        if (isset($_POST['mark_read'])) {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $stmt->execute([(int)$_POST['message_id']]);
            $success = "Message marked as read!";
        }

        if (isset($_POST['mark_unread'])) {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?");
            $stmt->execute([(int)$_POST['message_id']]);
            $success = "Message marked as unread!";
        }

        if (isset($_POST['delete_message'])) {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([(int)$_POST['message_id']]);
            $success = "Message deleted successfully!";
        }

        if (isset($_POST['reply_message'])) {
            // In a real implementation, you would send an email here
            $stmt = $pdo->prepare("UPDATE contact_messages SET replied_at = NOW() WHERE id = ?");
            $stmt->execute([(int)$_POST['message_id']]);
            $success = "Reply sent successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch messages
try {
    $pdo = getDBConnection();

    // Build WHERE clause
    $whereClause = "WHERE 1=1";
    $params = [];

    if ($filter === 'unread') {
        $whereClause .= " AND is_read = 0";
    } elseif ($filter === 'read') {
        $whereClause .= " AND is_read = 1";
    } elseif ($filter === 'replied') {
        $whereClause .= " AND replied_at IS NOT NULL";
    }

    if (!empty($search)) {
        $whereClause .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam, $searchParam];
    }

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) FROM contact_messages $whereClause";
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $totalMessages = $stmt->fetchColumn();
    $totalPages = ceil($totalMessages / $limit);

    // Get messages with pagination
    $query = "SELECT * FROM contact_messages $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics
    $stats = [
        'total' => $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
        'unread' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn(),
        'read' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 1")->fetchColumn(),
        'replied' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE replied_at IS NOT NULL")->fetchColumn()
    ];
} catch (Exception $e) {
    $error = "Error fetching messages: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Panel</title>
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
                <li><a href="education.php"><i class="fas fa-graduation-cap"></i> Education</a></li>
                <li><a href="skills.php"><i class="fas fa-code"></i> Skills</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="achievements.php"><i class="fas fa-trophy"></i> Achievements</a></li>
                <li><a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
                <p>Manage and respond to contact form submissions</p>
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

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total Messages</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon unread">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['unread'] ?></h3>
                        <p>Unread Messages</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon read">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['read'] ?></h3>
                        <p>Read Messages</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon replied">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $stats['replied'] ?></h3>
                        <p>Replied Messages</p>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card">
                <div class="card-body">
                    <div class="messages-controls">
                        <div class="filter-tabs">
                            <a href="?filter=all&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                                All (<?= $stats['total'] ?>)
                            </a>
                            <a href="?filter=unread&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'unread' ? 'active' : '' ?>">
                                Unread (<?= $stats['unread'] ?>)
                            </a>
                            <a href="?filter=read&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'read' ? 'active' : '' ?>">
                                Read (<?= $stats['read'] ?>)
                            </a>
                            <a href="?filter=replied&search=<?= urlencode($search) ?>" class="filter-tab <?= $filter === 'replied' ? 'active' : '' ?>">
                                Replied (<?= $stats['replied'] ?>)
                            </a>
                        </div>

                        <div class="search-box">
                            <form method="GET" class="search-form">
                                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search messages...">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages List -->
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($messages)): ?>
                        <div class="messages-list">
                            <?php foreach ($messages as $message): ?>
                                <div class="message-item <?= $message['is_read'] ? 'read' : 'unread' ?>">
                                    <div class="message-header">
                                        <div class="message-sender">
                                            <div class="sender-info">
                                                <h4><?= htmlspecialchars($message['name']) ?></h4>
                                                <p><?= htmlspecialchars($message['email']) ?></p>
                                            </div>
                                            <div class="message-meta">
                                                <span class="message-date"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></span>
                                                <?php if (!$message['is_read']): ?>
                                                    <span class="unread-badge">New</span>
                                                <?php endif; ?>
                                                <?php if ($message['replied_at']): ?>
                                                    <span class="replied-badge">Replied</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="message-actions">
                                            <button onclick="toggleMessage(<?= $message['id'] ?>)" class="btn-icon" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <?php if (!$message['is_read']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                                    <button type="submit" name="mark_read" class="btn-icon" title="Mark as Read">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                                    <button type="submit" name="mark_unread" class="btn-icon" title="Mark as Unread">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <button onclick="replyToMessage(<?= $message['id'] ?>, '<?= htmlspecialchars($message['email']) ?>')" class="btn-icon" title="Reply">
                                                <i class="fas fa-reply"></i>
                                            </button>

                                            <button onclick="deleteMessage(<?= $message['id'] ?>)" class="btn-icon btn-delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="message-subject">
                                        <strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?>
                                    </div>

                                    <div class="message-content" id="message-<?= $message['id'] ?>" style="display: none;">
                                        <div class="message-body">
                                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                                        </div>

                                        <?php if ($message['ip_address']): ?>
                                            <div class="message-footer">
                                                <small><strong>IP Address:</strong> <?= htmlspecialchars($message['ip_address']) ?></small>
                                                <?php if ($message['user_agent']): ?>
                                                    <br><small><strong>User Agent:</strong> <?= htmlspecialchars($message['user_agent']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>" class="pagination-link">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                <?php endif; ?>

                                <span class="pagination-info">
                                    Page <?= $page ?> of <?= $totalPages ?>
                                </span>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>" class="pagination-link">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <h3>No Messages Found</h3>
                            <p>
                                <?php if (!empty($search)): ?>
                                    No messages match your search criteria.
                                <?php elseif ($filter !== 'all'): ?>
                                    No messages in this category.
                                <?php else: ?>
                                    You haven't received any messages yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Delete</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="message_id" id="delete_message_id">
                <div class="modal-footer">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="delete_message" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMessage(messageId) {
            const messageContent = document.getElementById('message-' + messageId);
            if (messageContent.style.display === 'none') {
                messageContent.style.display = 'block';
            } else {
                messageContent.style.display = 'none';
            }
        }

        function replyToMessage(messageId, email) {
            // In a real implementation, this would open an email client or modal
            // For now, we'll use a simple mailto link
            const subject = encodeURIComponent('Re: Your portfolio contact');
            const body = encodeURIComponent('Thank you for contacting me. ');
            window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;

            // Mark as replied
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `reply_message=1&message_id=${messageId}`
            }).then(() => {
                location.reload();
            });
        }

        function deleteMessage(messageId) {
            document.getElementById('delete_message_id').value = messageId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }
    </script>

    <script src="assets/js/admin.js"></script>
</body>

</html>