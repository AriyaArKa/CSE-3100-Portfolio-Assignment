"# Portfolio Management System

A complete full-stack portfolio website for **Arka Braja Prasad Nath** built with PHP, MySQL, HTML, CSS, and JavaScript. Features dark/light theme, responsive design, and a comprehensive admin panel.

## Features

### Frontend Features

- **Responsive Design**: Works perfectly on all devices
- **Dark/Light Theme**: Toggle between themes with persistent settings
- **Modern UI**: Clean, professional design with smooth animations
- **Interactive Elements**: Typing effect, parallax scrolling, smooth navigation
- **Contact Form**: Ajax-powered contact form with validation
- **Project Portfolio**: Detailed project showcases with filtering
- **Skills Section**: Animated progress bars and categorized skills
- **Achievement Gallery**: Showcase of awards and certifications

### Backend Features

- **Admin Dashboard**: Complete content management system
- **CRUD Operations**: Full create, read, update, delete functionality
- **File Upload**: Image and document upload with validation
- **Database Management**: Well-structured MySQL database
- **Security**: Password hashing, CSRF protection, input sanitization
- **API Endpoints**: RESTful API for contact form and data management

### Admin Panel Features

- **Dashboard**: Overview with statistics and quick actions
- **Personal Info Management**: Update profile, contact details, and bio
- **Education Management**: Add/edit educational background
- **Skills Management**: Organize skills by categories with proficiency levels
- **Project Management**: Full project portfolio management
- **Achievement Management**: Track awards, certifications, and milestones
- **Message Management**: View and respond to contact form submissions
- **Settings**: Site configuration and preferences

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Styling**: CSS Grid, Flexbox, CSS Variables
- **Icons**: Font Awesome 6
- **Animations**: CSS Animations, AOS (Animate On Scroll)
- **Forms**: Native form validation + custom JavaScript validation

## Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Step 1: Download and Extract

1. Download the portfolio files
2. Extract to your web server directory (e.g., `htdocs`, `www`, etc.)

### Step 2: Database Setup

1. Create a new MySQL database named `portfolio_db`
2. Import the SQL file:
   ```sql
   mysql -u your_username -p portfolio_db < database/portfolio.sql
   ```
3. Update database credentials in `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'portfolio_db');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

### Step 3: Configure Permissions

1. Set write permissions for uploads directory:
   ```bash
   chmod 755 uploads/
   chmod 755 assets/images/
   ```

### Step 4: Admin Access

1. Default admin credentials:
   - **Username**: `admin`
   - **Password**: `password`
2. **Important**: Change the default password immediately after first login!

### Step 5: Site Configuration

1. Update site URL in `config/config.php`:
   ```php
   define('SITE_URL', 'http://your-domain.com');
   ```
2. Configure email settings if needed for contact form notifications

## File Structure

```
Portfolio/
├── admin/                  # Admin panel
│   ├── assets/
│   │   ├── css/           # Admin CSS files
│   │   └── js/            # Admin JavaScript files
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   └── [other admin pages]
├── api/                   # API endpoints
│   └── contact.php        # Contact form handler
├── assets/                # Frontend assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # Main JavaScript
│   └── images/            # Site images
├── config/                # Configuration files
│   ├── config.php         # Main configuration
│   └── database.php       # Database connection
├── database/              # Database files
│   └── portfolio.sql      # Database schema and data
├── uploads/               # User uploaded files
├── index.php              # Homepage
├── projects.php           # Projects listing page
└── README.md              # This file
```

## Usage Guide

### For Website Visitors

1. **Browse Portfolio**: Navigate through sections using the menu
2. **View Projects**: Click on projects to see details and links
3. **Contact**: Use the contact form to send messages
4. **Theme Toggle**: Click the theme button to switch between light/dark modes

### For Admin (Portfolio Owner)

1. **Access Admin Panel**: Go to `your-site.com/admin`
2. **Login**: Use your admin credentials
3. **Manage Content**:
   - Update personal information and bio
   - Add/edit education, skills, projects, and achievements
   - View and respond to contact messages
   - Configure site settings

### Adding New Content

1. **Projects**: Go to Admin → Projects → Add New
2. **Skills**: Go to Admin → Skills → Add New (organize by category)
3. **Education**: Go to Admin → Education → Add New
4. **Achievements**: Go to Admin → Achievements → Add New

## Customization

### Theme Colors

Edit CSS variables in `assets/css/style.css`:

```css
:root {
  --primary-color: #3b82f6;
  --secondary-color: #8b5cf6;
  --accent-color: #f59e0b;
  /* ... other variables */
}
```

### Adding New Sections

1. Create database table for new content type
2. Add admin management page
3. Update frontend to display new content
4. Add navigation links if needed

### Email Configuration

Update email settings in `config/config.php` for contact form notifications:

```php
define('SMTP_HOST', 'your-smtp-server.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@domain.com');
define('SMTP_PASSWORD', 'your-email-password');
```

## Security Features

- **Password Hashing**: All passwords use PHP's `password_hash()`
- **Input Sanitization**: All user inputs are sanitized
- **CSRF Protection**: Forms include CSRF token validation
- **File Upload Security**: File type and size validation
- **SQL Injection Prevention**: Prepared statements used throughout
- **Session Security**: Secure session management

## Performance Features

- **Lazy Loading**: Images load as needed
- **Optimized CSS**: Efficient selectors and minimal reflows
- **Database Optimization**: Indexed queries and efficient joins
- **Asset Compression**: Minified CSS and JavaScript (can be added)
- **Caching Headers**: Browser caching for static assets

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Check database credentials in `config/config.php`
   - Ensure MySQL service is running
   - Verify database exists and is accessible

2. **File Upload Issues**

   - Check directory permissions (755 for uploads/)
   - Verify file size limits in PHP configuration
   - Ensure allowed file types are correct

3. **Admin Login Issues**

   - Verify admin user exists in database
   - Check password hash in database
   - Clear browser cookies/cache

4. **Email Not Working**

   - Configure SMTP settings in config
   - Check server mail functionality
   - Verify email addresses are correct

5. **Theme Not Persisting**
   - Check cookie settings
   - Verify JavaScript is enabled
   - Clear browser cache

### Error Logging

Enable error logging by setting `DEBUG_MODE` to `true` in `config/config.php`:

```php
define('DEBUG_MODE', true);
```

## License

This project is created for **Arka Braja Prasad Nath's** personal portfolio. Feel free to use as inspiration for your own portfolio projects.

## Support

For technical support or questions about customization, please refer to the code comments and documentation within the files.

## Updates and Maintenance

### Regular Maintenance

1. **Database Backups**: Regular backups of the database
2. **Security Updates**: Keep PHP and MySQL updated
3. **Content Updates**: Regular updates to projects and achievements
4. **Performance Monitoring**: Monitor site speed and optimize as needed

### Future Enhancements

- Blog/Articles section
- Image optimization and compression
- Advanced analytics and visitor tracking
- Social media integration
- PDF resume generation
- Multi-language support

---

**Built with ❤️ for Arka Braja Prasad Nath**
_Computer Science & Engineering Student at KUET_"
