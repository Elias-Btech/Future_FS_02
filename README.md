# 🚀 CRM Pulse – Mini Lead Management System

A modern, lightweight Customer Relationship Management (CRM) system built with PHP for managing leads, tracking follow-ups, and generating business reports.

**Project:** Full Stack Web Development – Task 2 (2026) | Future Interns

---

## ✨ Features

### 📊 Dashboard
- Real-time statistics (Total Leads, New Leads, Follow-ups, Conversions)
- Interactive charts showing leads over time, status distribution, and source breakdown
- Recent activity feed
- Quick access to overdue follow-ups

### 👥 Lead Management
- Create, view, edit, and delete leads
- Track lead information: name, email, phone, company, source
- Assign priority levels (Low, Medium, High)
- Manage lead status (New, Contacted, Follow-up, Converted, Closed)
- Search and filter leads by status, priority, or source

### 📅 Follow-up System
- Schedule follow-up dates for leads
- Add notes and track communication history
- Automatic overdue follow-up alerts
- Activity logging for all lead interactions

### 📈 Reports & Analytics
- Comprehensive business analytics
- Lead status breakdown with visual progress bars
- Source performance tracking
- Export data to CSV for external analysis
- Conversion rate calculations

### 🎨 User Interface
- Clean, modern design with Tailwind CSS
- Responsive layout (mobile, tablet, desktop)
- Dark mode support
- Smooth animations and transitions
- Intuitive navigation with sidebar menu

### 🔐 Security
- Secure authentication system
- Session management
- Input validation and sanitization
- SQL injection protection with PDO prepared statements
- Password hashing with bcrypt

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| **PHP 8+** | Backend logic with OOP architecture |
| **MySQL** | Database management |
| **PDO** | Secure database interactions |
| **Tailwind CSS** | Modern, utility-first styling |
| **Chart.js** | Interactive data visualizations |
| **Vanilla JavaScript** | Client-side interactivity |
| **Bootstrap Icons** | Icon library |

---

## 📦 Installation & Setup

### Prerequisites
- XAMPP (or any PHP 8+ and MySQL environment)
- Web browser (Chrome, Firefox, Edge, etc.)

### Step-by-Step Installation

1. **Download and Extract**
   - Extract the project folder to your XAMPP directory
   - Recommended path: `C:/xampp/htdocs/crm/`

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

3. **Create Database**
   - Open your browser and go to `http://localhost/phpmyadmin`
   - Create a new database named `crm_db`
   - Click on the database, then go to the **Import** tab
   - Select the file `database/schema.sql` from the project folder
   - Click **Go** to import the database structure and sample data

4. **Configure Database Connection** (if needed)
   - Open `config/database.php`
   - Update credentials if your MySQL setup is different:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'crm_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

5. **Access the Application**
   - Open your browser and visit: `http://localhost/crm/public/`
   - You'll be redirected to the login page

6. **Login**
   - **Username:** `admin`
   - **Password:** `Admin@1234`

---

## 📁 Project Structure

```
crm/
├── app/                        # Core application logic
│   ├── Core/                   # Framework core classes
│   │   ├── Auth.php           # Authentication & authorization
│   │   ├── Database.php       # PDO database connection
│   │   ├── Session.php        # Session management
│   │   ├── Validator.php      # Input validation
│   │   └── Helper.php         # Utility functions
│   │
│   └── Models/                 # Database models
│       ├── Admin.php          # Admin user model
│       ├── Lead.php           # Lead management
│       ├── FollowUp.php       # Follow-up tracking
│       ├── ActivityLog.php    # Activity logging
│       └── BaseModel.php      # Base model class
│
├── config/                     # Configuration files
│   ├── app.php                # Application settings
│   └── database.php           # Database credentials
│
├── database/                   # Database files
│   └── schema.sql             # Database structure + sample data
│
├── public/                     # Web-accessible files
│   ├── index.php              # Entry point
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   ├── logout.php             # Logout handler
│   ├── dashboard.php          # Main dashboard
│   ├── home.php               # Public homepage
│   │
│   ├── leads/                 # Lead management pages
│   │   ├── index.php          # List all leads
│   │   ├── create.php         # Add new lead
│   │   ├── edit.php           # Edit lead
│   │   ├── view.php           # View lead details
│   │   ├── delete.php         # Delete lead
│   │   └── update_status.php  # Update lead status
│   │
│   └── reports/               # Reports & analytics
│       └── index.php          # Reports dashboard
│
├── views/                      # HTML templates
│   └── layouts/
│       └── main.php           # Main layout (sidebar + topbar)
│
├── assets/                     # Static assets
│   ├── css/
│   │   └── custom.css         # Custom styles
│   └── js/
│       └── app.js             # JavaScript functionality
│
├── docs/                       # Documentation
│   └── architecture.md        # Detailed architecture
│
├── .htaccess                   # URL rewriting & security
└── README.md                   # This file
```

