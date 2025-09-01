CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;
-- Admin table
CREATE TABLE `admin` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
);
-- Projects table
CREATE TABLE `projects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text,
    `technologies` varchar(500),
    `github_link` varchar(255),
    `demo_link` varchar(255),
    `image` varchar(255),
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
-- Skills table
CREATE TABLE `skills` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `category` varchar(100) NOT NULL,
    `skill_name` varchar(100) NOT NULL,
    `proficiency` int(11) DEFAULT 80,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
-- Achievements table
CREATE TABLE `achievements` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text,
    `date_achieved` date,
    `category` varchar(100),
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
-- Testimonials table
CREATE TABLE `testimonials` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `position` varchar(100),
    `company` varchar(100),
    `message` text NOT NULL,
    `image` varchar(255),
    `rating` int(11) DEFAULT 5,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
-- Messages table
CREATE TABLE `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `message` text NOT NULL,
    `status` enum('unread', 'read') DEFAULT 'unread',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);
-- Insert default admin user (password: admin123)
INSERT INTO `admin` (`username`, `password`)
VALUES (
        'admin',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );
-- Insert projects data
INSERT INTO `projects` (
        `title`,
        `description`,
        `technologies`,
        `github_link`,
        `demo_link`,
        `status`
    )
VALUES (
        'Android Ball Bounce Game',
        'A fun and interactive ball bouncing game developed for Android devices using Unity game engine.',
        'Unity, C#, Android',
        'https://github.com/AriyaArKa/Android-Ball-Bounce-game',
        '',
        'active'
    ),
    (
        'AI Chatbot',
        'Virtual AI Assistant (Doctor) using LLaMA 3 API for healthcare consultations and medical advice.',
        'Python, LLaMA 3 API, NLP, Machine Learning',
        'https://github.com/AriyaArKa/AI-Chatbot',
        '',
        'active'
    ),
    (
        'Music Recommendation System',
        'NLP-based music recommendation system that suggests songs based on user preferences and listening history.',
        'Python, NLP, Machine Learning, Scikit-learn',
        'https://github.com/AriyaArKa/Music-Recommendation-System',
        '',
        'active'
    ),
    (
        'Plant Disease Detection App',
        'Mobile application that detects 3 plant diseases: Corn rust, Potato early blight, and Tomato bacterial spot using computer vision.',
        'Python, TensorFlow, OpenCV, CNN, Mobile Development',
        'https://github.com/AriyaArKa/Plant-Disease-Detection-App',
        '',
        'active'
    ),
    (
        'BookStore App',
        'C# Windows Forms Inventory Manager with SQL Server backend for managing bookstore operations and inventory.',
        'C#, Windows Forms, SQL Server, RDLC Reporting',
        'https://github.com/AriyaArKa/BookStoreApp',
        '',
        'active'
    ),
    (
        'VirtualBox Firewall',
        'Network security implementation using pfSense and Ubuntu to create a LAN firewall system in VirtualBox.',
        'pfSense, Ubuntu, Networking, VirtualBox, Firewall',
        'https://github.com/AriyaArKa/VirtualBox-Firewall',
        '',
        'active'
    );
-- Insert skills data
INSERT INTO `skills` (
        `category`,
        `skill_name`,
        `proficiency`,
        `status`
    )
