# Project Architecture

```
crm/
│
├── app/                        # Core application logic (OOP)
│   ├── Controllers/            # Handle requests, call models, load views
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── LeadController.php
│   │   ├── FollowUpController.php
│   │   └── ReportController.php
│   │
│   ├── Models/                 # Database interaction (PDO)
│   │   ├── Admin.php
│   │   ├── Lead.php
│   │   ├── FollowUp.php
│   │   └── ActivityLog.php
│   │
│   └── Core/                   # Framework core classes
│       ├── Database.php        # PDO singleton connection
│       ├── Session.php         # Session management
│       ├── Auth.php            # Authentication guard
│       ├── Validator.php       # Input validation
│       └── Helper.php          # Utility functions
│
├── config/                     # Configuration files
│   ├── database.php            # DB credentials
│   └── app.php                 # App settings (name, timezone, etc.)
│
├── database/                   # SQL files
│   ├── schema.sql              # Full database structure + sample data
│   └── migrations/             # Future migration files
│
├── public/                     # Web root (only this folder is public)
│   ├── index.php               # Entry redirect
│   ├── login.php               # Login page
│   ├── register.php            # Register page
│   ├── logout.php              # Logout handler
│   ├── dashboard.php           # Main dashboard
│   ├── leads/
│   │   ├── index.php           # All leads list
│   │   ├── create.php          # Add new lead
│   │   ├── edit.php            # Edit lead
│   │   ├── view.php            # Lead detail page
│   │   └── delete.php          # Delete handler
│   ├── followups/
│   │   ├── create.php          # Add follow-up note
│   │   └── edit.php            # Edit follow-up
│   ├── reports/
│   │   └── index.php           # Reports + export
│   └── contact.php             # Public contact form
│
├── views/                      # HTML templates (loaded by controllers)
│   ├── layouts/
│   │   ├── main.php            # Main layout (sidebar + topbar)
│   │   └── auth.php            # Auth layout (login/register)
│   ├── dashboard/
│   │   └── index.php
│   ├── leads/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── view.php
│   ├── followups/
│   │   └── list.php
│   ├── reports/
│   │   └── index.php
│   └── partials/
│       ├── sidebar.php
│       ├── topbar.php
│       ├── alerts.php
│       └── pagination.php
│
├── assets/                     # Static files
│   ├── js/
│   │   └── app.js              # Main JavaScript
│   └── css/
│       └── custom.css          # Custom styles on top of Tailwind
│
├── storage/                    # Writable storage
│   └── exports/                # CSV/Excel exports
│
└── .htaccess                   # URL rewriting + security
```
