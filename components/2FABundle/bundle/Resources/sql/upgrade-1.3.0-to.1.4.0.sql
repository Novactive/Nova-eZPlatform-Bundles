ALTER TABLE `user_auth_secret`
MODIFY COLUMN `backup_codes` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' AFTER `microsoft_authentication_secret`;

ALTER TABLE `user_auth_secret`
ADD COLUMN `email_authentication` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `backup_codes`;

ALTER TABLE `user_auth_secret`
ADD COLUMN `email_authentication_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' AFTER `email_authentication`;