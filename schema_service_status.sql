-- Service status board
CREATE TABLE IF NOT EXISTS `service_status` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_name` VARCHAR(100) NOT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('operational','degraded','outage','maintenance') NOT NULL DEFAULT 'operational',
  `message` TEXT,
  `updated_by` INT UNSIGNED,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `service_status` (`service_name`, `sort_order`, `status`) VALUES
('Web Hosting',     1, 'operational'),
('Email',           2, 'operational'),
('DNS',             3, 'operational'),
('Customer Portal', 4, 'operational'),
('Support',         5, 'operational');
