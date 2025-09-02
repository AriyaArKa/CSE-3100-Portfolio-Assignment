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
                <ul class="nav-menu">
                    <li><a href="#home" class="nav-link">Home</a></li>
                    <li><a href="#about" class="nav-link">About</a></li>
                    <li><a href="#projects" class="nav-link">Projects</a></li>
                    <li><a href="#skills" class="nav-link">Skills</a></li>
                    <li><a href="#achievements" class="nav-link">Achievements</a></li>
                    <li><a href="#extracurricular" class="nav-link">Activities</a></li>
                    <li><a href="#testimonials" class="nav-link">Testimonials</a></li>
                    <li><a href="#education" class="nav-link">Education</a></li>
                    <li><a href="#contact" class="nav-link">Contact</a></li>
                </ul>
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
            <h2 class="section-title">About Me</h2>
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
            <h2 class="section-title">Projects</h2>
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-image">
                            <img src="<?php echo $project['image'] ?: 'https://via.placeholder.com/300x200/4a90e2/ffffff?text=Project'; ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        </div>
                        <div class="project-content">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p><?php echo htmlspecialchars($project['description']); ?></p>
                            <div class="project-tech">
                                <span class="tech-tag"><?php echo htmlspecialchars($project['technologies']); ?></span>
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
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="skills">
        <div class="container">
            <h2 class="section-title">Skills</h2>
            <div class="skills-container">
                <?php foreach ($skills as $category => $categorySkills): ?>
                    <div class="skill-category">
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                        <div class="skills-list">
                            <?php foreach ($categorySkills as $skill): ?>
                                <div class="skill-item">
                                    <span class="skill-name"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                                    <div class="skill-bar">
                                        <div class="skill-progress" style="width: <?php echo $skill['proficiency']; ?>%"></div>
                                    </div>
                                    <span class="skill-percent"><?php echo $skill['proficiency']; ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievements Section -->
    <section id="achievements" class="achievements">
        <div class="container">
            <h2 class="section-title">Achievements</h2>
            <div class="achievements-grid">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="achievement-content">
                            <h3><?php echo htmlspecialchars($achievement['title']); ?></h3>
                            <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                            <span class="achievement-date"><?php echo date('F Y', strtotime($achievement['date_achieved'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Extracurricular Activities Section -->
    <section id="extracurricular" class="extracurricular-section">
        <div class="container">
            <h2 class="section-title">Extracurricular Activities</h2>
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
            <h2 class="section-title">Education</h2>
            <div class="education-timeline">
                <div class="education-item">
                    <div class="education-year">2023 – Present</div>
                    <div class="education-content">
                        <h3>Bachelor of Science in Computer Science & Engineering</h3>
                        <h4>Khulna University of Engineering & Technology (KUET)</h4>
                        <p>Currently pursuing my undergraduate degree with a focus on software engineering, artificial intelligence, and machine learning. Actively involved in various programming competitions and technical projects.</p>
                    </div>
                </div>
                <div class="education-item">
                    <div class="education-year">2021</div>
                    <div class="education-content">
                        <h3>Higher Secondary Certificate</h3>
                        <h4>Engineering University School & College, Dhaka</h4>
                        <p><strong>Group:</strong> Science | <strong>GPA:</strong> 5.00/5.00</p>
                        <p>Completed higher secondary education with excellent academic performance, laying a strong foundation in mathematics, physics, and chemistry.</p>
                    </div>
                </div>
                <div class="education-item">
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
            <h2 class="section-title">Testimonials</h2>
            <div class="testimonials-slider">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-empty'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p>"<?php echo htmlspecialchars($testimonial['message']); ?>"</p>
                            <div class="testimonial-author">
                                <div class="author-image">
                                    <img src="<?php echo $testimonial['image'] ?: 'https://via.placeholder.com/80x80/4a90e2/ffffff?text=' . substr($testimonial['name'], 0, 1); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                                </div>
                                <div class="author-info">
                                    <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($testimonial['position']); ?><?php echo $testimonial['company'] ? ' at ' . htmlspecialchars($testimonial['company']) : ''; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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