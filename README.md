# Portfolio Website with Admin Panel

A complete single-page portfolio website with an admin panel for managing content, built with PHP, HTML, CSS, and JavaScript.

## Features

### Portfolio Website

- **Single-page design** with smooth scrolling navigation
- **Responsive design** that works on all devices
- **Hero section** with social media links
- **About section** with education details
- **Projects showcase** with GitHub links
- **Skills section** with proficiency bars
- **Achievements section** with extracurricular activities
- **Testimonials slider**
- **Contact form** with database storage
- **Modern animations** and hover effects

### Admin Panel

- **Secure login system** with session management
- **Dashboard** with statistics and quick actions
- **CRUD operations** for:
  - Projects (add, edit, delete, activate/deactivate)
  - Skills (add, edit, delete with categories and proficiency)
  - Achievements (add, edit, delete with dates and categories)
  - Testimonials (add, edit, delete with ratings)
  - Messages (view, mark as read/unread, delete)
- **Responsive admin interface**
- **Search and filter** functionality

## Setup Instructions

### 1. Requirements

- XAMPP (or any PHP server with MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### 2. Installation Steps

1. **Download and extract** the files to your XAMPP htdocs directory:

   ```
   c:\xampp\htdocs\Portfolio\final\
   ```

2. **Start XAMPP** services:

   - Start Apache
   - Start MySQL

3. **Create the database**:

   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database.sql` file to create the database and tables with sample data

4. **Configure database connection** (if needed):

   - Edit `config.php` if your database settings are different
   - Default settings: host=localhost, user=root, password=(empty), database=portfolio_db

5. **Access the website**:
   - Portfolio: http://localhost/Portfolio/final/
   - Admin Panel: http://localhost/Portfolio/final/admin/

### 3. Default Admin Credentials

- **Username**: admin
- **Password**: admin123

⚠️ **Important**: Change the default admin password after first login!

## File Structure

```
Portfolio/final/
├── index.php              # Main portfolio page
├── config.php             # Database configuration
├── style.css              # All CSS styles
├── script.js              # JavaScript functionality
├── database.sql           # Database schema and sample data
├── admin/                 # Admin panel directory
│   ├── index.php          # Admin login page
│   ├── auth.php           # Authentication helper
│   ├── dashboard.php      # Admin dashboard
│   ├── projects.php       # Projects management
│   ├── skills.php         # Skills management
│   ├── achievements.php   # Achievements management
│   ├── testimonials.php   # Testimonials management
│   ├── messages.php       # Messages management
│   └── logout.php         # Logout functionality
└── README.md              # This file
```

## Database Tables

- **admin**: Admin user credentials
- **projects**: Portfolio projects with links and technologies
- **skills**: Technical skills with categories and proficiency levels
- **achievements**: Awards and accomplishments with dates
- **testimonials**: Client/colleague testimonials with ratings
- **messages**: Contact form submissions

## Customization

### 1. Personal Information

Edit the following in `index.php`:

- Name, tagline, and social links in the hero section
- Education details in the about section
- Contact information

### 2. Styling

Modify `style.css` to change:

- Colors and fonts
- Layout and spacing
- Animations and effects

### 3. Content

Use the admin panel to manage:

- Add/edit/delete projects
- Update skills and proficiency levels
- Add achievements and awards
- Manage testimonials
- View and respond to messages

## Security Features

- **Password hashing** using PHP's password_hash()
- **SQL injection protection** using prepared statements
- **Session management** for admin authentication
- **Input validation** and sanitization
- **CSRF protection** through form tokens

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Troubleshooting

### Common Issues

1. **Database connection error**:

   - Check XAMPP MySQL service is running
   - Verify database credentials in `config.php`
   - Ensure database is imported correctly

2. **Admin login not working**:

   - Check if database is imported with admin user
   - Try clearing browser cache and cookies
   - Verify session support is enabled in PHP

3. **Images not displaying**:

   - Check image URLs are valid
   - Ensure image paths are accessible
   - Use placeholder images for testing

4. **Contact form not working**:
   - Check database connection
   - Verify table structure matches schema
   - Check PHP error logs

## Performance Optimization

- Images are loaded with lazy loading
- CSS and JS are minified for production
- Database queries are optimized
- Caching headers can be added for static assets

## Future Enhancements

- **File upload** for project images and testimonials
- **Email notifications** for new messages
- **Analytics dashboard** with visitor statistics
- **SEO optimization** with meta tags
- **Multi-language support**
- **Dark mode toggle**
- **Social media integration**

## Support

For any issues or questions:

1. Check the troubleshooting section above
2. Review PHP error logs in XAMPP
3. Verify database structure matches schema
4. Ensure all files are uploaded correctly

---

**Created by**: Arka Braja Prasad Nath  
**Date**: September 2025  
**Version**: 1.0
