<?php
function requireLogin()
{
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit;
    }
}

function getAdminNavigation($currentPage = '')
{
    $navItems = [
        'dashboard.php' => ['title' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt'],
        'projects.php' => ['title' => 'Projects', 'icon' => 'fas fa-project-diagram'],
        'skills.php' => ['title' => 'Skills', 'icon' => 'fas fa-code'],
        'achievements.php' => ['title' => 'Achievements', 'icon' => 'fas fa-trophy'],
        'testimonials.php' => ['title' => 'Testimonials', 'icon' => 'fas fa-quote-right'],
        'messages.php' => ['title' => 'Messages', 'icon' => 'fas fa-envelope'],
        'logout.php' => ['title' => 'Logout', 'icon' => 'fas fa-sign-out-alt']
    ];

    $html = '<nav class="admin-sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-user-shield"></i> Admin Panel</h3>
                </div>
                <ul class="sidebar-menu">';

    foreach ($navItems as $page => $item) {
        $active = ($currentPage === $page) ? ' class="active"' : '';
        $html .= '<li><a href="' . $page . '"' . $active . '>
                    <i class="' . $item['icon'] . '"></i>
                    <span>' . $item['title'] . '</span>
                  </a></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}

function getAdminHeader($title = 'Admin Panel', $adminUsername = '')
{
    return '
    <header class="admin-header">
        <div class="container">
            <h1>' . htmlspecialchars($title) . '</h1>
            <p>Welcome, ' . htmlspecialchars($adminUsername) . '</p>
        </div>
    </header>';
}