For detailed architecture information, see [`docs/architecture.md`](docs/architecture.md)

---

## 🎯 Usage Guide

### Managing Leads

1. **Add a New Lead**
   - Click "Add Lead" button in the top right or sidebar
   - Fill in lead information (name, email, phone, company, source)
   - Set priority and status
   - Click "Create Lead"

2. **View Lead Details**
   - Click on any lead name from the leads list
   - View complete lead information
   - See follow-up history and activity log

3. **Edit Lead**
   - Open lead details page
   - Click "Edit Lead" button
   - Update information and save

4. **Update Lead Status**
   - Use the status dropdown on the lead details page
   - Status options: New → Contacted → Follow-up → Converted → Closed

### Follow-ups

1. **Schedule Follow-up**
   - Open lead details page
   - Click "Add Follow-up" button
   - Set follow-up date and add notes
   - Save to schedule

2. **View Overdue Follow-ups**
   - Check the dashboard for overdue alerts
   - Click "Follow-ups" in the sidebar
   - Filter by status to see pending items

### Reports

1. **View Analytics**
   - Click "Reports" in the sidebar
   - View conversion rates, status breakdown, and source performance

2. **Export Data**
   - Click "Export CSV" button on the reports page
   - Download complete lead data for external analysis

---

## 🔧 Configuration

### Application Settings
Edit `config/app.php` to customize:
- Application name
- Base URL
- Timezone
- Date format

### Database Settings
Edit `config/database.php` for:
- Database host
- Database name
- Username and password

---

## 🎨 Customization

### Styling
- Main styles: `assets/css/custom.css`
- Tailwind configuration: Inline in `views/layouts/main.php`
- Color scheme: Modify CSS variables for brand colors

### Features
- Add new lead sources: Update dropdown in `public/leads/create.php`
- Modify status options: Edit `Lead.php` model
- Add custom fields: Update database schema and forms

---

## 🐛 Troubleshooting

### Common Issues

**Problem:** "Database connection failed"
- **Solution:** Check MySQL is running in XAMPP, verify credentials in `config/database.php`

**Problem:** "Page not found" or 404 errors
- **Solution:** Ensure `.htaccess` file exists and Apache `mod_rewrite` is enabled

**Problem:** "Permission denied" errors
- **Solution:** Check folder permissions, ensure Apache has read/write access

**Problem:** Charts not displaying
- **Solution:** Check browser console for JavaScript errors, ensure internet connection for CDN resources

**Problem:** Login not working
- **Solution:** Verify database was imported correctly, check `admins` table has default user

---

## 📝 Default Credentials

After importing the database, use these credentials:

| Username | Password | Role |
|----------|----------|------|
| `admin` | `Admin@1234` | Administrator |

**⚠️ Important:** Change the default password after first login for security.

---

## 🚀 Future Enhancements

Potential features for future development:
- [ ] Email notifications for follow-ups
- [ ] Advanced search and filtering
- [ ] Lead assignment to multiple users/teams
- [ ] Calendar view for follow-ups
- [ ] API endpoints for integrations
- [ ] Bulk import/export functionality
- [ ] Custom fields and forms
- [ ] Email templates
- [ ] SMS integration
- [ ] Advanced reporting with date ranges

---

## 📄 License

This project is developed as part of the Future Interns Full Stack Web Development program (2026).

---

## 👨‍💻 Developer Notes

### Code Standards
- PHP: PSR-12 coding standards
- Database: PDO with prepared statements
- Security: Input validation, XSS protection, CSRF tokens
- Architecture: MVC-inspired structure with OOP principles

### Database Schema
- **admins**: User authentication
- **leads**: Lead information
- **follow_ups**: Follow-up scheduling and notes
- **activity_logs**: System activity tracking

---

## 📞 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review `docs/architecture.md` for technical details
3. Inspect browser console for JavaScript errors
4. Check Apache/PHP error logs in XAMPP

---

**Built with ❤️ for Future Interns | 2026**
