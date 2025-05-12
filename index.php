<?php
// Structure for a full-stack portfolio website using PHP and MySQL

/**
 * DATABASE SCHEMA
 * 
 * -- User/Admin Table
 * CREATE TABLE users (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   username VARCHAR(50) NOT NULL UNIQUE,
 *   password VARCHAR(255) NOT NULL,
 *   email VARCHAR(100) NOT NULL UNIQUE,
 *   role ENUM('admin', 'client') NOT NULL DEFAULT 'client',
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * -- Projects Table
 * CREATE TABLE projects (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   title VARCHAR(100) NOT NULL,
 *   description TEXT NOT NULL,
 *   category VARCHAR(50) NOT NULL,
 *   image_url VARCHAR(255),
 *   github_link VARCHAR(255),
 *   live_demo_link VARCHAR(255),
 *   technologies VARCHAR(255) NOT NULL,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * -- Skills Table
 * CREATE TABLE skills (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   category VARCHAR(50) NOT NULL,
 *   name VARCHAR(50) NOT NULL,
 *   proficiency INT NOT NULL,
 *   icon VARCHAR(50)
 * );
 * 
 * -- Messages Table
 * CREATE TABLE messages (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   sender_id INT,
 *   receiver_id INT,
 *   subject VARCHAR(100) NOT NULL,
 *   message TEXT NOT NULL,
 *   is_read BOOLEAN DEFAULT FALSE,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
 *   FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL
 * );
 * 
 * -- Services Table
 * CREATE TABLE services (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   title VARCHAR(100) NOT NULL,
 *   description TEXT NOT NULL,
 *   price DECIMAL(10,2) NOT NULL,
 *   duration VARCHAR(50) NOT NULL,
 *   image_url VARCHAR(255)
 * );
 * 
 * -- Orders Table
 * CREATE TABLE orders (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   client_id INT NOT NULL,
 *   service_id INT NOT NULL,
 *   status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
 *   requirements TEXT,
 *   price DECIMAL(10,2) NOT NULL,
 *   payment_method VARCHAR(50),
 *   payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
 *   transaction_id VARCHAR(100),
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
 *   FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
 * );
 * 
 * -- Testimonials Table
 * CREATE TABLE testimonials (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   client_name VARCHAR(100) NOT NULL,
 *   client_company VARCHAR(100),
 *   client_position VARCHAR(100),
 *   content TEXT NOT NULL,
 *   rating INT NOT NULL,
 *   is_approved BOOLEAN DEFAULT FALSE,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * -- Blog Posts Table
 * CREATE TABLE blog_posts (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   title VARCHAR(200) NOT NULL,
 *   content TEXT NOT NULL,
 *   category VARCHAR(50) NOT NULL,
 *   image_url VARCHAR(255),
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * -- Analytics Table
 * CREATE TABLE analytics (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   page_visited VARCHAR(50) NOT NULL,
 *   visitor_ip VARCHAR(50) NOT NULL,
 *   country VARCHAR(50),
 *   device VARCHAR(50),
 *   browser VARCHAR(50),
 *   visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 */

// File structure
/**
 * portfolio/
 * ├── admin/
 * │   ├── dashboard.php
 * │   ├── messages.php
 * │   ├── orders.php
 * │   ├── projects.php
 * │   ├── services.php
 * │   ├── settings.php
 * │   ├── skills.php
 * │   └── testimonials.php
 * ├── assets/
 * │   ├── css/
 * │   ├── images/
 * │   ├── js/
 * │   └── uploads/
 * ├── client/
 * │   ├── dashboard.php
 * │   ├── messages.php
 * │   ├── orders.php
 * │   └── profile.php
 * ├── includes/
 * │   ├── chatbot.php
 * │   ├── config.php
 * │   ├── db.php
 * │   ├── footer.php
 * │   ├── functions.php
 * │   ├── header.php
 * │   └── nav.php
 * ├── payment/
 * │   ├── bkash.php
 * │   ├── callback.php
 * │   ├── checkout.php
 * │   └── sslcommerz.php
 * ├── about.php
 * ├── blog.php
 * ├── chat.php
 * ├── contact.php
 * ├── index.php
 * ├── login.php
 * ├── projects.php
 * ├── register.php
 * ├── services.php
 * └── single-project.php
 */

