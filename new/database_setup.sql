-- ===============================================
-- ARKA'S DYNAMIC PORTFOLIO DATABASE SETUP
-- ===============================================
-- Run this script in phpMyAdmin to create the complete database
-- Make sure to create database 'portfolio_db' first or change the database name below
-- Create database (run this first if database doesn't exist)
CREATE DATABASE IF NOT EXISTS `portfolio_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;
-- ===============================================
-- TABLE STRUCTURE FOR ADMIN AUTHENTICATION
-- ===============================================
CREATE TABLE `admin` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR PERSONAL INFORMATION
-- ===============================================
CREATE TABLE `personal_info` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `title` varchar(200) DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `profile_image` varchar(255) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `location` varchar(100) DEFAULT NULL,
    `resume_url` varchar(255) DEFAULT NULL,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR SOCIAL LINKS
-- ===============================================
CREATE TABLE `social_links` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `platform` varchar(50) NOT NULL,
    `url` varchar(255) NOT NULL,
    `icon` varchar(100) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR EDUCATION
-- ===============================================
CREATE TABLE `education` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `degree` varchar(200) NOT NULL,
    `institution` varchar(200) NOT NULL,
    `duration` varchar(100) DEFAULT NULL,
    `gpa` varchar(20) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `year_start` year(4) DEFAULT NULL,
    `year_end` year(4) DEFAULT NULL,
    `is_current` tinyint(1) DEFAULT 0,
    `logo_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR SKILL CATEGORIES
-- ===============================================
CREATE TABLE `skill_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `category_name` varchar(100) NOT NULL,
    `icon` varchar(100) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR SKILLS
-- ===============================================
CREATE TABLE `skills` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `category_id` int(11) DEFAULT NULL,
    `skill_name` varchar(100) NOT NULL,
    `proficiency_level` int(11) DEFAULT 80,
    `icon` varchar(100) DEFAULT NULL,
    `years_experience` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `category_id` (`category_id`),
    CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR ACHIEVEMENTS
-- ===============================================
CREATE TABLE `achievements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `category` varchar(100) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `date_achieved` date DEFAULT NULL,
    `position` varchar(100) DEFAULT NULL,
    `organization` varchar(200) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `certificate_url` varchar(255) DEFAULT NULL,
    `featured` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR PROJECTS
-- ===============================================
CREATE TABLE `projects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `category` varchar(100) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `technologies` text DEFAULT NULL,
    `github_url` varchar(255) DEFAULT NULL,
    `live_url` varchar(255) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'completed',
    `featured` tinyint(1) DEFAULT 0,
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR CERTIFICATES
-- ===============================================
CREATE TABLE `certificates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `issuer` varchar(200) DEFAULT NULL,
    `issue_date` date DEFAULT NULL,
    `expiry_date` date DEFAULT NULL,
    `credential_id` varchar(100) DEFAULT NULL,
    `credential_url` varchar(255) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `skills_gained` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR WORK EXPERIENCE
-- ===============================================
CREATE TABLE `experience` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `position` varchar(200) NOT NULL,
    `company` varchar(200) NOT NULL,
    `location` varchar(100) DEFAULT NULL,
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `is_current` tinyint(1) DEFAULT 0,
    `description` text DEFAULT NULL,
    `technologies` text DEFAULT NULL,
    `achievements` text DEFAULT NULL,
    `company_logo` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR GALLERY
-- ===============================================
CREATE TABLE `gallery` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) DEFAULT NULL,
    `category` varchar(100) DEFAULT NULL,
    `image_path` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `tags` varchar(255) DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR TESTIMONIALS
-- ===============================================
CREATE TABLE `testimonials` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `position` varchar(200) DEFAULT NULL,
    `company` varchar(200) DEFAULT NULL,
    `testimonial` text NOT NULL,
    `image` varchar(255) DEFAULT NULL,
    `rating` int(11) DEFAULT 5,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR BLOG POSTS (OPTIONAL)
-- ===============================================
CREATE TABLE `blog_posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `content` longtext NOT NULL,
    `excerpt` text DEFAULT NULL,
    `featured_image` varchar(255) DEFAULT NULL,
    `category` varchar(100) DEFAULT NULL,
    `tags` varchar(255) DEFAULT NULL,
    `status` enum('draft', 'published') DEFAULT 'draft',
    `published_at` datetime DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- TABLE STRUCTURE FOR CONTACT MESSAGES
-- ===============================================
CREATE TABLE `contact_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `subject` varchar(200) DEFAULT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) DEFAULT 0,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ===============================================
-- INSERT DEFAULT DATA
-- ===============================================
-- Insert default admin user
INSERT INTO `admin` (`username`, `password`, `email`)
VALUES (
        'arka_admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'arka@example.com'
    );
