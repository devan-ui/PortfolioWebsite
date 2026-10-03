-- Devanandan CS Portfolio Website - Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.4+ / MySQL 8.0+

CREATE DATABASE IF NOT EXISTS `devanandan_portfolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `devanandan_portfolio`;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(60) NOT NULL UNIQUE,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Personal Profile Table
CREATE TABLE IF NOT EXISTS `profile` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL DEFAULT 'Devanandan CS',
    `displayed_name` VARCHAR(100) NOT NULL DEFAULT 'Devanandan CS',
    `professional_title` VARCHAR(150) NOT NULL DEFAULT 'MCA Student, Full Stack Developer',
    `tagline` VARCHAR(255) NOT NULL,
    `bio` TEXT NOT NULL,
    `location` VARCHAR(150) NOT NULL DEFAULT 'Ernakulam, Kerala, India',
    `email` VARCHAR(120) NOT NULL DEFAULT 'devanandancs@example.com',
    `phone` VARCHAR(30) DEFAULT NULL,
    `career_goal` VARCHAR(255) NOT NULL DEFAULT 'Become a web developer',
    `availability_status` VARCHAR(100) NOT NULL DEFAULT 'Available for opportunities',
    `profile_photo` VARCHAR(255) DEFAULT NULL,
    `resume_file` VARCHAR(255) DEFAULT NULL,
    `github_url` VARCHAR(255) DEFAULT 'https://github.com',
    `linkedin_url` VARCHAR(255) DEFAULT 'https://linkedin.com',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Skills Table
CREATE TABLE IF NOT EXISTS `skills` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `category` ENUM('Programming', 'Frontend', 'Backend', 'Databases', 'Tools', 'AI/ML') NOT NULL,
    `proficiency` ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL DEFAULT 'Beginner',
    `icon` VARCHAR(100) DEFAULT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_skills_category` (`category`),
    INDEX `idx_skills_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Projects Table
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(160) NOT NULL UNIQUE,
    `category` VARCHAR(100) NOT NULL DEFAULT 'Web Application',
    `role` VARCHAR(100) NOT NULL DEFAULT 'Full Stack Developer',
    `status` VARCHAR(50) NOT NULL DEFAULT 'In Development',
    `summary` VARCHAR(300) NOT NULL,
    `description` TEXT NOT NULL,
    `technologies` VARCHAR(255) NOT NULL,
    `github_url` VARCHAR(255) DEFAULT NULL,
    `live_url` VARCHAR(255) DEFAULT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `display_order` INT NOT NULL DEFAULT 0,
    `seo_title` VARCHAR(200) DEFAULT NULL,
    `seo_description` VARCHAR(300) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_projects_slug` (`slug`),
    INDEX `idx_projects_featured` (`featured`),
    INDEX `idx_projects_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Project Images / Screenshots Table
CREATE TABLE IF NOT EXISTS `project_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(200) DEFAULT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_project_images_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_project_images_featured` (`is_featured`),
    INDEX `idx_project_images_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Education Table
CREATE TABLE IF NOT EXISTS `education` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `degree` VARCHAR(120) NOT NULL,
    `field_of_study` VARCHAR(150) NOT NULL,
    `institution` VARCHAR(200) NOT NULL,
    `location` VARCHAR(150) DEFAULT 'Kerala, India',
    `start_year` VARCHAR(20) NOT NULL,
    `end_year` VARCHAR(20) NOT NULL,
    `is_current` TINYINT(1) NOT NULL DEFAULT 0,
    `grade` VARCHAR(50) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_education_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Experience / Internships Table
CREATE TABLE IF NOT EXISTS `experience` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(120) NOT NULL,
    `company` VARCHAR(150) NOT NULL,
    `location` VARCHAR(150) DEFAULT 'Ernakulam, Kerala, India',
    `start_date` VARCHAR(30) NOT NULL,
    `end_date` VARCHAR(30) DEFAULT 'Present',
    `is_current` TINYINT(1) NOT NULL DEFAULT 0,
    `description` TEXT NOT NULL,
    `responsibilities` TEXT DEFAULT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_experience_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Certifications Table
CREATE TABLE IF NOT EXISTS `certifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `issuer` VARCHAR(150) NOT NULL,
    `issue_date` VARCHAR(30) NOT NULL,
    `expiration_date` VARCHAR(30) DEFAULT 'No Expiration',
    `credential_id` VARCHAR(100) DEFAULT NULL,
    `credential_url` VARCHAR(255) DEFAULT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_certifications_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Achievements Table
CREATE TABLE IF NOT EXISTS `achievements` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(80) NOT NULL DEFAULT 'Academic',
    `achievement_date` VARCHAR(30) NOT NULL,
    `description` TEXT NOT NULL,
    `highlight_badge` VARCHAR(80) DEFAULT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_achievements_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Services Table
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `short_description` VARCHAR(255) NOT NULL,
    `full_description` TEXT NOT NULL,
    `deliverables` TEXT DEFAULT NULL,
    `icon` VARCHAR(60) NOT NULL DEFAULT 'code',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `display_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_services_active` (`is_active`),
    INDEX `idx_services_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Social Links Table
CREATE TABLE IF NOT EXISTS `social_links` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `platform` VARCHAR(60) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(60) NOT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_social_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Contact Messages Table
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_messages_is_read` (`is_read`),
    INDEX `idx_messages_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(80) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`setting_key`),
    INDEX `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. SEO Metadata Per Page
CREATE TABLE IF NOT EXISTS `seo_pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_identifier` VARCHAR(60) NOT NULL UNIQUE,
    `meta_title` VARCHAR(200) NOT NULL,
    `meta_description` VARCHAR(300) NOT NULL,
    `meta_keywords` VARCHAR(255) DEFAULT NULL,
    `canonical_url` VARCHAR(255) DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_seo_page_id` (`page_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
