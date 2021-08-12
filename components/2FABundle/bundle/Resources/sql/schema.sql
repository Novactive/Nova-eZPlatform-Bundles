CREATE TABLE `user_google_auth_secret` (
   `user_contentobject_id` int(11) NOT NULL UNIQUE,
   `google_authentication_secret` varchar(255) NOT NULL,
   PRIMARY KEY (`user_contentobject_id`)
) ENGINE=InnoDB;
