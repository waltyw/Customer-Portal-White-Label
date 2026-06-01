-- Per-customer invoice visibility toggle
ALTER TABLE users ADD COLUMN IF NOT EXISTS show_invoices TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;
