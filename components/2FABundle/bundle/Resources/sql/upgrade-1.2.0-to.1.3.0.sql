ALTER TABLE `user_auth_secret`
ADD COLUMN `backup_codes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL AFTER `microsoft_authentication_secret`;