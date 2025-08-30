<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Get personal info
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Get active social links
$stmt = $conn->prepare("SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order ASC");
$stmt->execute();
$social_links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get education records
$stmt = $conn->prepare("SELECT * FROM education ORDER BY year_start DESC");
$stmt->execute();
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get skills by category
$stmt = $conn->prepare("
    SELECT sc.category_name, sc.icon as category_icon, s.skill_name, s.proficiency_level, s.icon
    FROM skill_categories sc 
    LEFT JOIN skills s ON sc.id = s.category_id 
    ORDER BY sc.sort_order, s.skill_name
");
$stmt->execute();
$skills_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group skills by category
$skills_by_category = [];
foreach ($skills_data as $skill) {
    if ($skill['skill_name']) { // Only add if skill exists
        $skills_by_category[$skill['category_name']][] = $skill;
    }
}

// Get achievements
$stmt = $conn->prepare("SELECT * FROM achievements ORDER BY date_achieved DESC");
$stmt->execute();
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get featured projects
$stmt = $conn->prepare("SELECT * FROM projects ORDER BY featured DESC, created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?> - Portfolio</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo $personal_info ? $personal_info['bio'] : 'Computer Science & Engineering Student | Full Stack Developer'; ?>">
    <meta name="keywords" content="portfolio, developer, computer science, full stack, web development">
    <meta name="author" content="<?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?> - Portfolio">
    <meta property="og:description" content="<?php echo $personal_info ? $personal_info['bio'] : 'Computer Science & Engineering Student | Full Stack Developer'; ?>">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💻</text></svg>">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <ul class="nav-menu" id="nav-menu">
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#education" class="nav-link">Education</a></li>
                <li><a href="#skills" class="nav-link">Skills</a></li>
                <li><a href="#achievements" class="nav-link">Achievements</a></li>
                <li><a href="#projects" class="nav-link">Projects</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>

            <div style="display: flex; align-items: center;">
                <button class="theme-toggle" id="theme-toggle" title="Toggle Theme">
                    <i class="icon-moon"></i>
                </button>
                <button class="nav-toggle" id="nav-toggle">
                    <i class="icon-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Hi, I'm <?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>
                </h1>
                <p class="hero-subtitle">
                    <?php echo $personal_info ? $personal_info['title'] : 'Computer Science & Engineering Student | Full Stack Developer'; ?>
                </p>
                <p class="hero-description">
                    <?php echo $personal_info ? $personal_info['bio'] : 'Passionate about creating innovative solutions through code and technology.'; ?>
                </p>

                <div class="social-links">
                    <?php foreach ($social_links as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>"
                            target="_blank"
                            class="social-link"
                            title="<?php echo htmlspecialchars($link['platform']); ?>"
                            onclick="portfolio.trackSocialClick('<?php echo htmlspecialchars($link['platform']); ?>')">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="hero-actions">
                    <a href="#contact" class="btn btn-primary">
                        <i class="icon-envelope"></i> Get In Touch
                    </a>
                    <a href="#projects" class="btn btn-outline">
                        <i class="icon-eye"></i> View Work
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section id="education" class="section section-alt">
        <div class="container">
            <div class="section-title">
                <h2>Education Journey</h2>
                <p>My academic path and continuous learning experience</p>
            </div>

            <div class="education-timeline">
                <?php foreach ($education as $index => $edu): ?>
                    <div class="education-item" style="animation-delay: <?php echo $index * 0.2; ?>s">
                        <div class="education-icon">
                            <i class="icon-graduation-cap"></i>
                        </div>
                        <div class="education-content">
                            <h3 class="education-degree"><?php echo htmlspecialchars($edu['degree']); ?></h3>
                            <h4 class="education-institution"><?php echo htmlspecialchars($edu['institution']); ?></h4>
                            <div class="education-duration">
                                <i class="icon-calendar"></i>
                                <?php echo htmlspecialchars($edu['duration']); ?>
                                <?php if ($edu['is_current']): ?>
                                    <span class="education-gpa">Current</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($edu['gpa']): ?>
                                <div class="education-gpa">GPA: <?php echo htmlspecialchars($edu['gpa']); ?></div>
                            <?php endif; ?>
                            <?php if ($edu['description']): ?>
                                <p class="education-description"><?php echo htmlspecialchars($edu['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="section">
        <div class="container">
            <div class="section-title">
                <h2>Skills & Expertise</h2>
                <p>Technologies and tools I work with</p>
            </div>

            <div class="grid grid-2">
                <?php foreach ($skills_by_category as $category => $skills): ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <?php if (!empty($skills[0]['category_icon'])): ?>
                                    <i class="<?php echo htmlspecialchars($skills[0]['category_icon']); ?>"></i>
                                <?php else: ?>
                                    <i class="icon-code"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title"><?php echo htmlspecialchars($category); ?></h3>
                        </div>
                        <div class="card-content">
                            <?php foreach ($skills as $skill): ?>
                                <div class="skill-item">
                                    <div class="skill-header">
                                        <span class="skill-name">
                                            <?php if ($skill['icon']): ?>
                                                <i class="<?php echo htmlspecialchars($skill['icon']); ?>"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($skill['skill_name']); ?>
                                        </span>
                                        <span class="skill-level"><?php echo $skill['proficiency_level']; ?>%</span>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="skill-progress-bar" data-width="<?php echo $skill['proficiency_level']; ?>"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section id="achievements" class="section section-alt">
        <div class="container">
            <div class="section-title">
                <h2>Achievements</h2>
                <p>Recognition and accomplishments</p>
            </div>

            <div class="grid grid-2">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="icon-trophy"></i>
                            </div>
                            <div>
                                <h3 class="card-title"><?php echo htmlspecialchars($achievement['title']); ?></h3>
                                <?php if ($achievement['organization']): ?>
                                    <p style="margin: 0; color: var(--primary-color); font-weight: 600;">
                                        <?php echo htmlspecialchars($achievement['organization']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-content">
                            <?php if ($achievement['position']): ?>
                                <div class="achievement-position"><?php echo htmlspecialchars($achievement['position']); ?></div>
                            <?php endif; ?>
                            <?php if ($achievement['date_achieved']): ?>
                                <div class="achievement-date">
                                    <i class="icon-calendar"></i>
                                    <?php echo date('F Y', strtotime($achievement['date_achieved'])); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($achievement['description']): ?>
                                <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="section">
        <div class="container">
            <div class="section-title">
                <h2>Featured Projects</h2>
                <p>Some of the work I'm proud of</p>
            </div>

            <div class="grid grid-2">
                <?php foreach ($projects as $project): ?>
                    <div class="card project-card">
                        <?php if ($project['featured']): ?>
                            <div class="project-featured">
                                <i class="icon-star"></i> Featured
                            </div>
                        <?php endif; ?>

                        <?php if ($project['category']): ?>
                            <div class="project-category"><?php echo htmlspecialchars($project['category']); ?></div>
                        <?php endif; ?>

                        <h3 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h3>

                        <?php if ($project['description']): ?>
                            <p class="project-description"><?php echo htmlspecialchars($project['description']); ?></p>
                        <?php endif; ?>

                        <?php if ($project['technologies']): ?>
                            <p class="project-tech">
                                <strong>Tech Stack:</strong> <?php echo htmlspecialchars($project['technologies']); ?>
                            </p>
                        <?php endif; ?>

                        <div class="project-links">
                            <?php if ($project['github_url']): ?>
                                <a href="<?php echo htmlspecialchars($project['github_url']); ?>"
                                    target="_blank"
                                    class="project-link project-link-github">
                                    <i class="icon-github"></i> Code
                                </a>
                            <?php endif; ?>
                            <?php if ($project['live_url']): ?>
                                <a href="<?php echo htmlspecialchars($project['live_url']); ?>"
                                    target="_blank"
                                    class="project-link project-link-live">
                                    <i class="icon-external-link"></i> Live Demo
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="contact-content">
                <h2 style="color: white; margin-bottom: 1rem;">Let's Work Together</h2>
                <p style="font-size: 1.1rem; margin-bottom: 2rem;">
                    I'm always open to discussing new opportunities, interesting projects,
                    or just having a chat about technology and innovation.
                </p>

                <div class="social-links" style="margin-bottom: 2rem;">
                    <?php foreach ($social_links as $link): ?>
                        <a href="<?php echo htmlspecialchars($link['url']); ?>"
                            target="_blank"
                            class="social-link"
                            title="<?php echo htmlspecialchars($link['platform']); ?>">
                            <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($personal_info && $personal_info['email']): ?>
                    <a href="mailto:<?php echo htmlspecialchars($personal_info['email']); ?>" class="btn btn-outline">
                        <i class="icon-envelope"></i> Send Email
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $personal_info ? htmlspecialchars($personal_info['name']) : 'Arka Braja Prasad Nath'; ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Admin Button -->
    <a href="admin/login.php" class="admin-btn" title="Admin Panel">
        <i class="icon-cog"></i>
    </a>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>

</html>