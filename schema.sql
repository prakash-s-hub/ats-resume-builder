-- Create Database
CREATE DATABASE IF NOT EXISTS `career_craft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `career_craft`;

-- 1. Resumes Table (Stores main resume metadata, template type, styling, and timestamps)
CREATE TABLE IF NOT EXISTS `resumes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `template` VARCHAR(50) NOT NULL DEFAULT 'harvard', -- harvard, mckinsey, google, stanford
  `font` VARCHAR(50) NOT NULL DEFAULT 'inter',         -- inter, lato, merriweather
  `color` VARCHAR(20) NOT NULL DEFAULT '#2563eb',       -- Accent HEX color
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Personal Info Table (Contact details, title, and professional summary)
CREATE TABLE IF NOT EXISTS `personal_info` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resume_id` INT NOT NULL,
  `full_name` VARCHAR(150) DEFAULT NULL,
  `job_title` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `summary` TEXT DEFAULT NULL,
  FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Experience Table (Work history entries)
CREATE TABLE IF NOT EXISTS `experience` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resume_id` INT NOT NULL,
  `company` VARCHAR(150) NOT NULL,
  `position` VARCHAR(150) NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `start_date` VARCHAR(20) DEFAULT NULL,
  `end_date` VARCHAR(20) DEFAULT NULL,
  `current_job` TINYINT(1) DEFAULT 0,
  `description` TEXT DEFAULT NULL,
  FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Education Table (Academic background)
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resume_id` INT NOT NULL,
  `school` VARCHAR(150) NOT NULL,
  `degree` VARCHAR(150) NOT NULL,
  `field` VARCHAR(100) DEFAULT NULL,
  `start_date` VARCHAR(20) DEFAULT NULL,
  `end_date` VARCHAR(20) DEFAULT NULL,
  `gpa` VARCHAR(20) DEFAULT NULL,
  FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Skills Table (Technical and soft skills with proficiency levels)
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resume_id` INT NOT NULL,
  `skill_name` VARCHAR(100) NOT NULL,
  `level` VARCHAR(50) DEFAULT 'Intermediate', -- Beginner, Intermediate, Advanced, Expert
  `font` VARCHAR(50) DEFAULT 'inter',
  FOREIGN KEY (`resume_id`) REFERENCES `resumes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- SAMPLE SEED DATA (Optional: To test out of the box)
-- =====================================================

-- Insert a sample resume
INSERT INTO `resumes` (`id`, `title`, `template`, `font`, `color`) VALUES 
(1, 'Senior Software Engineer Resume', 'harvard', 'inter', '#2563eb');

-- Insert personal info for resume ID 1
INSERT INTO `personal_info` (`resume_id`, `full_name`, `job_title`, `email`, `phone`, `location`, `linkedin`, `summary`) VALUES 
(1, 'Alex Morgan', 'Senior Full Stack Developer', 'alex.morgan@example.com', '+1 (555) 234-5678', 'San Francisco, CA', 'linkedin.com/in/alexmorgan', 'Results-driven Software Engineer with 8+ years of experience designing scalable web applications, optimizing microservices, and leading high-performing engineering teams.');

-- Insert sample work experience
INSERT INTO `experience` (`resume_id`, `company`, `position`, `location`, `start_date`, `end_date`, `current_job`, `description`) VALUES 
(1, 'TechCorp Solutions', 'Senior Backend Engineer', 'San Francisco, CA', '2021-03', '', 1, '• Architected and scaled high-throughput REST APIs using PHP and Node.js.\n• Reduced database query latency by 45% through strategic indexing and caching.\n• Mentored 5 junior developers and enforced rigorous code review standards.');

-- Insert sample education
INSERT INTO `education` (`resume_id`, `school`, `degree`, `field`, `start_date`, `end_date`, `gpa`) VALUES 
(1, 'University of California, Berkeley', 'B.S.', 'Computer Science', '2015-08', '2019-05', '3.8');

-- Insert sample skills
INSERT INTO `skills` (`resume_id`, `skill_name`, `level`) VALUES 
(1, 'PHP / Laravel', 'Expert'),
(1, 'JavaScript / React', 'Advanced'),
(1, 'MySQL / PostgreSQL', 'Advanced'),
(1, 'Docker & Kubernetes', 'Intermediate');