-- Note: This password hash is for 'admin123'
-- Insert personal information
INSERT INTO `personal_info` (`name`, `title`, `bio`, `email`, `location`)
VALUES (
        'Arka Braja Prasad Nath',
        'Computer Science & Engineering Student | Full Stack Developer',
        'Passionate Computer Science & Engineering student at KUET with expertise in full-stack development, machine learning, and mobile app development. Experienced in multiple programming languages and frameworks with a strong foundation in problem-solving and innovative solution development.',
        'arka.nath@example.com',
        'Khulna, Bangladesh'
    );
-- Insert social links
INSERT INTO `social_links` (
        `platform`,
        `url`,
        `icon`,
        `is_active`,
        `sort_order`
    )
VALUES (
        'GitHub',
        'https://github.com/AriyaArKa',
        'fab fa-github',
        1,
        1
    ),
    (
        'Kaggle',
        'https://www.kaggle.com/arkaariya',
        'fab fa-kaggle',
        1,
        2
    ),
    (
        'LinkedIn',
        'https://www.linkedin.com/in/arka-nath55/',
        'fab fa-linkedin',
        1,
        3
    );
-- Insert education records
INSERT INTO `education` (
        `degree`,
        `institution`,
        `duration`,
        `gpa`,
        `description`,
        `year_start`,
        `year_end`,
        `is_current`
    )
VALUES (
        'Bachelor of Science in Computer Science & Engineering',
        'Khulna University of Engineering & Technology (KUET)',
        '2023 - Present',
        NULL,
        'Currently pursuing Bachelor of Science degree with focus on software engineering, algorithms, data structures, and emerging technologies.',
        2023,
        NULL,
        1
    ),
    (
        'Higher Secondary Certificate (HSC)',
        'Engineering University School & College, Dhaka',
        '2021',
        '5.00/5.00',
        'Completed HSC in Science group with perfect GPA, demonstrating strong academic foundation in mathematics, physics, and chemistry.',
        2019,
        2021,
        0
    ),
    (
        'Secondary School Certificate (SSC)',
        'Motijheel Govt. Boys\' High School, Dhaka',
        '2019',
        '5.00/5.00',
        'Completed SSC in Science group with perfect GPA, establishing strong foundation in core subjects.',
        2017,
        2019,
        0
    );
-- Insert skill categories
INSERT INTO `skill_categories` (`category_name`, `icon`, `sort_order`)
VALUES ('Programming Languages', 'fas fa-code', 1),
    ('Frontend Development', 'fas fa-paint-brush', 2),
    ('Backend Development', 'fas fa-server', 3),
    ('Mobile App Development', 'fas fa-mobile-alt', 4),
    ('Desktop App Development', 'fas fa-desktop', 5),
    ('Game Development', 'fas fa-gamepad', 6),
    ('Databases', 'fas fa-database', 7),
    ('Machine Learning & AI', 'fas fa-brain', 8),
    (
        'Data Science & Analysis',
        'fas fa-chart-line',
        9
    ),
    ('Tools & Platforms', 'fas fa-tools', 10),
    ('Design & Prototyping', 'fas fa-palette', 11);
-- Insert skills
INSERT INTO `skills` (
        `category_id`,
        `skill_name`,
        `proficiency_level`,
        `icon`
    )
