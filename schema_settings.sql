-- Run this in phpMyAdmin to add the settings table
-- (separate from the main schema.sql as it can be added to existing installs)

CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
('app_name',        'Beebizzi Customer Portal'),
('primary_color',   '#2563eb'),
('primary_dark',    '#1d4ed8'),
('sidebar_bg',      '#0f172a'),
('sidebar_text',    '#94a3b8'),
('sidebar_active',  '#2563eb'),
('support_email',   'support@beebizzi.co.uk');
