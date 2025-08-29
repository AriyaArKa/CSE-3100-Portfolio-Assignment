<?php
require_once 'config/database.php';

$database = new Database();

// Create database first
if ($database->createDatabase()) {
    echo "Database created successfully<br>";
}

// Get connection
$conn = $database->getConnection();

if ($conn) {
    try {
        // Admin table for authentication
        $sql_admin = "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_admin);

        // Personal info table
        $sql_personal = "CREATE TABLE IF NOT EXISTS personal_info (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            title VARCHAR(200),
            bio TEXT,
            profile_image VARCHAR(255),
            phone VARCHAR(20),
            email VARCHAR(100),
            location VARCHAR(100),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_personal);

        // Social links table
        $sql_social = "CREATE TABLE IF NOT EXISTS social_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(100),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_social);

        // Education table
        $sql_education = "CREATE TABLE IF NOT EXISTS education (
            id INT AUTO_INCREMENT PRIMARY KEY,
            degree VARCHAR(200) NOT NULL,
            institution VARCHAR(200) NOT NULL,
            duration VARCHAR(100),
            gpa VARCHAR(20),
            description TEXT,
            year_start YEAR,
            year_end YEAR,
            is_current BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_education);

        // Skills categories table
        $sql_skill_categories = "CREATE TABLE IF NOT EXISTS skill_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(100) NOT NULL,
            icon VARCHAR(100),
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_skill_categories);

        // Skills table
        $sql_skills = "CREATE TABLE IF NOT EXISTS skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT,
            skill_name VARCHAR(100) NOT NULL,
            proficiency_level INT DEFAULT 80,
            icon VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
        )";
        $conn->exec($sql_skills);

        // Achievements table
        $sql_achievements = "CREATE TABLE IF NOT EXISTS achievements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            category VARCHAR(100),
            description TEXT,
            date_achieved DATE,
            position VARCHAR(100),
            organization VARCHAR(200),
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_achievements);

        // Projects table
        $sql_projects = "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            category VARCHAR(100),
            description TEXT,
            technologies TEXT,
            github_url VARCHAR(255),
            live_url VARCHAR(255),
            image VARCHAR(255),
            status VARCHAR(50) DEFAULT 'completed',
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_projects);

        // Certificates table
        $sql_certificates = "CREATE TABLE IF NOT EXISTS certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            issuer VARCHAR(200),
            issue_date DATE,
            expiry_date DATE,
            credential_id VARCHAR(100),
            credential_url VARCHAR(255),
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_certificates);

        // Experience table
        $sql_experience = "CREATE TABLE IF NOT EXISTS experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            position VARCHAR(200) NOT NULL,
            company VARCHAR(200) NOT NULL,
            location VARCHAR(100),
            start_date DATE,
            end_date DATE,
            is_current BOOLEAN DEFAULT FALSE,
            description TEXT,
            technologies TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_experience);

        // Gallery table for photos/designs
        $sql_gallery = "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200),
            category VARCHAR(100),
            image_path VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql_gallery);

        echo "All tables created successfully!<br>";

        // Insert default admin user (password: admin123)
        $admin_check = $conn->prepare("SELECT COUNT(*) FROM admin WHERE username = ?");
        $admin_check->execute(['arka_admin']);

        if ($admin_check->fetchColumn() == 0) {
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $insert_admin = $conn->prepare("INSERT INTO admin (username, password, email) VALUES (?, ?, ?)");
            $insert_admin->execute(['arka_admin', $admin_password, 'arka@example.com']);
            echo "Default admin user created - Username: arka_admin, Password: admin123<br>";
        }

        // Insert personal info
        $personal_check = $conn->prepare("SELECT COUNT(*) FROM personal_info");
        $personal_check->execute();

        if ($personal_check->fetchColumn() == 0) {
            $insert_personal = $conn->prepare("INSERT INTO personal_info (name, title, bio, email) VALUES (?, ?, ?, ?)");
            $insert_personal->execute([
                'Arka Braja Prasad Nath',
                'Computer Science & Engineering Student | Full Stack Developer',
                'Passionate Computer Science & Engineering student at KUET with expertise in full-stack development, machine learning, and mobile app development. Experienced in multiple programming languages and frameworks.',
                'arka@example.com'
            ]);
        }

        // Insert social links
        $social_links = [
            ['GitHub', 'https://github.com/AriyaArKa', 'fab fa-github'],
            ['Kaggle', 'https://www.kaggle.com/arkaariya', 'fab fa-kaggle'],
            ['LinkedIn', 'https://www.linkedin.com/in/arka-nath55/', 'fab fa-linkedin']
        ];

        foreach ($social_links as $link) {
            $social_check = $conn->prepare("SELECT COUNT(*) FROM social_links WHERE platform = ?");
            $social_check->execute([$link[0]]);

            if ($social_check->fetchColumn() == 0) {
                $insert_social = $conn->prepare("INSERT INTO social_links (platform, url, icon) VALUES (?, ?, ?)");
                $insert_social->execute($link);
            }
        }

        echo "Sample data inserted successfully!<br>";
        echo "<a href='admin/login.php'>Go to Admin Login</a> | <a href='index.php'>View Portfolio</a>";
    } catch (PDOException $e) {
        echo "Error creating tables: " . $e->getMessage();
    }
} else {
    echo "Failed to connect to database";
}