// Sample index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arka Nath - Portfolio & Freelance Services</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

    <!-- Hero Section -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 d-flex flex-column justify-content-center">
                    <h1>Arka Braja Prasad Nath</h1>
                    <h2>Full Stack Developer | AI/ML Specialist | Graphics Designer</h2>
                    <p>KUET CSE Undergraduate | Freelancer</p>
                    <div class="d-flex">
                        <a href="#about" class="btn-get-started scrollto">About Me</a>
                        <a href="#portfolio" class="btn-portfolio scrollto">View My Work</a>
                        <a href="#services" class="btn-services scrollto">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-6 hero-img">
                    <img src="assets/images/profile.jpg" class="img-fluid rounded-circle" alt="Arka Nath">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-title">
                <h2>About Me</h2>
            </div>
            <div class="row content">
                <div class="col-lg-6">
                    <p>
                        I'm a Computer Science & Engineering student at Khulna University of Engineering & Technology (KUET). 
                        With a passion for full-stack development, AI/ML, and graphics design, I offer professional freelance services 
                        to clients across Bangladesh and worldwide.
                    </p>
                    <ul>
                        <li>Full-stack web development (PHP, MySQL, JavaScript, React)</li>
                        <li>AI/ML solutions (TensorFlow, PyTorch, Computer Vision)</li>
                        <li>Mobile and desktop application development</li>
                        <li>Graphics design and UI/UX solutions</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="education">
                        <h3>Education</h3>
                        <div class="education-item">
                            <h4>Bachelor of Science in Computer Science & Engineering</h4>
                            <p>Khulna University of Engineering & Technology (KUET)</p>
                            <p>2023 - Present</p>
                        </div>
                        <div class="education-item">
                            <h4>Higher Secondary Certificate (HSC)</h4>
                            <p>Engineering University School & College, Dhaka</p>
                            <p>GPA: 5.00/5.00 | Year: 2021</p>
                        </div>
                        <div class="education-item">
                            <h4>Secondary School Certificate (SSC)</h4>
                            <p>Motijheel Govt. Boys' High School, Dhaka</p>
                            <p>GPA: 5.00/5.00 | Year: 2019</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="skills">
        <div class="container">
            <div class="section-title">
                <h2>My Skills</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="skill-category">
                        <h3>Programming Languages</h3>
                        <ul>
                            <li>C / C++</li>
                            <li>Java</li>
                            <li>JavaScript</li>
                            <li>Python</li>
                            <li>PHP</li>
                            <li>Dart</li>
                            <li>C#</li>
                            <li>SQL</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="skill-category">
                        <h3>Web Development</h3>
                        <ul>
                            <li>HTML5 / CSS3</li>
                            <li>React.js</li>
                            <li>jQuery</li>
                            <li>Node.js</li>
                            <li>Express.js</li>
                            <li>Laravel</li>
                            <li>Bootstrap</li>
                            <li>AJAX</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="skill-category">
                        <h3>Machine Learning & AI</h3>
                        <ul>
                            <li>TensorFlow</li>
                            <li>PyTorch</li>
                            <li>Keras</li>
                            <li>Scikit-learn</li>
                            <li>OpenCV</li>
                            <li>MediaPipe</li>
                            <li>YOLOv8</li>
                            <li>NLP (SpaCy, NLTK)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="skill-category">
                        <h3>Tools & Others</h3>
                        <ul>
                            <li>GitHub</li>
                            <li>Android Studio</li>
                            <li>Unity (C#)</li>
                            <li>Flutter</li>
                            <li>Figma</li>
                            <li>Adobe Illustrator</li>
                            <li>MongoDB</li>
                            <li>Firebase</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="projects">
        <div class="container">
            <div class="section-title">
                <h2>Featured Projects</h2>
            </div>
            <div class="row">
                <?php
                // In real implementation, fetch from database
                $projects = [
                    [
                        'title' => 'AI Chatbot',
                        'description' => 'Virtual AI Assistant (Doctor) -- Chat App using LLaMA 3 API',
                        'image' => 'assets/images/projects/chatbot.jpg',
                        'github' => 'https://github.com/AriyaArKa/AI-Chabot',
                        'category' => 'AI/ML'
                    ],
                    [
                        'title' => 'Music Recommendation System',
                        'description' => 'Music Recommendation System using NLP',
                        'image' => 'assets/images/projects/music.jpg',
                        'github' => 'https://github.com/AriyaArKa/Music-Recommendation-System',
                        'category' => 'AI/ML'
                    ],
                    [
                        'title' => 'Plant Disease Detection App',
                        'description' => 'A beginner level plant disease detection model that works for 3 diseases',
                        'image' => 'assets/images/projects/plant.jpg',
                        'github' => 'https://github.com/AriyaArKa/Plant-Disease-Detection-App',
                        'category' => 'AI/ML'
                    ],
                    [
                        'title' => 'Android Ball Bounce Game',
                        'description' => 'An android ball bouncing game made with UNITY game engine',
                        'image' => 'assets/images/projects/game.jpg',
                        'github' => 'https://github.com/AriyaArKa/Android-Ball-Bounce-game',
                        'category' => 'Game Development'
                    ]
                ];

                foreach ($projects as $project) {
                    echo '
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card project-card">
                            <img src="' . $project['image'] . '" class="card-img-top" alt="' . $project['title'] . '">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">' . $project['category'] . '</span>
                                <h5 class="card-title">' . $project['title'] . '</h5>
                                <p class="card-text">' . $project['description'] . '</p>
                                <div class="project-links">
                                    <a href="' . $project['github'] . '" class="btn btn-outline-dark btn-sm" target="_blank"><i class="fab fa-github"></i> Code</a>
                                    <a href="single-project.php?id=1" class="btn btn-primary btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }
                ?>
            </div>
            <div class="text-center mt-4">
                <a href="projects.php" class="btn btn-outline-primary">View All Projects</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <h2>My Services</h2>
                <p>Professional services I offer to clients in Bangladesh and worldwide</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-code service-icon"></i>
                        <h3>Web Development</h3>
                        <p>Full-stack web applications using PHP, MySQL, React.js, and more. From portfolio sites to complex web apps.</p>
                        <div class="price">Starting at ৳ 15,000</div>
                        <a href="services.php#web" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=web" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-robot service-icon"></i>
                        <h3>AI & ML Solutions</h3>
                        <p>Custom AI models, computer vision applications, chatbots, and recommendation systems.</p>
                        <div class="price">Starting at ৳ 20,000</div>
                        <a href="services.php#ai" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=ai" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-mobile-alt service-icon"></i>
                        <h3>Mobile App Development</h3>
                        <p>Android apps using Java/Kotlin or cross-platform apps with Flutter. Fully functional and user-friendly.</p>
                        <div class="price">Starting at ৳ 18,000</div>
                        <a href="services.php#mobile" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=mobile" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-paint-brush service-icon"></i>
                        <h3>Graphics Design</h3>
                        <p>Logo design, posters, banners, UI/UX design, and illustrations for various purposes.</p>
                        <div class="price">Starting at ৳ 3,000</div>
                        <a href="services.php#design" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=design" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-gamepad service-icon"></i>
                        <h3>Game Development</h3>
                        <p>2D and 3D games using Unity (C#). Interactive, engaging, and optimized for various platforms.</p>
                        <div class="price">Starting at ৳ 25,000</div>
                        <a href="services.php#game" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=game" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-box">
                        <i class="fas fa-database service-icon"></i>
                        <h3>Database Design</h3>
                        <p>Efficient database architecture, optimization, and maintenance for web and mobile applications.</p>
                        <div class="price">Starting at ৳ 10,000</div>
                        <a href="services.php#database" class="btn btn-outline-primary btn-sm">Details</a>
                        <a href="contact.php?service=database" class="btn btn-primary btn-sm">Hire Me</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <div class="section-title">
                <h2>Client Testimonials</h2>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            "Arka developed an AI-based recommendation system for our e-commerce platform that increased our sales by 23%. Excellent work and great communication throughout the project."
                        </p>
                        <div class="client-info">
                            <img src="assets/images/client1.jpg" alt="Client 1" class="client-img">
                            <div>
                                <h5>Samir Ahmed</h5>
                                <p>CEO, TechBD Solutions</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">
                            "Arka designed our company website and implemented a custom CMS. The design is beautiful and the functionality is exactly what we needed. Highly recommended!"
                        </p>
                        <div class="client-info">
                            <img src="assets/images/client2.jpg" alt="Client 2" class="client-img">
                            <div>
                                <h5>Nusrat Jahan</h5>
                                <p>Marketing Director, Fashion House BD</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            "The mobile app Arka developed for our business has received great feedback from our customers. The UI is intuitive and the app runs smoothly. Will definitely work with him again."
                        </p>
                        <div class="client-info">
                            <img src="assets/images/client3.jpg" alt="Client 3" class="client-img">
                            <div>
                                <h5>Rafiq Islam</h5>
                                <p>Owner, Dhaka Food Delivery</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section id="cta" class="cta">
        <div class="container">
            <div class="text-center">
                <h3>Ready to start a project?</h3>
                <p>Let's discuss your ideas and bring them to life with cutting-edge technology and design.</p>
                <a href="contact.php" class="btn-cta">Contact Me</a>
                <a href="services.php" class="btn-services">View All Services</a>
            </div>
        </div>
    </section>

    <!-- Live Chat Widget -->
    <div id="chat-widget" class="chat-widget">
        <div class="chat-header">
            <h4>Chat with Arka</h4>
            <button id="minimize-chat" class="minimize-btn"><i class="fas fa-minus"></i></button>
        </div>
        <div class="chat-body">
            <div class="chat-messages" id="chat-messages">
                <div class="message received">
                    <p>Hi there! How can I help you today? Feel free to ask me anything about my services.</p>
                </div>
            </div>
            <div class="chat-input">
                <input type="text" id="message-input" placeholder="Type your message...">
                <button id="send-message"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <div class="chat-button" id="open-chat">
            <i class="fas fa-comment"></i>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Chat widget functionality
        document.getElementById('open-chat').addEventListener('click', function() {
            document.getElementById('chat-widget').classList.add('active');
            document.getElementById('open-chat').style.display = 'none';
        });

        document.getElementById('minimize-chat').addEventListener('click', function() {
            document.getElementById('chat-widget').classList.remove('active');
            document.getElementById('open-chat').style.display = 'block';
        });

        document.getElementById('send-message').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            
            if (message !== '') {
                // Add user message
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML += `
                    <div class="message sent">
                        <p>${message}</p>
                    </div>
                `;
                
                // Clear input
                messageInput.value = '';
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // In real implementation, send to server via AJAX and get response
                // For demo, simulate response after delay
                setTimeout(function() {
                    chatMessages.innerHTML += `
                        <div class="message received">
                            <p>Thanks for your message! I'll get back to you soon. If you'd like to discuss a project, please share some details about what you need.</p>
                        </div>
                    `;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1000);
            }
        }
    </script>
</body>
</html>