VALUES -- Programming Languages
    (1, 'C', 85, 'fab fa-cuttlefish'),
    (1, 'C++', 90, 'fab fa-cuttlefish'),
    (1, 'Java', 85, 'fab fa-java'),
    (1, 'JavaScript', 90, 'fab fa-js'),
    (1, 'Python', 95, 'fab fa-python'),
    (1, 'PHP', 88, 'fab fa-php'),
    (1, 'Dart', 80, NULL),
    (1, 'C#', 75, 'fab fa-microsoft'),
    (1, 'SQL', 85, 'fas fa-database'),
    -- Frontend Development
    (2, 'HTML5', 95, 'fab fa-html5'),
    (2, 'CSS3', 90, 'fab fa-css3-alt'),
    (2, 'Bootstrap', 85, 'fab fa-bootstrap'),
    (2, 'React.js', 80, 'fab fa-react'),
    (2, 'jQuery', 85, 'fab fa-js'),
    (2, 'AJAX', 80, NULL),
    -- Backend Development
    (3, 'Node.js', 75, 'fab fa-node-js'),
    (3, 'Express.js', 75, NULL),
    (3, 'Flask', 80, NULL),
    (3, 'Laravel', 70, 'fab fa-laravel'),
    (3, 'ASP.NET', 70, 'fab fa-microsoft'),
    -- Mobile App Development
    (
        4,
        'Android Studio (Java/Kotlin)',
        80,
        'fab fa-android'
    ),
    (4, 'Flutter (Dart)', 85, NULL),
    -- Desktop App Development
    (5, 'JavaFX', 75, 'fab fa-java'),
    (5, 'ASP.NET', 70, 'fab fa-microsoft'),
    -- Game Development
    (6, 'Unity Game Engine (C#)', 80, 'fab fa-unity'),
    -- Databases
    (7, 'MySQL', 90, NULL),
    (7, 'MongoDB', 75, NULL),
    (7, 'Firebase Realtime DB', 80, 'fab fa-google'),
    (7, 'SQL Server (SSMS)', 75, 'fab fa-microsoft'),
    -- Machine Learning & AI
    (8, 'TensorFlow', 80, NULL),
    (8, 'PyTorch', 75, NULL),
    (8, 'Keras', 80, NULL),
    (8, 'Scikit-learn', 85, NULL),
    (8, 'OpenCV', 80, NULL),
    (8, 'MediaPipe', 75, NULL),
    (8, 'YOLOv8', 70, NULL),
    (8, 'SpaCy', 75, NULL),
    (8, 'NLTK', 80, NULL),
    (8, 'CNN', 75, NULL),
    -- Data Science & Analysis
    (9, 'NumPy', 85, NULL),
    (9, 'Pandas', 90, NULL),
    (9, 'Matplotlib', 80, NULL),
    -- Tools & Platforms
    (10, 'GitHub', 90, 'fab fa-github'),
    (10, 'GitHub Pages', 85, 'fab fa-github'),
    (10, 'VS Code', 95, 'fas fa-code'),
    (10, 'Jupyter Notebook', 85, NULL),
    (10, 'Google Colab', 80, 'fab fa-google'),
    (10, 'VirtualBox', 75, NULL),
    (10, 'Arduino IDE', 70, NULL),
    (
        10,
        'Cisco Packet Tracer',
        75,
        'fas fa-network-wired'
    ),
    (10, 'Linux', 80, 'fab fa-linux'),
    -- Design & Prototyping
    (11, 'Figma', 75, 'fab fa-figma'),
    (11, 'Canva', 80, NULL),
    (11, 'Adobe Illustrator', 70, NULL);
-- Insert achievements
INSERT INTO `achievements` (
        `title`,
        `category`,
        `description`,
        `date_achieved`,
        `position`,
        `organization`
    )
VALUES (
        'KUET Rising Star - BITFEST 2025',
        'Datathon',
        'Achieved 19th position among 150 teams on the leaderboard in the prestigious BITFEST 2025 datathon competition, demonstrating strong data analysis and machine learning skills.',
        '2025-01-15',
        '19th Position',
        'KUET'
    ),
    (
        'Second Runners-up of Biz Bash 3.0',
        'Case Study',
        'Secured second runners-up position in the business case study competition organized by KUET Career Club, showcasing analytical and strategic thinking abilities.',
        '2024-11-20',
        'Second Runners-up',
        'KUET Career Club'
    ),
    (
        'Battle of Minds 2024',
        'Competition',
        'Successfully qualified for the second round of Battle of Minds 2024, competing against top participants in intellectual challenges.',
        '2024-10-15',
        'Second Round Qualifier',
        'Competition Organizers'
    ),
    (
        '25th Position at Intra KUET Programming Contest',
        'Contest Programming',
        'Achieved 25th position in the Intra KUET Programming Contest (IKPC) arranged by SGIPC, demonstrating strong algorithmic problem-solving skills.',
        '2024-09-10',
        '25th Position',
        'SGIPC, KUET'
    ),
    (
        'Digit Recognizer - Kaggle Competition',
        'Kaggle Competition',
        'Ranked 61st out of thousands of participants in the MNIST digit recognition challenge, demonstrating proficiency in machine learning and neural networks using Python, TensorFlow, Keras, and Scikit-learn.',
        '2024-08-05',
        '61st Position',
        'Kaggle'
    ),
    (
        'Best Volunteer of Hult Prize at KUET 2024',
        'Volunteering',
        'Recognized as the best volunteer for outstanding contribution and dedication in organizing and managing the Hult Prize competition at KUET.',
        '2024-07-15',
        'Best Volunteer',
        'Hult Prize at KUET'
    );
