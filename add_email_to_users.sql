ALTER TABLE `users` ADD COLUMN `email` VARCHAR(255) NOT NULL AFTER `username`;
-- Optional: Update existing admin to have a default email
UPDATE `users` SET `email` = 'admin@admin.com' WHERE `username` = 'admin';
