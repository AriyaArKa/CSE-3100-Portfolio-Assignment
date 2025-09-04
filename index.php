<?php
include 'config.php';

// Fetch data from database
$pdo = getConnection();

// Fetch projects
$stmt = $pdo->prepare("SELECT * FROM projects WHERE status = 'active' ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills grouped by category
$stmt = $pdo->prepare("SELECT * FROM skills WHERE status = 'active' ORDER BY category, skill_name");
$stmt->execute();
$all_skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
$skills = [];
foreach ($all_skills as $skill) {
    $skills[$skill['category']][] = $skill;
}

// Fetch achievements
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE status = 'active' ORDER BY date_achieved DESC");
$stmt->execute();
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch testimonials
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE status = 'active' ORDER BY created_at DESC");
$stmt->execute();
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle contact form submission
if ($_POST && isset($_POST['contact_submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $message])) {
            $success_message = "Thank you for your message! I'll get back to you soon.";
        } else {
            $error_message = "Sorry, there was an error sending your message. Please try again.";
        }
    } else {
        $error_message = "Please fill all fields with valid information.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arka Braja Prasad Nath - Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <a href="#home">Arka Nath</a>
                </div>
                <div class="nav-menu-wrapper">
                    <ul class="nav-menu">
                        <li><a href="#about" class="nav-link">About</a></li>
                        <li><a href="#projects" class="nav-link">Projects</a></li>
                        <li><a href="#skills" class="nav-link">Skills</a></li>
                        <li><a href="#achievements" class="nav-link">Achievements</a></li>
                        <li><a href="#contact" class="nav-link">Contact</a></li>
                    </ul>
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Arka Braja Prasad Nath</h1>
                <p class="hero-subtitle">Computer Science & Engineering Student | Developer | AI & ML Enthusiast</p>
                <div class="hero-links">
                    <a href="https://github.com/AriyaArKa" target="_blank" class="hero-link">
                        <i class="fab fa-github"></i> GitHub
                    </a>
                    <a href="https://www.kaggle.com/arkaariya" target="_blank" class="hero-link">
                        <i class="fab fa-kaggle"></i> Kaggle
                    </a>
                    <a href="https://www.linkedin.com/in/arka-nath55/" target="_blank" class="hero-link">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                </div>
                <a href="#about" class="cta-button">Learn More About Me</a>
            </div>
            <div class="hero-image">
                <div class="profile-image">
                    <img src="https://via.placeholder.com/300x300/4a90e2/ffffff?text=AN" alt="Arka Nath">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">The Marauder's Map</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>I am a passionate Computer Science & Engineering student at KUET with a strong foundation in programming, AI/ML, and full-stack development. I love creating innovative solutions and am always eager to learn new technologies.</p>
                    <p>My journey in technology started early, and I've been consistently developing my skills through various projects, competitions, and hands-on experiences. I believe in continuous learning and staying updated with the latest technological advancements.</p>
                    <p>When I'm not coding, I enjoy participating in hackathons, contributing to open-source projects, and sharing knowledge with the developer community. I'm particularly interested in artificial intelligence, machine learning, and their applications in solving real-world problems.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="projects">
        <div class="container">
            <h2 class="section-title">The Spellbook</h2>
            <div class="projects-slider-wrapper">
                <div class="projects-slider">
                    <?php foreach ($projects as $index => $project): ?>
                        <div class="project-slide" data-index="<?php echo $index; ?>">
                            <div class="project-card">
                                <div class="project-image">
                                    <img src="<?php echo $project['image'] ?: 'https://via.placeholder.com/300x200/4a90e2/ffffff?text=Project'; ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                </div>
                                <div class="project-content">
                                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                                    <div class="project-tech">
                                        <?php
                                        $techs = explode(',', $project['technologies']);
                                        foreach ($techs as $tech):
                                            $tech = trim($tech);
                                            if (!empty($tech)):
                                        ?>
                                                <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                    <div class="project-links">
                                        <?php if ($project['github_link']): ?>
                                            <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" class="project-link">
                                                <i class="fab fa-github"></i> GitHub
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project['demo_link']): ?>
                                            <a href="<?php echo htmlspecialchars($project['demo_link']); ?>" target="_blank" class="project-link">
                                                <i class="fas fa-external-link-alt"></i> Demo
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider-dots">
                    <?php foreach ($projects as $index => $project): ?>
                        <button class="slider-dot" onclick="goToSlide(<?php echo $index; ?>)"></button>
                    <?php endforeach; ?>
                </div>
                <button class="slider-nav prev" onclick="previousSlide()"><i class="fas fa-chevron-left"></i></button>
                <button class="slider-nav next" onclick="nextSlide()"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="skills">
        <div class="container">
            <h2 class="section-title">Wand Armory</h2>
            <div class="skills-container">
                <div class="skill-categories-nav">
                    <?php $first = true;
                    foreach ($skills as $category => $categorySkills): ?>
                        <button class="category-btn <?php echo $first ? 'active' : ''; ?>" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </button>
                    <?php $first = false;
                    endforeach; ?>
                </div>

                <?php $first = true;
                foreach ($skills as $category => $categorySkills): ?>
                    <div class="skill-category <?php echo $first ? 'active' : ''; ?>" id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                        <div class="skills-grid">
                            <?php foreach ($categorySkills as $skill): ?>
                                <div class="skill-card">
                                    <div class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                                    <div class="skill-progress-circle">
                                        <svg class="progress-ring" width="60" height="60">
                                            <circle class="progress-ring-circle" cx="30" cy="30" r="22"></circle>
                                            <circle class="progress-ring-progress" cx="30" cy="30" r="22"
                                                style="stroke-dasharray: <?php echo 2 * 3.14159 * 22 * ($skill['proficiency'] / 100); ?> <?php echo 2 * 3.14159 * 22; ?>;"></circle>
                                        </svg>
                                        <div class="skill-percent"><?php echo $skill['proficiency']; ?>%</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $first = false;
                endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section id="achievements" class="achievements">
        <div class="container">
            <h2 class="section-title magical-title">Gringotts Vault</h2>
            <div class="achievements-grid">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="achievement-flip-card">
                        <div class="flip-card-inner">
                            <!-- Front of Card -->
                            <div class="flip-card-front">
                                <div class="card-border">
                                    <div class="card-corner top-left"></div>
                                    <div class="card-corner top-right"></div>
                                    <div class="card-corner bottom-left"></div>
                                    <div class="card-corner bottom-right"></div>

                                    <?php if (!empty($achievement['image'])): ?>
                                        <div class="achievement-image">
                                            <img src="<?php echo htmlspecialchars($achievement['image']); ?>"
                                                alt="<?php echo htmlspecialchars($achievement['title']); ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="achievement-icon">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-title">
                                        <h3><?php echo htmlspecialchars($achievement['title']); ?></h3>
                                    </div>

                                    <div class="card-frame"></div>
                                </div>
                            </div>

                            <!-- Back of Card -->
                            <div class="flip-card-back">
                                <div class="card-border">
                                    <div class="card-corner top-left"></div>
                                    <div class="card-corner top-right"></div>
                                    <div class="card-corner bottom-left"></div>
                                    <div class="card-corner bottom-right"></div>

                                    <div class="parchment-content">
                                        <div class="achievement-details">
                                            <h3><?php echo htmlspecialchars($achievement['title']); ?></h3>
                                            <p class="achievement-description"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                            <div class="achievement-meta">
                                                <span class="achievement-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?php echo date('F Y', strtotime($achievement['date_achieved'])); ?>
                                                </span>
                                                <?php if (!empty($achievement['category'])): ?>
                                                    <span class="achievement-category">
                                                        <i class="fas fa-tag"></i>
                                                        <?php echo htmlspecialchars($achievement['category']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-frame"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Extracurricular Activities Section -->
    <section id="extracurricular" class="extracurricular-section">
        <div class="container">
            <h2 class="section-title">The Room of Requirement</h2>
            <div class="activities-grid">
                <div class="activity-card">
                    <h4>Senior Executive</h4>
                    <p>KUET Business & Entrepreneurship Club (KBEC)</p>
                </div>
                <div class="activity-card">
                    <h4>Sub Autonomous-Trainee</h4>
                    <p>Team DURBAR (Mars Rover project)</p>
                </div>
                <div class="activity-card">
                    <h4>Design Member</h4>
                    <p>Bit to Byte (R&D Community)</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Education Section -->
    <section id="education" class="education-section">
        <div class="container">
            <h2 class="section-title">Hogwarts Records</h2>
            <div class="education-grid">
                <div class="education-card">
                    <div class="education-year">2023 – Present</div>
                    <div class="education-content">
                        <h3>Bachelor of Science in Computer Science & Engineering</h3>
                        <h4>Khulna University of Engineering & Technology (KUET)</h4>
                        <p>Currently pursuing my undergraduate degree with a focus on software engineering, artificial intelligence, and machine learning. Actively involved in various programming competitions and technical projects.</p>
                    </div>
                </div>
                <div class="education-card">
                    <div class="education-year">2021</div>
                    <div class="education-content">
                        <h3>Higher Secondary Certificate</h3>
                        <h4>Engineering University School & College, Dhaka</h4>
                        <p><strong>Group:</strong> Science | <strong>GPA:</strong> 5.00/5.00</p>
                        <p>Completed higher secondary education with excellent academic performance, laying a strong foundation in mathematics, physics, and chemistry.</p>
                    </div>
                </div>
                <div class="education-card">
                    <div class="education-year">2019</div>
                    <div class="education-content">
                        <h3>Secondary School Certificate</h3>
                        <h4>Motijheel Govt. Boys' High School, Dhaka</h4>
                        <p><strong>Group:</strong> Science | <strong>GPA:</strong> 5.00/5.00</p>
                        <p>Achieved perfect GPA in secondary education with strong performance in science subjects, demonstrating early interest in technology and problem-solving.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-feather-alt"></i>
                The Daily Prophet
            </h2>
            <div class="testimonials-slider-container">
                <button class="slider-btn prev-btn" onclick="changeTestimonialSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="testimonials-slider">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <div class="testimonial-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index + 1; ?>">
                            <div class="testimonial-card">
                                <div class="parchment-note">
                                    <div class="note-content">
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-empty'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="testimonial-text">
                                            <p>"<?php echo htmlspecialchars($testimonial['message']); ?>"</p>
                                        </div>
                                        <div class="testimonial-author">
                                            <div class="author-image">
                                                <img src="<?php echo $testimonial['image'] ?: 'https://via.placeholder.com/60x60/8b4513/ffffff?text=' . substr($testimonial['name'], 0, 1); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                            </div>
                                            <div class="author-info">
                                                <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                                <p><?php echo htmlspecialchars($testimonial['position']); ?><?php echo $testimonial['company'] ? ' at ' . htmlspecialchars($testimonial['company']) : ''; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="slider-dots">
                        <?php foreach ($testimonials as $index => $testimonial): ?>
                            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentTestimonialSlide(<?php echo $index + 1; ?>)"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="slider-btn next-btn" onclick="changeTestimonialSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-envelope"></i>
                Send Me an Owl
            </h2>
            <div class="contact-content">
                <div class="contact-info">
                    <h2 class="magical-title">Get in Touch</h2>
                    <p class="magical-subtitle">Ready to embark on a magical coding journey together? Send me a message!</p>

                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>YOUR.EMAIL@HOGWARTS.EDU</span>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-linkedin"></i>
                            <span>LINKEDIN.COM/IN/YOURNAME</span>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-github"></i>
                            <span>GITHUB.COM/YOURNAME</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-error"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="#contact" class="magical-form">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="YOUR NAME" required>
                            <i class="fas fa-user form-icon"></i>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="YOUR EMAIL" required>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" placeholder="SUBJECT" required>
                            <i class="fas fa-tag form-icon"></i>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="YOUR MESSAGE" rows="6" required></textarea>
                            <i class="fas fa-comment form-icon"></i>
                        </div>
                        <button type="submit" name="contact_submit" class="magical-submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            SEND OWL
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
                <div class="footer-links">
                    <a href="https://github.com/AriyaArKa" target="_blank"><i class="fab fa-github"></i></a>
                    <a href="https://www.kaggle.com/arkaariya" target="_blank"><i class="fab fa-kaggle"></i></a>
                    <a href="https://www.linkedin.com/in/arka-nath55/" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
                <p>&copy; 2025 Arka Braja Prasad Nath. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>

</html>