<div align="center">

<img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
<img src="https://img.shields.io/badge/Chart.js-4.4-FF6384?style=for-the-badge&logo=chart.js&logoColor=white" />
<img src="https://img.shields.io/badge/License-MIT-22c55e?style=for-the-badge" />

<br/><br/>

<h1>⚡ CRM Pulse</h1>
<h3>Smart Lead Management System for Modern Businesses</h3>

<p>A full-stack, production-ready Mini CRM built with PHP 8, MySQL, and Tailwind CSS.<br/>
Track leads, automate follow-ups, and grow your business — all from one beautiful dashboard.</p>

<br/>

<a href="#-demo">View Demo</a> · <a href="#-features">Features</a> · <a href="#-quick-start">Quick Start</a> · <a href="#-project-structure">Structure</a>

</div>

---

## 📸 Preview

| Home Page | Dashboard | Reports |
|-----------|-----------|---------|
| Modern landing page with hero, features, pricing | Real-time stats, charts, activity feed | Analytics, pipeline breakdown, CSV export |

---

## ✨ Features

### 🔐 Authentication & Security
- Session-based login with **bcrypt** password hashing
- Protected routes — unauthenticated users are redirected
- Input validation and **XSS sanitization** on all forms
- SQL injection prevention via **PDO prepared statements**

### 👥 Lead Management
- Full **CRUD** — Create, Read, Update, Delete leads
- Track name, email, phone, company, source, status, priority
- Instant **search** by name, email, or company
- **Filter** by status, priority, source, and date range

### 📅 Follow-up System
- Schedule follow-up dates per lead
- Add timestamped notes and interaction history
- **Overdue alerts** on the dashboard — never miss a follow-up
- Full activity log for every action taken

### � Dashboard & Analytics
- Live stat cards — Total, New, Follow-ups, Converted
- **Line chart** — Leads over the last 7 days
- **Doughnut chart** — Lead status distribution
- **Bar chart** — Leads by source
- Pipeline breakdown with progress bars
- Recent leads table + activity feed

### 📈 Reports & Export
- Conversion rate calculations
- Status and source breakdown with visual bars
- **One-click CSV export** of all lead data

### 🎨 UI / UX
- Fully **responsive** — mobile, tablet, desktop
- Clean modern design with **Tailwind CSS**
- Smooth animations and scroll reveal effects
- Beautiful public landing page with pricing, testimonials, and CTA

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8+ · OOP · MVC-inspired architecture |
| **Database** | MySQL 8 · PDO · Prepared Statements |
| **Frontend** | Tailwind CSS · Vanilla JavaScript · Bootstrap Icons |
| **Charts** | Chart.js 4.4 |
| **Fonts** | Inter · Plus Jakarta Sans (Google Fonts) |
| **Server** | Apache · XAMPP |

---

## � Quick Start

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8+ & MySQL)
- A modern web browser

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/Elias-Araya/FUTURE_FS_02.git
cd FUTURE_FS_02
```

**2. Move to XAMPP htdocs**
```
C:/xampp/htdocs/FUTURE_FS_02/
```

**3. Start XAMPP**
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

**4. Create the database**
- Go to `http://localhost/phpmyadmin`
- Create a database named `crm_db`
- Click **Import** → select `database/schema.sql` → click **Go**

**5. Configure database connection**
```bash
cp config/database.example.php config/database.php
```
Edit `config/database.php` if your MySQL credentials differ from the defaults.

**6. Open the app**
```
http://localhost/FUTURE_FS_02/public/
```

### Default Login
| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `Admin@1234` |

> ⚠️ Change the default password after your first login.

---

## 📁 Project Structure

