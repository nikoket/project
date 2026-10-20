-- Create Database
CREATE DATABASE IF NOT EXISTS `baps_elibrary` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `baps_elibrary`;

-- Table 1: users (Unified login accounts)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `role` ENUM('librarian', 'student') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: students (Student-specific details & Unique Barcode)
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `student_id` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `course` VARCHAR(100) NOT NULL,
    `barcode` VARCHAR(50) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 3: books (Empty by default for a fresh system)
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(200) NOT NULL,
    `isbn` VARCHAR(50) NOT NULL UNIQUE,
    `category` VARCHAR(100) NOT NULL,
    `total_copies` INT NOT NULL DEFAULT 1,
    `available_copies` INT NOT NULL DEFAULT 1,
    `cover_image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 4: borrow_requests (Borrow/Return Workflows)
CREATE TABLE IF NOT EXISTS `borrow_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `book_id` INT NOT NULL,
    `status` ENUM('Pending Borrow', 'Borrowed', 'Return Requested', 'Returned', 'Rejected') DEFAULT 'Pending Borrow',
    `request_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `approved_date` DATE DEFAULT NULL,
    `due_date` DATE DEFAULT NULL,
    `return_date` DATE DEFAULT NULL,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Default Librarian Account
-- Username: Admin
-- Password: Administrator (Stored as BCRYPT hash)
INSERT INTO `users` (`username`, `password`, `name`, `role`) 
VALUES ('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'librarian')
ON DUPLICATE KEY UPDATE `id`=`id`;