-- Insert sample projects
INSERT INTO `projects` (
        `title`,
        `category`,
        `description`,
        `technologies`,
        `github_url`,
        `status`,
        `featured`
    )
VALUES (
        'Android Ball Bounce Game',
        'Game Development',
        'An engaging Android ball bouncing game developed with Unity Game Engine. Features a ball that bounces off walls and a moving paddle - miss the paddle and it\'s game over! Demonstrates mobile game development skills and physics implementation.',
        'Unity Game Engine, C#, Android SDK',
        'https://github.com/AriyaArKa/Android-Ball-Bounce-Game',
        'completed',
        1
    ),
    (
        'AI Chatbot - Virtual Doctor Assistant',
        'Machine Learning',
        'Virtual AI assistant functioning as a doctor chat application utilizing Llama 3 API. Provides medical consultation and health advice through natural language processing and conversation AI.',
        'Python, Llama 3 API, NLP, Flask',
        'https://github.com/AriyaArKa/AI-Chatbot',
        'completed',
        1
    ),
    (
        'Music Recommendation System',
        'Machine Learning',
        'Intelligent music recommendation system using Natural Language Processing to analyze user preferences and suggest personalized music based on listening patterns and preferences.',
        'Python, NLP, Scikit-learn, Pandas, NumPy',
        'https://github.com/AriyaArKa/Music-Recommendation-System',
        'completed',
        0
    ),
    (
        'Plant Disease Detection App',
        'Machine Learning',
        'Beginner-level plant disease detection application that identifies three primary diseases: Corn (Maize) Common Rust, Potato Early Blight, and Tomato Bacterial Spot using computer vision and machine learning.',
        'Python, TensorFlow, OpenCV, Machine Learning, Image Processing',
        'https://github.com/AriyaArKa/Plant-Disease-Detection-App',
        'completed',
        0
    ),
    (
        'BookStore App - Windows Forms Inventory Manager',
        'Desktop App Development',
        'A comprehensive C#-based Windows Forms application designed for efficient bookstore inventory management with SQL Server backend. Features include adding, viewing, and printing book records with input validation and auto-generated book IDs.',
        'C#, Windows Forms, .NET Framework, SQL Server Express, ADO.NET, RDLC Reporting',
        NULL,
        'completed',
        0
    ),
    (
        'pfSense & Ubuntu LAN Firewall',
        'Network Security',
        'Configured a comprehensive LAN firewall system using pfSense on Ubuntu running in VirtualBox. Implemented network security, routing, traffic control, NAT, VPN settings, and network monitoring for secure local network management.',
        'VirtualBox, pfSense, Ubuntu, Linux, Network Configuration, Security',
        NULL,
        'completed',
        0
    );
-- Insert sample gallery items (for design work)
INSERT INTO `gallery` (`title`, `category`, `image_path`, `description`)
VALUES (
        'Facebook Poster Design',
        'Graphic Design',
        '/images/gallery/facebook-poster.jpg',
        'Social media poster design for previous club works'
    ),
    (
        'Hackathon Event Poster',
        'Graphic Design',
        '/images/gallery/hackathon-poster.jpg',
        'Poster created for the annual university hackathon using Adobe Illustrator'
    ),
    (
        'Banner Design Collection',
        'Graphic Design',
        '/images/gallery/banner-design.jpg',
        'Collection of various banner designs for events and promotions'
    ),
    (
        'Club Work Portfolio',
        'Graphic Design',
        '/images/gallery/club-work.jpg',
        'Previous design work completed for university clubs and organizations'
    ),
    (
        'Event Promotion Graphics',
        'Graphic Design',
        '/images/gallery/event-graphics.jpg',
        'Graphics and promotional materials for various university events'
    );
-- ===============================================
-- INDEXES FOR BETTER PERFORMANCE
-- ===============================================
-- Add indexes for frequently queried columns
CREATE INDEX idx_skills_category ON skills(category_id);
CREATE INDEX idx_achievements_date ON achievements(date_achieved);
CREATE INDEX idx_projects_featured ON projects(featured);
CREATE INDEX idx_social_links_active ON social_links(is_active);
CREATE INDEX idx_gallery_category ON gallery(category);
CREATE INDEX idx_education_current ON education(is_current);
-- ===============================================
-- END OF DATABASE SETUP
-- ===============================================
-- Note: After running this script, the database will be ready for use
-- Default admin credentials: username = 'arka_admin', password = 'admin123'
-- Make sure to change the default password after first login for security