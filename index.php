<?php
require_once 'config/config.php';

// Get personal information
$personalInfo = $db->fetchOne("SELECT * FROM personal_info WHERE id = 1");
$socialLinks = $db->fetchAll("SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order");
$skillCategories = $db->fetchAll("
    SELECT sc.*, GROUP_CONCAT(s.skill_name ORDER BY s.sort_order SEPARATOR '|') as skills,
           GROUP_CONCAT(s.proficiency_level ORDER BY s.sort_order SEPARATOR '|') as proficiencies
    FROM skill_categories sc 
    LEFT JOIN skills s ON sc.id = s.category_id 
    WHERE sc.is_active = 1 
    GROUP BY sc.id 
    ORDER BY sc.sort_order
");
$education = $db->fetchAll("SELECT * FROM education ORDER BY start_year DESC");
$featuredProjects = $db->fetchAll("SELECT * FROM projects WHERE featured = 1 ORDER BY sort_order LIMIT 6");
$achievements = $db->fetchAll("SELECT * FROM achievements ORDER BY date_achieved DESC LIMIT 8");
$experience = $db->fetchAll("SELECT * FROM experience ORDER BY start_date DESC");

// Get current theme
$currentTheme = $_COOKIE[THEME_COOKIE_NAME] ?? DEFAULT_THEME;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <meta name="description" content="<?php echo sanitize($personalInfo['about_text'] ?? ''); ?>">
    <meta name="keywords" content="Arka Nath, Computer Science, KUET, Full Stack Developer, Machine Learning">
    <meta name="author" content="<?php echo sanitize($personalInfo['name'] ?? ''); ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo SITE_TITLE; ?>">
    <meta property="og:description" content="<?php echo sanitize($personalInfo['about_text'] ?? ''); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader">
            <div class="cube-wrapper">
                <div class="cube-folding">
                    <span class="leaf1"></span>
                    <span class="leaf2"></span>
                    <span class="leaf3"></span>
                    <span class="leaf4"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="#home" class="brand-link">
                    <span class="brand-text"><?php echo strtok($personalInfo['name'], ' '); ?></span>
                </a>
            </div>

            <div class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#home" class="nav-link active">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="#about" class="nav-link">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="#education" class="nav-link">Education</a>
                    </li>
                    <li class="nav-item">
                        <a href="#skills" class="nav-link">Skills</a>
                    </li>
                    <li class="nav-item">
                        <a href="#projects" class="nav-link">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a href="#achievements" class="nav-link">Achievements</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">Contact</a>
                    </li>
                </ul>
            </div>

            <div class="nav-controls">
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-sun sun-icon"></i>
                    <i class="fas fa-moon moon-icon"></i>
                </button>
                <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-background">
            <div class="hero-particles"></div>
        </div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <div class="hero-text">
                    <h1 class="hero-title">
                        Hi, I'm <span class="text-gradient"><?php echo sanitize($personalInfo['name']); ?></span>
                    </h1>
                    <h2 class="hero-subtitle">
                        <span class="typing-text" data-text='["Computer Science Student", "Full Stack Developer", "ML Enthusiast", "Problem Solver"]'></span>
                    </h2>
                    <p class="hero-description">
                        <?php echo sanitize($personalInfo['about_text']); ?>
                    </p>
                    <div class="hero-buttons">
                        <a href="#contact" class="btn btn-primary">
                            <i class="fas fa-envelope"></i>
                            Get In Touch
                        </a>
                        <a href="#projects" class="btn btn-secondary">
                            <i class="fas fa-folder-open"></i>
                            View Projects
                        </a>
                    </div>
                </div>
                <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                    <div class="image-container">
                        <img src="<?php echo getImageUrl($personalInfo['profile_image']); ?>"
                            alt="<?php echo sanitize($personalInfo['name']); ?>"
                            class="profile-image">
                        <div class="image-decoration"></div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="social-links" data-aos="fade-up" data-aos-delay="400">
                <?php foreach ($socialLinks as $social): ?>
                    <a href="<?php echo sanitize($social['url']); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="social-link"
                        aria-label="<?php echo sanitize($social['platform']); ?>">
                        <i class="<?php echo sanitize($social['icon_class']); ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            <span class="scroll-text">Scroll Down</span>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section section-padding">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">Get to know me</span>
                <h2 class="section-title">About Me</h2>
                <p class="section-subtitle">
                    A passionate computer science student with a drive for innovation and excellence
                </p>
            </div>

            <div class="about-content">
                <div class="about-text" data-aos="fade-right">
                    <div class="about-details">
                        <h3>Hello! I'm <?php echo sanitize($personalInfo['name']); ?></h3>
                        <p><?php echo nl2br(sanitize($personalInfo['about_text'])); ?></p>

                        <div class="about-stats">
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo count($featuredProjects); ?>">0</div>
                                <div class="stat-label">Projects Completed</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo count($achievements); ?>">0</div>
                                <div class="stat-label">Achievements</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number" data-count="<?php echo count($skillCategories); ?>">0</div>
                                <div class="stat-label">Skill Categories</div>
                            </div>
                        </div>

                        <?php if ($personalInfo['resume_file']): ?>
                            <div class="about-actions">
                                <a href="<?php echo getImageUrl($personalInfo['resume_file']); ?>"
                                    target="_blank"
                                    class="btn btn-primary">
                                    <i class="fas fa-download"></i>
                                    Download Resume
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="about-visual" data-aos="fade-left" data-aos-delay="200">
                    <div class="timeline-preview">
                        <h4>Quick Timeline</h4>
                        <div class="timeline-items">
                            <?php foreach (array_slice($education, 0, 3) as $edu): ?>
                                <div class="timeline-item">
                                    <div class="timeline-date"><?php echo formatDate($edu['start_year'] . '-01-01'); ?></div>
                                    <div class="timeline-content">
                                        <h5><?php echo sanitize($edu['degree']); ?></h5>
                                        <p><?php echo sanitize($edu['institution']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section id="education" class="education-section section-padding bg-alternate">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">My Journey</span>
                <h2 class="section-title">Education</h2>
                <p class="section-subtitle">
                    My academic journey and educational background
                </p>
            </div>

            <div class="education-timeline">
                <?php foreach ($education as $index => $edu): ?>
                    <div class="timeline-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="timeline-marker">
                            <div class="timeline-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <div class="timeline-date">
                                    <?php echo $edu['start_year']; ?> -
                                    <?php echo $edu['is_current'] ? 'Present' : $edu['end_year']; ?>
                                </div>
                                <?php if ($edu['gpa']): ?>
                                    <div class="timeline-gpa">GPA: <?php echo sanitize($edu['gpa']); ?></div>
                                <?php endif; ?>
                            </div>
                            <h3 class="timeline-title"><?php echo sanitize($edu['degree']); ?></h3>
                            <h4 class="timeline-subtitle"><?php echo sanitize($edu['institution']); ?></h4>
                            <?php if ($edu['location']): ?>
                                <p class="timeline-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo sanitize($edu['location']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($edu['description']): ?>
                                <p class="timeline-description"><?php echo sanitize($edu['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="skills-section section-padding">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">What I know</span>
                <h2 class="section-title">Skills & Technologies</h2>
                <p class="section-subtitle">
                    Technologies and tools I work with
                </p>
            </div>

            <div class="skills-container">
                <?php foreach ($skillCategories as $index => $category):
                    $skills = explode('|', $category['skills']);
                    $proficiencies = explode('|', $category['proficiencies']);
                ?>
                    <div class="skill-category" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="category-header">
                            <h3 class="category-title"><?php echo sanitize($category['category_name']); ?></h3>
                        </div>
                        <div class="skills-grid">
                            <?php for ($i = 0; $i < count($skills); $i++):
                                if (empty($skills[$i])) continue;
                            ?>
                                <div class="skill-item">
                                    <div class="skill-info">
                                        <span class="skill-name"><?php echo sanitize($skills[$i]); ?></span>
                                        <span class="skill-percentage"><?php echo $proficiencies[$i]; ?>%</span>
                                    </div>
                                    <div class="skill-bar">
                                        <div class="skill-progress" data-progress="<?php echo $proficiencies[$i]; ?>"></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="projects-section section-padding bg-alternate">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">My Work</span>
                <h2 class="section-title">Featured Projects</h2>
                <p class="section-subtitle">
                    Some of my recent work and personal projects
                </p>
            </div>

            <div class="projects-grid">
                <?php foreach ($featuredProjects as $index => $project): ?>
                    <div class="project-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-footer" data-aos="fade-up">
                <a href="projects.php" class="btn btn-primary">
                    <i class="fas fa-folder-open"></i>
                    View All Projects
                </a>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section id="achievements" class="achievements-section section-padding">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">Recognition</span>
                <h2 class="section-title">Achievements & Awards</h2>
                <p class="section-subtitle">
                    Milestones and recognition in my journey
                </p>
            </div>

            <div class="achievements-grid">
                <?php foreach ($achievements as $index => $achievement): ?>
                    <div class="achievement-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="achievement-icon">
                            <i class="fas <?php echo $achievement['category'] === 'competition' ? 'fa-trophy' : ($achievement['category'] === 'certification' ? 'fa-certificate' : ($achievement['category'] === 'award' ? 'fa-award' : 'fa-star')); ?>"></i>
                        </div>
                        <div class="achievement-content">
                            <h3 class="achievement-title"><?php echo sanitize($achievement['title']); ?></h3>
                            <?php if ($achievement['organization']): ?>
                                <h4 class="achievement-org"><?php echo sanitize($achievement['organization']); ?></h4>
                            <?php endif; ?>
                            <p class="achievement-description"><?php echo sanitize($achievement['description']); ?></p>
                            <div class="achievement-meta">
                                <?php if ($achievement['position']): ?>
                                    <span class="achievement-position"><?php echo sanitize($achievement['position']); ?></span>
                                <?php endif; ?>
                                <?php if ($achievement['date_achieved']): ?>
                                    <span class="achievement-date"><?php echo formatDate($achievement['date_achieved'], 'M Y'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section section-padding bg-alternate">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-label">Get in touch</span>
                <h2 class="section-title">Contact Me</h2>
                <p class="section-subtitle">
                    Let's discuss opportunities and collaborations
                </p>
            </div>

            <div class="contact-content">
                <div class="contact-info" data-aos="fade-right">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p><?php echo sanitize($personalInfo['email']); ?></p>
                        </div>
                    </div>

                    <?php if ($personalInfo['phone']): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone</h4>
                                <p><?php echo sanitize($personalInfo['phone']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($personalInfo['location']): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Location</h4>
                                <p><?php echo sanitize($personalInfo['location']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="contact-social">
                        <h4>Follow Me</h4>
                        <div class="social-links-grid">
                            <?php foreach ($socialLinks as $social): ?>
                                <a href="<?php echo sanitize($social['url']); ?>"
                                    target="_blank"
                                    class="social-link">
                                    <i class="<?php echo sanitize($social['icon_class']); ?>"></i>
                                    <span><?php echo sanitize($social['platform']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container" data-aos="fade-left" data-aos-delay="200">
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject">
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo sanitize($personalInfo['name']); ?></h3>
                    <p>Computer Science & Engineering Student</p>
                    <div class="footer-social">
                        <?php foreach ($socialLinks as $social): ?>
                            <a href="<?php echo sanitize($social['url']); ?>"
                                target="_blank"
                                class="social-link">
                                <i class="<?php echo sanitize($social['icon_class']); ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#about">About</a></li>
                        <li><a href="#skills">Skills</a></li>
                        <li><a href="#projects">Projects</a></li>
                        <li><a href="#achievements">Achievements</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="footer-contact">
                        <p><i class="fas fa-envelope"></i> <?php echo sanitize($personalInfo['email']); ?></p>
                        <?php if ($personalInfo['location']): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo sanitize($personalInfo['location']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo sanitize($personalInfo['name']); ?>. All rights reserved.</p>
                <p>Built with <i class="fas fa-heart text-primary"></i> using PHP, MySQL & JavaScript</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>