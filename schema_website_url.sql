-- Add website URL to customer accounts
ALTER TABLE users ADD COLUMN website_url VARCHAR(500) DEFAULT NULL AFTER phone;
