CREATE TABLE `user_auth_secret` (
   `user_contentobject_id` int(11) NOT NULL UNIQUE,
   `google_authentication_secret` varchar(255) NOT NULL DEFAULT '',
   `totp_authentication_secret` varchar(255) NOT NULL DEFAULT '',
   `microsoft_authentication_secret` varchar(255) NOT NULL DEFAULT '',
   `backup_codes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
   PRIMARY KEY (`user_contentobject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
