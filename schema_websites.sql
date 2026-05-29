-- Multiple websites per customer
CREATE TABLE IF NOT EXISTS `customer_websites` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `label` VARCHAR(100) DEFAULT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add website reference to tickets
ALTER TABLE `tickets` ADD COLUMN `website_url` VARCHAR(500) DEFAULT NULL AFTER `category`;
