<?php
require_once 'config/config.php';

// Get filter parameters
$type = $_GET['type'] ?? 'all';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Build query conditions
$conditions = ['1=1'];
$params = [];

if ($type !== 'all') {
    $conditions[] = 'project_type = ?';
    $params[] = $type;
}

if (!empty($search)) {
    $conditions[] = '(title LIKE ? OR description LIKE ? OR technologies LIKE ?)';
    $searchTerm = '%' . $search . '%';
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = implode(' AND ', $conditions);

// Get total count
$totalCount = $db->fetchOne(
    "SELECT COUNT(*) as count FROM projects WHERE $whereClause",
    $params
)['count'];

$totalPages = ceil($totalCount / $perPage);

// Get projects
$projects = $db->fetchAll(
    "SELECT * FROM projects WHERE $whereClause ORDER BY sort_order, created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$perPage, $offset])
);

// Get project types for filter
$projectTypes = $db->fetchAll("SELECT DISTINCT project_type FROM projects ORDER BY project_type");

// Get personal info for header
$personalInfo = $db->fetchOne("SELECT * FROM personal_info WHERE id = 1");
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_COOKIE[THEME_COOKIE_NAME] ?? DEFAULT_THEME; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - <?php echo SITE_TITLE; ?></title>
    <meta name="description" content="Browse all projects by <?php echo sanitize($personalInfo['name']); ?>">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .projects-header {
            background: var(--gradient-primary);
            color: white;
            padding: 6rem 0 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .projects-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .projects-header-content {
            position: relative;
            z-index: 1;
        }

        .projects-filters {
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            padding: 2rem 0;
            position: sticky;
            top: 80px;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .filters-container {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-label {
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.875rem;
        }

        .type-filters {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .type-filter {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            background: var(--bg-color);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .type-filter:hover,
        .type-filter.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-left: auto;
        }

        .search-input {
            border: none;
            background: none;
            color: var(--text-color);
            font-size: 0.875rem;
            width: 200px;
        }

        .search-input:focus {
            outline: none;
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .projects-content {
            padding: 3rem 0;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .project-card {
            background: var(--surface-color);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .project-image {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: var(--bg-secondary);
        }

        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .project-card:hover .project-image img {
            transform: scale(1.05);
        }

        .project-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .project-card:hover .project-overlay {
            opacity: 1;
        }

        .project-links {
            display: flex;
            gap: 1rem;
        }

        .project-link {
            width: 45px;
            height: 45px;
            background: white;
            color: var(--text-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .project-link:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .project-content {
            padding: 1.5rem;
        }

        .project-type {
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .project-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--text-color);
        }

        .project-description {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .project-technologies {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tech-tag {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid var(--border-color);
        }

        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 3rem;
        }

        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--surface-color);
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination-link:hover,
        .pagination-link.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-link:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .back-button:hover {
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navigation (simplified for this page) -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php" class="brand-link">
                    <span class="brand-text"><?php echo strtok($personalInfo['name'], ' '); ?></span>
                </a>
            </div>

            <div class="nav-controls">
                <button class="theme-toggle" id="theme-toggle">
                    <i class="fas fa-sun sun-icon"></i>
                    <i class="fas fa-moon moon-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="projects-header">
        <div class="container">
            <div class="projects-header-content">
                <a href="index.php#projects" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Back to Portfolio
                </a>
                <h1>My Projects</h1>
                <p>Explore my work and personal projects across different technologies</p>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <section class="projects-filters">
        <div class="container">
            <div class="filters-container">
                <div class="filter-group">
                    <span class="filter-label">Filter by type:</span>
                    <div class="type-filters">
                        <a href="?type=all<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                            class="type-filter <?php echo $type === 'all' ? 'active' : ''; ?>">
                            All
                        </a>
                        <?php foreach ($projectTypes as $projectType): ?>
                            <a href="?type=<?php echo urlencode($projectType['project_type']); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                class="type-filter <?php echo $type === $projectType['project_type'] ? 'active' : ''; ?>">
                                <?php echo ucfirst($projectType['project_type']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <form method="GET" class="search-box">
                    <?php if ($type !== 'all'): ?>
                        <input type="hidden" name="type" value="<?php echo sanitize($type); ?>">
                    <?php endif; ?>
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="Search projects..."
                        value="<?php echo sanitize($search); ?>">
                </form>
            </div>
        </div>
    </section>

    <!-- Projects Content -->
    <section class="projects-content">
        <div class="container">
            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3>No projects found</h3>
                    <p>
                        <?php if (!empty($search) || $type !== 'all'): ?>
                            Try adjusting your filters or search terms.
                        <?php else: ?>
                            Projects will appear here once they are added.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $project): ?>
                        <div class="project-card">
                            <div class="project-image">
                                <img src="<?php echo getImageUrl($project['image']); ?>"
                                    alt="<?php echo sanitize($project['title']); ?>"
                                    loading="lazy">
                                <div class="project-overlay">
                                    <div class="project-links">
                                        <?php if ($project['github_url']): ?>
                                            <a href="<?php echo sanitize($project['github_url']); ?>"
                                                target="_blank"
                                                class="project-link"
                                                aria-label="View source code">
                                                <i class="fab fa-github"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project['live_url']): ?>
                                            <a href="<?php echo sanitize($project['live_url']); ?>"
                                                target="_blank"
                                                class="project-link"
                                                aria-label="View live demo">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <div class="project-type"><?php echo ucfirst($project['project_type']); ?></div>
                                <h3 class="project-title"><?php echo sanitize($project['title']); ?></h3>
                                <p class="project-description"><?php echo sanitize($project['description']); ?></p>

                                <?php if ($project['technologies']): ?>
                                    <div class="project-technologies">
                                        <?php
                                        $techs = explode(',', $project['technologies']);
                                        foreach ($techs as $tech):
                                        ?>
                                            <span class="tech-tag"><?php echo trim(sanitize($tech)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="project-meta">
                                    <span>Created <?php echo timeAgo($project['created_at']); ?></span>
                                    <?php if ($project['featured']): ?>
                                        <span class="tech-tag" style="background: var(--accent-color); color: white;">Featured</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $type !== 'all' ? '&type=' . urlencode($type) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                class="pagination-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?><?php echo $type !== 'all' ? '&type=' . urlencode($type) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $type !== 'all' ? '&type=' . urlencode($type) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                class="pagination-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>

                        <span class="pagination-info">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                            (<?php echo $totalCount; ?> projects)
                        </span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo sanitize($personalInfo['name']); ?></h3>
                    <p>Computer Science & Engineering Student</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize($personalInfo['name']); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="assets/js/main.js"></script>
</body>

</html>