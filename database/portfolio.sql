-- Portfolio Database Structure
-- Created for Arka Braja Prasad Nath's Portfolio
CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;
-- Personal Information Table
CREATE TABLE IF NOT EXISTS personal_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    about_text TEXT,
    profile_image VARCHAR(255),
    resume_file VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100),
    location VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Social Links Table
CREATE TABLE IF NOT EXISTS social_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon_class VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0
);
-- Education Table
CREATE TABLE IF NOT EXISTS education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    degree VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    start_year YEAR,
    end_year YEAR,
    gpa VARCHAR(20),
    description TEXT,
    is_current BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0
);
-- Skills Categories Table
CREATE TABLE IF NOT EXISTS skill_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE
);
-- Skills Table
CREATE TABLE IF NOT EXISTS skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level INT DEFAULT 50,
    -- 1-100
    sort_order INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
);
-- Projects Table
CREATE TABLE IF NOT EXISTS projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    technologies TEXT,
    github_url VARCHAR(255),
    live_url VARCHAR(255),
    image VARCHAR(255),
    project_type ENUM(
        'web',
        'mobile',
        'desktop',
        'ai',
        'game',
        'other'
    ) DEFAULT 'web',
    featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Achievements Table
CREATE TABLE IF NOT EXISTS achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM(
        'competition',
        'certification',
        'award',
        'volunteer',
        'other'
    ) DEFAULT 'other',
    date_achieved DATE,
    position VARCHAR(100),
    organization VARCHAR(255),
    image VARCHAR(255),
    certificate_file VARCHAR(255),
    sort_order INT DEFAULT 0
);
-- Experience Table
CREATE TABLE IF NOT EXISTS experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    position VARCHAR(255) NOT NULL,
    organization VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    start_date DATE,
    end_date DATE,
    description TEXT,
    is_current BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0
);
-- Blog/Articles Table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    replied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'number', 'boolean', 'file') DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- Insert default data
INSERT INTO personal_info (name, title, about_text, email)
VALUES (
        'Arka Braja Prasad Nath',
        'Computer Science & Engineering Student',
        'Passionate CSE student at KUET with expertise in full-stack development, machine learning, and competitive programming. Experienced in multiple programming languages and frameworks with a strong focus on innovation and problem-solving.',
        'arka@example.com'
    );
INSERT INTO social_links (platform, url, icon_class)
VALUES (
        'GitHub',
        'https://github.com/AriyaArKa',
        'fab fa-github'
    ),
    (
        'Kaggle',
        'https://www.kaggle.com/arkaariya',
        'fab fa-kaggle'
    ),
    (
        'LinkedIn',
        'https://www.linkedin.com/in/arka-nath55/',
        'fab fa-linkedin'
    );
INSERT INTO education (
        degree,
        institution,
        start_year,
        end_year,
        gpa,
        is_current
    )
VALUES (
        'Bachelor of Science in Computer Science & Engineering',
        'Khulna University of Engineering & Technology (KUET)',
        2023,
        NULL,
        NULL,
        TRUE
    ),
    (
        'Higher Secondary Certificate (HSC)',
        'Engineering University School & College, Dhaka',
        2019,
        2021,
        '5.00/5.00',
        FALSE
    ),
    (
        'Secondary School Certificate (SSC)',
        'Motijheel Govt. Boys\' High School, Dhaka',
        2017,
        2019,
        '5.00/5.00',
        FALSE
    );
INSERT INTO skill_categories (category_name, sort_order)
VALUES ('Programming Languages', 1),
    ('Frontend Development', 2),
    ('Backend Development', 3),
    ('Mobile App Development', 4),
    ('Database Technologies', 5),
    ('Machine Learning & AI', 6),
    ('Tools & Platforms', 7);
INSERT INTO skills (category_id, skill_name, proficiency_level)
VALUES (1, 'C/C++', 90),
    (1, 'Java', 85),
    (1, 'JavaScript', 88),
    (1, 'Python', 90),
    (1, 'PHP', 82),
    (1, 'C#', 78),
    (2, 'HTML5/CSS3', 92),
    (2, 'React.js', 85),
    (2, 'Bootstrap', 88),
    (2, 'jQuery', 80),
    (3, 'Node.js', 82),
    (3, 'Express.js', 80),
    (3, 'Laravel', 75),
    (3, 'ASP.NET', 70),
    (4, 'Android Studio', 80),
    (4, 'Flutter', 78),
    (5, 'MySQL', 88),
    (5, 'MongoDB', 75),
    (5, 'Firebase', 80),
    (6, 'TensorFlow', 82),
    (6, 'PyTorch', 78),
    (6, 'OpenCV', 80),
    (7, 'Git/GitHub', 90),
    (7, 'VS Code', 95),
    (7, 'Linux', 85);
INSERT INTO admin_users (username, email, password_hash)
VALUES (
        'admin',
        'admin@portfolio.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );
-- password: password
INSERT INTO site_settings (setting_key, setting_value, setting_type)
VALUES (
        'site_title',
        'Arka Braja Prasad Nath - Portfolio',
        'text'
    ),
    (
        'site_description',
        'Portfolio of Arka Braja Prasad Nath - CSE Student & Full Stack Developer',
        'textarea'
    ),
    ('default_theme', 'light', 'text'),
    ('contact_email', 'arka@example.com', 'text'),
    ('show_blog', '1', 'boolean'),
    ('items_per_page', '10', 'number');