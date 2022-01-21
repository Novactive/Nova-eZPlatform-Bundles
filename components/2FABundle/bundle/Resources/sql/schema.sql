CREATE TABLE `user_auth_secret` (
    `user_contentobject_id` int(11) NOT NULL UNIQUE,
    `google_authentication_secret` varchar(255) NOT NULL DEFAULT '',
    `totp_authentication_secret` varchar(255) NOT NULL DEFAULT '',
    `microsoft_authentication_secret` varchar(255) NOT NULL DEFAULT '',
    `backup_codes` varchar(255) NOT NULL DEFAULT '',
    `email_authentication` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `email_authentication_code` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`user_contentobject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
