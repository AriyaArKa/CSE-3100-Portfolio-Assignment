# Arka Braja Prasad Nath - Portfolio

A magical Harry Potter-themed portfolio website showcasing my journey as a Computer Science & Engineering student at KUET. This portfolio combines modern web technologies with the enchanting aesthetics of the wizarding world.

![Portfolio Hero Section](images/portfolioImage/hero.PNG)

## 🎬 **Live Demo & Video Walkthrough**

📹 **Watch the Portfolio in Action**: [Portfolio Demo Video](https://drive.google.com/drive/u/0/folders/1xKOpEkvkJDJ6KoFzL2-Ux_if0OnPBvnv)

_Experience the magical journey through my portfolio with a complete video demonstration showcasing all features, animations, and functionality._

---

### Features

### **Magical User Interface**

- **Harry Potter Theme**: Custom fonts, colors, and magical aesthetics throughout
- **Responsive Design**: Seamlessly adapts to all device sizes
- **Smooth Animations**: Engaging transitions and hover effects
- **Interactive Elements**: Dynamic components with magical touches

### **Portfolio Sections**

#### **Hero Section - "Welcome to My Portfolio"**

- Custom Harry Potter font (`HARRYP__.TTF`)
- Elegant gradient backgrounds
- Professional CTA button with hover effects
- Background image with opacity overlay

![Hero Section](images/portfolioImage/hero.PNG)

#### **The Marauder's Map (About Section)**

- Parchment-style background design
- Personal introduction and skills overview
- Professional summary with magical theming

![About Section](images/portfolioImage/aboutme.PNG)

#### **The Spellbook (Projects Section)**

- Interactive project slider with navigation
- Dynamic project cards with hover effects
- Technology tags and live demo links
- GitHub repository links

![Projects Section](images/portfolioImage/projects.PNG)

![Projects Section 2](images/portfolioImage/projects2.PNG)

#### **Wand Armory (Skills Section)**

- Categorized skill display with tabs
- Circular progress indicators
- Interactive skill categories
- Visual proficiency ratings

![Skills Section](images/portfolioImage/skillssection.PNG)

#### **Gringotts Vault (Achievements Section)**

- Flip card animations
- Newspaper-style achievement cards
- Date and category organization
- Professional achievement showcase

![Achievements Section](images/portfolioImage/achieveemntets1.PNG)

![Achievements Section 2](images/portfolioImage/achievements2.PNG)

#### 📰 **The Room of Requirement (Extracurricular Activities)**

- Daily Prophet newspaper clippings design
- Moving picture effects
- Real project images integration
- Activities timeline presentation

![Room of Requirement](images/portfolioImage/exracurricluar.PNG)

#### 🎓 **Hogwarts Records (Education Section)**

- Timeline-based education display
- Academic achievements highlighting
- Institution details and GPAs
- Clean, professional layout

![Education Section](images/portfolioImage/education.PNG)

#### 💬 **The Daily Prophet (Testimonials)**

- Parchment note design
- Star ratings system
- Client/colleague feedback
- Carousel slider presentation

![Testimonials Section](images/portfolioImage/testimonialssection.PNG)

#### 📧 **Send Me an Owl (Contact Section)**

- Magical contact form design
- Real-time form validation
- Professional contact information
- Social media integration

![Contact Section](images/portfolioImage/contactpageowl.PNG)

![Contact Messages](images/portfolioImage/conatcmes.PNG)

### **Cookie Management System**

- GDPR-compliant cookie consent
- Customizable cookie preferences
- Professional cookie settings modal
- Analytics and marketing cookie controls

![Cookie Consent](images/portfolioImage/cookies.PNG)

### 🛡️ **Admin Panel & Management System**

- Complete content management dashboard
- Secure admin authentication
- Project, skills, and achievement management
- Testimonial and message handling
- Professional admin interface

![Admin Login](images/portfolioImage/adminlogin.PNG)

![Admin Dashboard](images/portfolioImage/admindashbaord.PNG)

![Admin Projects](images/portfolioImage/adminprojects.PNG)

![Admin Skills](images/portfolioImage/adminskills.PNG)

![Admin Achievements](images/portfolioImage/adminachieve.PNG)

![Admin Testimonials](images/portfolioImage/admintestsi.PNG)

## **Technology Stack**

### **Frontend**

- **HTML5**: Semantic markup and structure
- **CSS3**: Advanced styling with gradients, animations, and responsive design
- **JavaScript (ES6+)**: Interactive functionality and dynamic content
- **PHP**: Server-side rendering and database integration

### **Backend & Database**

- **PHP**: Server-side logic and form handling
- **MySQL**: Database for projects, skills, achievements, and testimonials
- **PDO**: Secure database connections and queries

### **Design & Assets**

- **Custom Fonts**: Harry Potter themed typography
- **Responsive Images**: Optimized for all devices
- **CSS Animations**: Smooth transitions and hover effects
- **Glass Morphism**: Modern UI design trends

### **Features & Functionality**

- **Contact Form**: Email integration with validation
- **Admin Panel**: Content management system
- **Cookie Management**: GDPR compliance
- **Mobile Navigation**: Responsive menu system
- **Image Optimization**: Fast loading and responsive images

## **Project Structure**

```
Portfolio/
├── index.php                 # Main portfolio page
├── config.php                # Database configuration
├── style.css                 # Main stylesheet
├── script.js                 # JavaScript functionality
├── admin/                    # Admin panel for content management
│   ├── dashboard.php         # Admin dashboard
│   ├── projects.php          # Project management
│   ├── skills.php            # Skills management
│   ├── achievements.php      # Achievement management
│   ├── testimonials.php      # Testimonial management
│   └── messages.php          # Contact message management
├── images/                   # Image assets
│   ├── projectsImage/        # Project-specific images
│   └── portfolioImage/       # Portfolio section screenshots
├── font/                     # Custom fonts
│   └── HARRYP__.TTF          # Harry Potter font
└── database.sql              # Database schema
```

## **Getting Started**

### **Prerequisites**

- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### **Installation**

1. **Clone the repository**

   ```bash
   git clone https://github.com/AriyaArKa/Portfolio.git
   cd Portfolio
   ```

2. **Set up database**

   - Import `database.sql` into your MySQL database
   - Update database credentials in `config.php`

3. **Configure web server**

   - Place files in your web server document root
   - Ensure PHP and MySQL are running

4. **Access the portfolio**
   - Navigate to `http://localhost/Portfolio` in your browser

### **Admin Panel Access**

- URL: `http://localhost/Portfolio/admin`
- Manage all portfolio content through the admin interface

## **Database Schema**

### **Tables**

- `projects`: Portfolio projects with descriptions, technologies, and links
- `skills`: Technical skills with categories and proficiency levels
- `achievements`: Professional achievements with dates and descriptions
- `testimonials`: Client/colleague feedback and ratings
- `messages`: Contact form submissions
