-- ============================================================
-- Mini CRM – Full Database Schema
-- Run in phpMyAdmin → SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS crm_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE crm_db;

-- ── 1. admins ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id         INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email    (email)
) ENGINE=InnoDB;

-- ── 2. lead_sources ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS lead_sources (
    id   INT         UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ── 3. leads ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS leads (
    id                 INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(100) NOT NULL,
    email              VARCHAR(100) NOT NULL,
    phone              VARCHAR(20)  DEFAULT NULL,
    company            VARCHAR(100) DEFAULT NULL,
    source             VARCHAR(50)  DEFAULT NULL,
    status             ENUM('New','Contacted','Follow-up','Converted','Closed') DEFAULT 'New',
    priority           ENUM('Low','Medium','High') DEFAULT 'Medium',
    notes              TEXT         DEFAULT NULL,
    next_followup_date DATE         DEFAULT NULL,
    assigned_to        INT UNSIGNED DEFAULT NULL,
    created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status   (status),
    INDEX idx_priority (priority),
    INDEX idx_email    (email),
    INDEX idx_created  (created_at)
) ENGINE=InnoDB;

-- ── 4. follow_ups ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS follow_ups (
    id            INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id       INT UNSIGNED NOT NULL,
    admin_id      INT UNSIGNED NOT NULL,
    note          TEXT         NOT NULL,
    followup_date DATE         DEFAULT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id)  REFERENCES leads(id)  ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_lead_id (lead_id)
) ENGINE=InnoDB;

-- ── 5. activity_logs ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_logs (
    id         INT          UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id   INT UNSIGNED NOT NULL,
    lead_id    INT UNSIGNED DEFAULT NULL,
    action     VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    FOREIGN KEY (lead_id)  REFERENCES leads(id)  ON DELETE SET NULL,
    INDEX idx_admin_id (admin_id),
    INDEX idx_created  (created_at)
) ENGINE=InnoDB;

-- ── Sample Data ────────────────────────────────────────────

-- Default admin: username=admin, password=Admin@1234
INSERT INTO admins (full_name, username, email, password) VALUES
('Super Admin', 'admin', 'admin@minicrm.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Lead sources
INSERT INTO lead_sources (name) VALUES
('Website'),('Google'),('Facebook'),('LinkedIn'),('Instagram'),('Referral'),('Cold Call'),('Other');

-- Sample leads
INSERT INTO leads (name, email, phone, company, source, status, priority, notes, next_followup_date) VALUES
('Alice Johnson',  'alice@example.com',  '+1-555-0101', 'TechCorp',    'Google',   'New',       'High',   'Interested in enterprise plan',        DATE_ADD(CURDATE(), INTERVAL 2 DAY)),
('Bob Martinez',   'bob@example.com',    '+1-555-0102', 'StartupXYZ',  'LinkedIn', 'Contacted', 'Medium', 'Sent intro email, awaiting reply',     DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
('Carol White',    'carol@example.com',  '+1-555-0103', 'DesignHub',   'Referral', 'Follow-up', 'High',   'Had a call, needs proposal',           CURDATE()),
('David Lee',      'david@example.com',  '+1-555-0104', 'MediaGroup',  'Facebook', 'Converted', 'Low',    'Signed contract on 2026-01-15',        NULL),
('Emma Davis',     'emma@example.com',   '+1-555-0105', 'RetailPlus',  'Website',  'New',       'Medium', 'Filled contact form',                  DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
('Frank Wilson',   'frank@example.com',  '+1-555-0106', 'BuildCo',     'Google',   'Closed',    'Low',    'Not interested at this time',          NULL),
('Grace Kim',      'grace@example.com',  '+1-555-0107', 'EduTech',     'Instagram','Contacted', 'High',   'Very interested, schedule demo',       DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
('Henry Brown',    'henry@example.com',  '+1-555-0108', 'FinanceApp',  'Referral', 'Follow-up', 'Medium', 'Waiting for budget approval',          DATE_ADD(CURDATE(), INTERVAL 7 DAY));
