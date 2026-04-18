-- ============================================================
-- RUN THIS SQL IN phpMyAdmin or MySQL to fix payment issues
-- This adds the payment_method column to existing database
-- ============================================================

USE bluebirdhotel;

-- Add payment_method to roombook table (stores method at booking time)
ALTER TABLE `roombook` 
  ADD COLUMN IF NOT EXISTS `payment_method` varchar(50) NOT NULL DEFAULT 'Cash';

-- Add payment_method to payment table (shows on invoice/payment list)
ALTER TABLE `payment` 
  ADD COLUMN IF NOT EXISTS `payment_method` varchar(50) NOT NULL DEFAULT 'Cash';

-- Update any existing rows that have empty payment_method
UPDATE `roombook` SET `payment_method` = 'Cash' WHERE `payment_method` = '';
UPDATE `payment` SET `payment_method` = 'Cash' WHERE `payment_method` = '';

SELECT 'Database updated successfully!' AS Status;