```
FUTURE_FS_02/
│
├── 🧠 app/                          # Core application logic
│   │
│   ├── 🔧 Core/
│   │   ├── Auth.php                 # 🔐 Authentication & session guard
│   │   ├── Database.php             # 🗄️  PDO singleton connection
│   │   ├── Helper.php               # 🛠️  Utility & formatting functions
│   │   ├── Session.php              # 💾 Session management
│   │   └── Validator.php            # ✅ Input validation engine
│   │
│   └── 📦 Models/
│       ├── BaseModel.php            # 🏗️  Shared model methods
│       ├── Admin.php                # 👤 Admin user model
│       ├── Lead.php                 # 🎯 Lead CRUD & stats
│       ├── FollowUp.php             # 📅 Follow-up scheduling
│       └── ActivityLog.php          # 📋 Action logging
│
├── ⚙️  config/
│   ├── app.php                      # 🌐 App name, URL, timezone
│   └── database.example.php         # 🔑 DB config template
│
├── 🗄️  database/
│   └── schema.sql                   # 📊 Full DB schema + seed data
│
├── 🌍 public/                       # Web-accessible entry points
│   ├── index.php                    # ↪️  Root redirect
│   ├── home.php                     # 🏠 Public landing page
│   ├── login.php                    # 🔑 Login page
│   ├── register.php                 # 📝 Registration page
│   ├── logout.php                   # 🚪 Session destroy
│   ├── dashboard.php                # 📊 Main dashboard
│   │
│   ├── 👥 leads/
│   │   ├── index.php                # 📋 All leads list
│   │   ├── create.php               # ➕ Add new lead
│   │   ├── edit.php                 # ✏️  Edit lead
│   │   ├── view.php                 # 👁️  Lead details
│   │   ├── delete.php               # 🗑️  Delete lead
│   │   └── update_status.php        # 🔄 Status updater
│   │
│   └── 📈 reports/
│       └── index.php                # 📉 Analytics & CSV export
│
├── 🎨 views/
│   └── layouts/
│       ├── main.php                 # 🖥️  Authenticated layout
│       └── auth.php                 # 🔒 Auth layout
│
├── 🖼️  assets/
│   ├── css/custom.css               # 💅 Custom styles
│   └── js/app.js                    # ⚡ Client-side scripts
│
├── 📚 docs/
│   └── architecture.md              # 🏛️  Architecture notes
│
├── .htaccess                        # 🛡️  Apache rewrite & security
├── .gitignore                       # 🙈 Ignored files
└── README.md                        # 📖 You are here
```

---

## 🗄️ Database Schema

```
admins          → id, name, username, email, password, created_at
leads           → id, admin_id, name, email, phone, company, source,
                  status, priority, notes, next_followup_date, created_at
follow_ups      → id, lead_id, admin_id, notes, followup_date, created_at
activity_logs   → id, admin_id, lead_id, action, created_at
```

---

## 🔧 Configuration

**`config/app.php`** — Application settings
```php
return [
    'name'     => 'Mini CRM',
    'base_url' => 'http://localhost/FUTURE_FS_02/public',
    'timezone' => 'UTC',
    'debug'    => true,
];
```

**`config/database.php`** — Database credentials *(not committed — copy from example)*
```php
return [
    'host'    => 'localhost',
    'dbname'  => 'crm_db',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
];
```

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Database connection failed | Ensure MySQL is running · check `config/database.php` credentials |
| 404 / Page not found | Ensure `.htaccess` exists · enable `mod_rewrite` in Apache |
| Charts not showing | Check browser console · ensure internet access for CDN |
| Login not working | Verify `schema.sql` was imported · check `admins` table |
| Blank white page | Enable PHP error display · check Apache error logs |

---

## �️ Roadmap

- [x] Lead CRUD with status & priority
- [x] Follow-up scheduling & overdue alerts
- [x] Dashboard with live charts
- [x] Reports & CSV export
- [x] Activity logging
- [ ] Email notifications for follow-ups
- [ ] Calendar view for scheduled follow-ups
- [ ] REST API endpoints
- [ ] Multi-user role management
- [ ] Bulk lead import via CSV

---

## 👨‍� Author

**Elias Araya**
Full Stack Web Development Intern · Future Interns · 2026

<a href="https://github.com/Elias-Araya">
  <img src="https://img.shields.io/badge/GitHub-Elias--Araya-181717?style=for-the-badge&logo=github" />
</a>

---

## 📄 License

This project is licensed under the **MIT License** — feel free to use, modify, and distribute.

---

<div align="center">
  <sub>Built with ❤️ using PHP · MySQL · Tailwind CSS</sub>
</div>
