# Dynamic Portfolio Website with Admin Panel

A comprehensive, dynamic portfolio website built with PHP, MySQL, HTML, CSS, and JavaScript. Features a complete admin panel for managing all portfolio content including education, skills, achievements, projects, and social links.

## Features

### 🎨 Frontend Portfolio

- **Responsive Design**: Modern, mobile-first design with Bootstrap 5
- **Interactive Animations**: Smooth scroll animations with AOS library
- **Dynamic Content**: All content loaded from database
- **Professional Layout**: Clean sections for education, skills, achievements, and projects
- **Social Integration**: Dynamic social media links
- **Contact Information**: Easy-to-find contact details

### 🔧 Admin Panel

- **Secure Authentication**: Password-protected admin access
- **Complete CRUD Operations**: Create, Read, Update, Delete for all sections
- **Personal Information Management**: Update name, bio, contact details
- **Education Management**: Add/edit educational qualifications
- **Skills Management**: Organize skills by categories with proficiency levels
- **Achievements Management**: Showcase awards, competitions, and recognitions
- **Projects Management**: Portfolio projects with GitHub and live demo links
- **Social Links Management**: Manage social media profiles
- **Dashboard Overview**: Quick stats and management options

### 🛡️ Security Features

- **Password Hashing**: Secure password storage using PHP password_hash()
- **Session Management**: Secure admin sessions
- **SQL Injection Protection**: Prepared statements throughout
- **Input Validation**: Form validation and sanitization

## Installation Guide

### Prerequisites

- **XAMPP** (includes Apache, MySQL, PHP)
- **Web Browser** (Chrome, Firefox, Safari, etc.)

### Step 1: Setup XAMPP

1. Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start XAMPP Control Panel
3. Start **Apache** and **MySQL** services

### Step 2: Configure Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `portfolio_db`
3. Set your MySQL password to `arka` (or update the password in `config/database.php`)

### Step 3: Install Portfolio

1. Copy the portfolio files to: `c:\xampp\htdocs\Portfolio\new\`
2. Navigate to: `http://localhost/Portfolio/new/setup.php`
3. This will create all necessary database tables and default data

### Step 4: Access the Portfolio

- **Portfolio**: `http://localhost/Portfolio/new/`
- **Admin Panel**: `http://localhost/Portfolio/new/admin/login.php`

### Default Admin Credentials

- **Username**: `arka_admin`
- **Password**: `admin123`

## Database Structure

The system uses the following main tables:

- `admin` - Admin authentication
- `personal_info` - Personal information
- `education` - Educational qualifications
- `skill_categories` - Skill categories (Programming, Frontend, etc.)
- `skills` - Individual skills with proficiency levels
- `achievements` - Awards, competitions, recognitions
- `projects` - Portfolio projects
- `social_links` - Social media profiles
- `certificates` - Certificates and certifications
- `experience` - Work experience
- `gallery` - Images and design work

## Customization

### Updating Your Information

1. **Login to Admin Panel**: Go to `admin/login.php`
2. **Personal Info**: Update your name, bio, contact details
3. **Education**: Add your educational background
4. **Skills**: Organize your skills by categories
5. **Achievements**: Add competitions, awards, recognition
6. **Projects**: Showcase your projects with links
7. **Social Links**: Add your social media profiles

### Adding Sample Data

The system includes sample data based on your provided information:

- **Education**: KUET, HSC, SSC details
- **Skills**: Programming languages, frameworks, tools
- **Achievements**: BITFEST, competitions, volunteering
- **Projects**: Android games, AI chatbot, ML projects
- **Social Links**: GitHub, Kaggle, LinkedIn

### Styling Customization

- **Colors**: Update CSS variables in `index.php` or create separate CSS file
- **Layout**: Modify Bootstrap classes and custom styles
- **Animations**: Adjust AOS animation settings
- **Typography**: Change fonts in CSS

## File Structure

```
Portfolio/new/
├── config/
│   └── database.php          # Database connection
├── admin/
│   ├── login.php            # Admin login
│   ├── dashboard.php        # Admin dashboard
│   ├── personal_info.php    # Personal info management
│   ├── education.php        # Education management
│   ├── skills.php           # Skills management
│   ├── achievements.php     # Achievements management
│   ├── projects.php         # Projects management
│   ├── social_links.php     # Social links management
│   └── logout.php           # Logout
├── index.php                # Main portfolio page
├── setup.php                # Database setup
└── README.md                # This file
```

## Technologies Used

### Backend

- **PHP 7.4+**: Server-side scripting
- **MySQL**: Database management
- **PDO**: Database abstraction layer

### Frontend

- **HTML5**: Markup structure
- **CSS3**: Styling and animations
- **JavaScript**: Interactive functionality
- **Bootstrap 5**: Responsive framework
- **Font Awesome 6**: Icons
- **AOS Library**: Scroll animations

### Security

- **Password Hashing**: PHP password_hash()
- **Prepared Statements**: SQL injection prevention
- **Session Management**: Secure authentication
- **Input Validation**: XSS protection

## Features in Detail

### Admin Dashboard

- Overview statistics for all content sections
- Quick action buttons for adding new content
- Recent activity summary
- Direct links to manage each section

### Content Management

- **Education**: Degree, institution, GPA, duration, current status
- **Skills**: Categories, proficiency levels, icons, visual progress bars
- **Achievements**: Title, organization, position, date, description, images
- **Projects**: Title, category, description, technologies, GitHub/demo links, featured status
- **Social Links**: Platform selection, custom icons, active/inactive status

### Portfolio Display

- **Hero Section**: Name, title, bio, social links
- **Education Timeline**: Chronological education history
- **Skills Showcase**: Categorized skills with proficiency indicators
- **Achievements Gallery**: Award and recognition highlights
- **Projects Portfolio**: Featured and regular projects with links
- **Contact Section**: Easy access to contact information

## Support and Maintenance

### Regular Updates

- Keep personal information current
- Update project statuses and add new projects
- Refresh skills and proficiency levels
- Add new achievements and certifications

### Security Best Practices

- Change default admin password
- Regular database backups
- Keep PHP and dependencies updated
- Monitor for suspicious admin access attempts

### Performance Optimization

- Optimize images for web
- Regular database cleanup
- Monitor database queries
- Consider caching for high traffic

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Check MySQL service is running
   - Verify database credentials in `config/database.php`
   - Ensure database `portfolio_db` exists

2. **Admin Login Not Working**

   - Check default credentials: `arka_admin` / `admin123`
   - Verify admin table has been created
   - Check session configuration

3. **Images Not Displaying**

   - Verify image URLs are accessible
   - Check file permissions
   - Ensure proper image formats (jpg, png, gif)

4. **Styling Issues**
   - Clear browser cache
   - Check Bootstrap and Font Awesome CDN links
   - Verify CSS file paths

## Future Enhancements

- **File Upload System**: Direct image upload instead of URLs
- **Email Contact Form**: Contact form with email notifications
- **Blog System**: Add blog functionality for articles
- **Analytics Dashboard**: Visitor statistics and engagement metrics
- **Multi-language Support**: Portfolio in multiple languages
- **Advanced Gallery**: Better image management and galleries
- **SEO Optimization**: Meta tags and structured data
- **Export Functionality**: PDF resume generation

## License

This project is open source and available under the MIT License.

## Contact

For support or questions about this portfolio system, please contact:

- **Developer**: Arka Braja Prasad Nath
- **GitHub**: [https://github.com/AriyaArKa](https://github.com/AriyaArKa)
- **Email**: Contact through the portfolio contact form

---

**Note**: Remember to change the default admin password after first login for security purposes!