VALUES ('Programming Languages', 'C', 85, 'active'),
    ('Programming Languages', 'C++', 90, 'active'),
    ('Programming Languages', 'Java', 85, 'active'),
    (
        'Programming Languages',
        'JavaScript',
        88,
        'active'
    ),
    ('Programming Languages', 'Python', 92, 'active'),
    ('Programming Languages', 'PHP', 80, 'active'),
    ('Programming Languages', 'Dart', 75, 'active'),
    ('Programming Languages', 'C#', 82, 'active'),
    ('Programming Languages', 'SQL', 85, 'active'),
    ('Programming Languages', 'HTML', 95, 'active'),
    ('Programming Languages', 'CSS', 90, 'active'),
    ('Frontend Development', 'HTML5', 95, 'active'),
    ('Frontend Development', 'CSS3', 90, 'active'),
    (
        'Frontend Development',
        'JavaScript',
        88,
        'active'
    ),
    (
        'Frontend Development',
        'Bootstrap',
        85,
        'active'
    ),
    ('Frontend Development', 'React.js', 80, 'active'),
    ('Frontend Development', 'jQuery', 82, 'active'),
    ('Frontend Development', 'AJAX', 78, 'active'),
    ('Backend Development', 'Node.js', 80, 'active'),
    (
        'Backend Development',
        'Express.js',
        78,
        'active'
    ),
    ('Backend Development', 'Flask', 82, 'active'),
    ('Backend Development', 'Laravel', 75, 'active'),
    ('Backend Development', 'ASP.NET', 80, 'active'),
    (
        'Mobile Development',
        'Android Studio',
        85,
        'active'
    ),
    ('Mobile Development', 'Flutter', 75, 'active'),
    ('Desktop Development', 'JavaFX', 80, 'active'),
    ('Desktop Development', 'ASP.NET', 80, 'active'),
    ('Game Development', 'Unity', 85, 'active'),
    ('Databases', 'MySQL', 88, 'active'),
    ('Databases', 'MongoDB', 75, 'active'),
    ('Databases', 'Firebase', 80, 'active'),
    ('Databases', 'SQL Server', 85, 'active'),
    ('ML & AI', 'TensorFlow', 85, 'active'),
    ('ML & AI', 'PyTorch', 80, 'active'),
    ('ML & AI', 'Keras', 82, 'active'),
    ('ML & AI', 'Scikit-learn', 88, 'active'),
    ('ML & AI', 'OpenCV', 85, 'active'),
    ('ML & AI', 'MediaPipe', 75, 'active'),
    ('ML & AI', 'YOLOv8', 80, 'active'),
    ('ML & AI', 'SpaCy', 78, 'active'),
    ('ML & AI', 'NLTK', 80, 'active'),
    ('ML & AI', 'CNN', 85, 'active'),
    ('Data Science & Analysis', 'NumPy', 90, 'active'),
    (
        'Data Science & Analysis',
        'Pandas',
        92,
        'active'
    ),
    (
        'Data Science & Analysis',
        'Matplotlib',
        85,
        'active'
    ),
    ('Tools & Platforms', 'GitHub', 90, 'active'),
    ('Tools & Platforms', 'VS Code', 95, 'active'),
    (
        'Tools & Platforms',
        'Jupyter Notebook',
        88,
        'active'
    ),
    (
        'Tools & Platforms',
        'Google Colab',
        85,
        'active'
    ),
    (
        'Tools & Platforms',
        'Android Studio',
        85,
        'active'
    ),
    ('Tools & Platforms', 'VirtualBox', 80, 'active'),
    ('Tools & Platforms', 'Arduino IDE', 75, 'active'),
    (
        'Tools & Platforms',
        'Cisco Packet Tracer',
        78,
        'active'
    ),
    ('Tools & Platforms', 'Linux', 82, 'active'),
    ('Design & Prototyping', 'Figma', 80, 'active'),
    ('Design & Prototyping', 'Canva', 85, 'active'),
    (
        'Design & Prototyping',
        'Adobe Illustrator',
        75,
        'active'
    );
-- Insert achievements data
INSERT INTO `achievements` (
        `title`,
        `description`,
        `date_achieved`,
        `category`,
        `status`
    )
VALUES (
        'KUET Rising Star – BITFEST 2025',
        'Achieved 19th position among 150 teams in the Datathon competition at BITFEST 2025',
        '2025-01-15',
        'Competition',
        'active'
    ),
    (
        'Second Runners-up – Biz Bash 3.0',
        'Secured second runners-up position in Biz Bash 3.0 organized by KUET Career Club',
        '2024-11-20',
        'Competition',
        'active'
    ),
    (
        'Battle of Minds 2024 – Second Round',
        'Successfully qualified and participated in the second round of Battle of Minds 2024',
        '2024-10-15',
        'Competition',
        'active'
    ),
    (
        '25th Position – Intra KUET Programming Contest',
        'Achieved 25th position in the Intra KUET Programming Contest (IKPC)',
        '2024-09-10',
        'Programming',
        'active'
    ),
    (
        'Kaggle Digit Recognizer – Top 100',
        'Secured Rank 61 among thousands of participants in Kaggle Digit Recognizer competition',
        '2024-08-20',
        'Machine Learning',
        'active'
    ),
    (
        'Best Volunteer – HULT Prize KUET 2024',
        'Awarded Best Volunteer for outstanding contribution to HULT Prize KUET 2024',
        '2024-07-30',
        'Volunteer',
        'active'
    );
-- Insert testimonials data (placeholders)
INSERT INTO `testimonials` (
        `name`,
        `position`,
        `company`,
        `message`,
        `rating`,
        `status`
    )
VALUES (
        'Dr. Sarah Johnson',
        'Professor',
        'KUET',
        'Arka is an exceptional student with outstanding programming skills and a keen interest in AI and machine learning. His dedication to learning and problem-solving abilities make him stand out among his peers.',
        5,
        'active'
    ),
    (
        'Mark Thompson',
        'Senior Developer',
        'TechCorp Solutions',
        'I had the pleasure of mentoring Arka during his internship. His ability to grasp complex concepts quickly and implement them effectively is remarkable. He would be a valuable addition to any development team.',
        5,
        'active'
    ),
    (
        'Emily Chen',
        'Project Manager',
        'Innovation Labs',
        'Arka delivered an excellent project under tight deadlines. His attention to detail and commitment to quality work impressed the entire team. I highly recommend him for any technical role.',
        5,
        'active'
    );