<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Get personal info
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Get active social links
$stmt = $conn->prepare("SELECT * FROM social_links WHERE is_active = 1 ORDER BY created_at");
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?> - Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section-title {
            position: relative;
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .skill-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .skill-card:hover {
            transform: translateY(-5px);
        }

        .skill-item {
            margin-bottom: 1.5rem;
        }

        .skill-progress {
            height: 8px;
            border-radius: 10px;
            background: #f0f0f0;
            overflow: hidden;
        }

        .skill-progress-bar {
            height: 100%;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            transition: width 1s ease-in-out;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            margin: 0 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .social-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            color: white;
        }

        .education-card,
        .achievement-card,
        .project-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .education-card:hover,
        .achievement-card:hover,
        .project-card:hover {
            transform: translateY(-5px);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .admin-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            border-radius: 50px;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .admin-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            color: white;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .floating-shapes span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            animation: animate 25s linear infinite;
            bottom: -150px;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        .section-padding {
            padding: 5rem 0;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-code"></i>
                <?php echo $personal_info ? explode(' ', $personal_info['name'])[0] : 'Arka'; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#education">Education</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#skills">Skills</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#achievements">Achievements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#projects">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="floating-shapes">
            <span style="left: 10%; animation-delay: 0s;"></span>
            <span style="left: 20%; animation-delay: 2s;"></span>
            <span style="left: 30%; animation-delay: 4s;"></span>
            <span style="left: 40%; animation-delay: 6s;"></span>
            <span style="left: 50%; animation-delay: 8s;"></span>
            <span style="left: 60%; animation-delay: 10s;"></span>
            <span style="left: 70%; animation-delay: 12s;"></span>
            <span style="left: 80%; animation-delay: 14s;"></span>
            <span style="left: 90%; animation-delay: 16s;"></span>
        </div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4">
                        Hi, I'm <?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>
                    </h1>
                    <h3 class="mb-4">
                        <?php echo $personal_info ? $personal_info['title'] : 'Computer Science & Engineering Student'; ?>
                    </h3>
                    <p class="lead mb-4">
                        <?php echo $personal_info ? $personal_info['bio'] : 'Passionate about creating innovative solutions through code.'; ?>
                    </p>
                    <div class="social-links mb-4">
                        <?php foreach ($social_links as $link): ?>
                            <a href="<?php echo $link['url']; ?>" target="_blank" title="<?php echo $link['platform']; ?>">
                                <i class="<?php echo $link['icon']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="#contact" class="btn btn-primary btn-lg">
                        <i class="fas fa-envelope"></i> Get In Touch
                    </a>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left">
                    <div class="hero-image">
                        <i class="fas fa-user-graduate" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section id="education" class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Education</h2>
            <div class="row">
                <?php foreach ($education as $edu): ?>
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="education-card">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($edu['degree']); ?></h5>
                                    <h6 class="text-primary mb-2"><?php echo htmlspecialchars($edu['institution']); ?></h6>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-calendar"></i> <?php echo htmlspecialchars($edu['duration']); ?>
                                        <?php if ($edu['is_current']): ?>
                                            <span class="badge bg-success ms-2">Current</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($edu['gpa']): ?>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-star"></i> GPA: <?php echo htmlspecialchars($edu['gpa']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($edu['description']): ?>
                                        <p class="mb-0"><?php echo htmlspecialchars($edu['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Skills & Expertise</h2>
            <div class="row">
                <?php foreach ($skills_by_category as $category => $skills): ?>
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="skill-card">
                            <h5 class="mb-4">
                                <?php if (!empty($skills[0]['category_icon'])): ?>
                                    <i class="<?php echo $skills[0]['category_icon']; ?> me-2"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($category); ?>
                            </h5>
                            <?php foreach ($skills as $skill): ?>
                                <div class="skill-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>
                                            <?php if ($skill['icon']): ?>
                                                <i class="<?php echo $skill['icon']; ?> me-2"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($skill['skill_name']); ?>
                                        </span>
                                        <span class="badge bg-primary"><?php echo $skill['proficiency_level']; ?>%</span>
                                    </div>
                                    <div class="skill-progress">
                                        <div class="skill-progress-bar" style="width: <?php echo $skill['proficiency_level']; ?>%"></div>
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
    <section id="achievements" class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Achievements</h2>
            <div class="row">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="achievement-card">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-trophy fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2"><?php echo htmlspecialchars($achievement['title']); ?></h5>
                                    <?php if ($achievement['organization']): ?>
                                        <h6 class="text-primary mb-2"><?php echo htmlspecialchars($achievement['organization']); ?></h6>
                                    <?php endif; ?>
                                    <?php if ($achievement['position']): ?>
                                        <p class="text-success mb-2">
                                            <i class="fas fa-medal"></i> <?php echo htmlspecialchars($achievement['position']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($achievement['date_achieved']): ?>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-calendar"></i> <?php echo date('F Y', strtotime($achievement['date_achieved'])); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($achievement['description']): ?>
                                        <p class="mb-0"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="section-padding">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Projects</h2>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                    <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="project-card">
                            <?php if ($project['featured']): ?>
                                <div class="badge bg-warning position-absolute" style="top: 1rem; right: 1rem;">
                                    <i class="fas fa-star"></i> Featured
                                </div>
                            <?php endif; ?>
                            <h5 class="mb-3"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <?php if ($project['category']): ?>
                                <span class="badge bg-secondary mb-3"><?php echo htmlspecialchars($project['category']); ?></span>
                            <?php endif; ?>
                            <?php if ($project['description']): ?>
                                <p class="mb-3"><?php echo htmlspecialchars($project['description']); ?></p>
                            <?php endif; ?>
                            <?php if ($project['technologies']): ?>
                                <p class="mb-3">
                                    <strong>Technologies:</strong> <?php echo htmlspecialchars($project['technologies']); ?>
                                </p>
                            <?php endif; ?>
                            <div class="d-flex gap-2">
                                <?php if ($project['github_url']): ?>
                                    <a href="<?php echo $project['github_url']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fab fa-github"></i> Code
                                    </a>
                                <?php endif; ?>
                                <?php if ($project['live_url']): ?>
                                    <a href="<?php echo $project['live_url']; ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-external-link-alt"></i> Live Demo
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding bg-primary text-white">
        <div class="container text-center">
            <h2 class="section-title text-white" data-aos="fade-up">Get In Touch</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <p class="lead mb-4">
                        I'm always open to discussing new opportunities, interesting projects, or just having a chat about technology and innovation.
                    </p>
                    <div class="social-links mb-4">
                        <?php foreach ($social_links as $link): ?>
                            <a href="<?php echo $link['url']; ?>" target="_blank" title="<?php echo $link['platform']; ?>">
                                <i class="<?php echo $link['icon']; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($personal_info && $personal_info['email']): ?>
                        <a href="mailto:<?php echo $personal_info['email']; ?>" class="btn btn-light btn-lg">
                            <i class="fas fa-envelope"></i> Send Email
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $personal_info ? $personal_info['name'] : 'Arka Braja Prasad Nath'; ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- Admin Button -->
    <a href="admin/login.php" class="admin-btn">
        <i class="fas fa-cog"></i> Admin
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255,255,255,0.95)';
            } else {
                navbar.style.background = 'rgba(255,255,255,0.95)';
            }
        });
    </script>
</body>

</html>