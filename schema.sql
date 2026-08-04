-- ====================================================================
-- HOTEL ROOM BOOKING SYSTEM - DATABASE SCHEMA & DEMO DATA SCRIPT
-- Engine: Fully Compatible with MariaDB 10+, MySQL 5.7+, MySQL 8.0+
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `hotel_booking_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hotel_booking_db`;

-- Disable foreign key checks during table drop & creation
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `room_types`;
DROP TABLE IF EXISTS `hotels`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------------------
-- 1. USERS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Vietnam',
  `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. HOTELS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `hotels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `star_rating` DECIMAL(2,1) DEFAULT 4.5,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_hotels_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. ROOM TYPES TABLE
-- --------------------------------------------------------------------
CREATE TABLE `room_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT NOT NULL,
  `type_name` VARCHAR(100) NOT NULL,
  `base_price_per_night` DECIMAL(10,2) NOT NULL,
  `max_occupancy` INT NOT NULL DEFAULT 2,
  `description` TEXT,
  `amenities` TEXT DEFAULT NULL,
  INDEX `idx_room_types_hotel` (`hotel_id`),
  CONSTRAINT `fk_room_types_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. ROOMS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_type_id` INT NOT NULL,
  `room_number` VARCHAR(20) NOT NULL,
  `floor_number` INT NOT NULL DEFAULT 1,
  `status` ENUM('available', 'maintenance', 'occupied') NOT NULL DEFAULT 'available',
  INDEX `idx_rooms_status` (`status`),
  INDEX `idx_rooms_type` (`room_type_id`),
  CONSTRAINT `fk_rooms_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. BOOKINGS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(20) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `check_in_date` DATE NOT NULL,
  `check_out_date` DATE NOT NULL,
  `total_guests` INT NOT NULL DEFAULT 1,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_bookings_dates` (`check_in_date`, `check_out_date`),
  INDEX `idx_bookings_user` (`user_id`),
  INDEX `idx_bookings_room` (`room_id`),
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. PAYMENTS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `payment_method` ENUM('credit_card', 'e_wallet', 'bank_transfer') NOT NULL,
  `transaction_reference` VARCHAR(50) NOT NULL UNIQUE,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `payment_status` ENUM('pending', 'success', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `paid_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_payments_booking` (`booking_id`),
  CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. REVIEWS TABLE
-- --------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `hotel_id` INT NOT NULL,
  `rating` INT NOT NULL,
  `comment` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_reviews_user` (`user_id`),
  INDEX `idx_reviews_hotel` (`hotel_id`),
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- SAMPLE SEED DATA FOR DEMO PURPOSES
-- ====================================================================

-- 1. Insert Users (Passwords are bcrypt hashed for "password123")
INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `city`, `country`, `role`) VALUES (1, 'Admin System Manager', 'admin@grandvista.com', '$2y$10$wE99q49VlH1d9sJ7F2oR/e8mQ8bVp/X3P5wR5y8z9wE99q49VlH1d', '+84 901 234 567', 'Ha Noi', 'Vietnam', 'admin');
INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `city`, `country`, `role`) VALUES (2, 'Nguyen Van Anh', 'vananh@gmail.com', '$2y$10$wE99q49VlH1d9sJ7F2oR/e8mQ8bVp/X3P5wR5y8z9wE99q49VlH1d', '+84 912 345 678', 'Da Nang', 'Vietnam', 'customer');
INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `city`, `country`, `role`) VALUES (3, 'Elena Rostova', 'elena.r@luxurytravel.com', '$2y$10$wE99q49VlH1d9sJ7F2oR/e8mQ8bVp/X3P5wR5y8z9wE99q49VlH1d', '+44 7700 900077', 'London', 'United Kingdom', 'customer');
INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `phone`, `city`, `country`, `role`) VALUES (4, 'Tran Minh Duc', 'minhduc@techcorp.vn', '$2y$10$wE99q49VlH1d9sJ7F2oR/e8mQ8bVp/X3P5wR5y8z9wE99q49VlH1d', '+84 988 776 554', 'Ho Chi Minh City', 'Vietnam', 'customer');

