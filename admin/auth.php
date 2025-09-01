<?php
function requireLogin()
{
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit;
    }
}

function getAdminNavigation($current_page = '')
{
    $nav_items = [
        'dashboard.php' => 'Dashboard',
        'projects.php' => 'Projects',
        'skills.php' => 'Skills',
        'achievements.php' => 'Achievements',
        'testimonials.php' => 'Testimonials',
        'messages.php' => 'Messages',
        'logout.php' => 'Logout'
    ];

    $html = '<nav class="admin-nav"><div class="container"><ul>';

    foreach ($nav_items as $page => $title) {
        $active = ($current_page === $page) ? ' class="active"' : '';
        $html .= '<li><a href="' . $page . '"' . $active . '>' . $title . '</a></li>';
    }

    $html .= '</ul></div></nav>';

    return $html;
}

function getAdminHeader($title = 'Admin Panel')
{
    return '
    <header class="admin-header">
        <div class="container">
            <h1>' . htmlspecialchars($title) . '</h1>
            <p>Welcome, ' . htmlspecialchars($_SESSION['admin_username']) . '</p>
        </div>
    </header>';
}