-- 2. Insert Hotels
INSERT INTO `hotels` (`id`, `name`, `city`, `address`, `star_rating`, `image_url`, `description`) VALUES (1, 'Grand Vista Ha Noi', 'Ha Noi', '146 Giang Vo, Ba Dinh District, Ha Noi', 5.0, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80', 'Luxury 5-star hotel in the heart of Hanoi featuring panoramic city views, infinity pool, and world-class spa.');
INSERT INTO `hotels` (`id`, `name`, `city`, `address`, `star_rating`, `image_url`, `description`) VALUES (2, 'Grand Vista Ocean Resort Da Nang', 'Da Nang', '88 Vo Nguyen Giap, Ngu Hanh Son, Da Nang', 4.8, 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80', 'Beachfront resort overlooking My Khe beach with private beach access and seafood gourmet dining.');
INSERT INTO `hotels` (`id`, `name`, `city`, `address`, `star_rating`, `image_url`, `description`) VALUES (3, 'Grand Vista Saigon Landmark', 'Ho Chi Minh City', '720A Dien Bien Phu, Ward 22, Binh Thanh District, HCMC', 4.9, 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80', 'Iconic skyscraper hotel with executive suites, sky lounge, and state-of-the-art conference facilities.');

-- 3. Insert Room Types
INSERT INTO `room_types` (`id`, `hotel_id`, `type_name`, `base_price_per_night`, `max_occupancy`, `description`, `amenities`) VALUES (1, 1, 'Deluxe City View', 120.00, 2, 'Spacious 35sqm room featuring king bed, marble bathroom, and panoramic Hanoi city view.', 'WiFi, Smart TV, Mini Bar, Air Conditioning, King Bed');
INSERT INTO `room_types` (`id`, `hotel_id`, `type_name`, `base_price_per_night`, `max_occupancy`, `description`, `amenities`) VALUES (2, 1, 'Presidential Suite', 450.00, 4, 'Opulent 90sqm suite with private lounge, jacuzzi, and dedicated butler service.', 'WiFi, Jacuzzi, Butler Service, Free Breakfast, Balcony');
INSERT INTO `room_types` (`id`, `hotel_id`, `type_name`, `base_price_per_night`, `max_occupancy`, `description`, `amenities`) VALUES (3, 2, 'Ocean Front Villa', 280.00, 3, 'Private beachside villa with direct ocean access and private plunge pool.', 'Private Pool, Ocean View, WiFi, Spa Access, Mini Bar');
INSERT INTO `room_types` (`id`, `hotel_id`, `type_name`, `base_price_per_night`, `max_occupancy`, `description`, `amenities`) VALUES (4, 3, 'Executive Sky Suite', 210.00, 2, 'High-floor suite with Saigon river view and access to the VIP Executive Lounge.', 'Executive Lounge Access, High Speed WiFi, Workplace, Breakfast');

-- 4. Insert Physical Rooms
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (1, 1, '101', 1, 'available');
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (2, 1, '102', 1, 'available');
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (3, 2, '501', 5, 'available');
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (4, 3, 'V-01', 1, 'available');
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (5, 3, 'V-02', 1, 'available');
INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor_number`, `status`) VALUES (6, 4, '2204', 22, 'available');

-- 5. Insert Bookings
INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_guests`, `total_amount`, `status`, `created_at`) VALUES (1, 'GVB-2026-8801', 2, 1, '2026-08-10', '2026-08-13', 2, 360.00, 'confirmed', '2026-08-01 10:30:00');
INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_guests`, `total_amount`, `status`, `created_at`) VALUES (2, 'GVB-2026-8802', 3, 3, '2026-08-15', '2026-08-18', 2, 1350.00, 'confirmed', '2026-08-02 14:15:00');
INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_guests`, `total_amount`, `status`, `created_at`) VALUES (3, 'GVB-2026-8803', 4, 4, '2026-08-20', '2026-08-22', 2, 560.00, 'completed', '2026-07-25 09:00:00');

-- 6. Insert Payments
INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `transaction_reference`, `amount_paid`, `payment_status`, `paid_at`) VALUES (1, 1, 'credit_card', 'PAY-VISA-99281', 360.00, 'success', '2026-08-01 10:32:00');
INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `transaction_reference`, `amount_paid`, `payment_status`, `paid_at`) VALUES (2, 2, 'e_wallet', 'PAY-MOMO-88210', 1350.00, 'success', '2026-08-02 14:16:00');
INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `transaction_reference`, `amount_paid`, `payment_status`, `paid_at`) VALUES (3, 3, 'bank_transfer', 'PAY-SEPA-33109', 560.00, 'success', '2026-07-25 09:05:00');

-- 7. Insert Reviews
INSERT INTO `reviews` (`id`, `user_id`, `hotel_id`, `rating`, `comment`, `created_at`) VALUES (1, 2, 1, 5, 'Exceptional hospitality and wonderful Hanoi skyline view from the room!', '2026-08-02 08:00:00');
INSERT INTO `reviews` (`id`, `user_id`, `hotel_id`, `rating`, `comment`, `created_at`) VALUES (2, 3, 2, 5, 'The ocean villa was breathtaking! Direct access to the beach and top-notch service.', '2026-07-28 11:45:00');

-- Re-enable foreign key checks at the end of script execution
SET FOREIGN_KEY_CHECKS = 1;
