ALTER TABLE `users` 
ADD COLUMN `two_factor_code` VARCHAR(10) NULL DEFAULT NULL AFTER `session_token`,
ADD COLUMN `two_factor_expires_at` DATETIME NULL DEFAULT NULL AFTER `two_factor_code`;

CREATE TABLE IF NOT EXISTS `trusted_devices` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  KEY `fk_trusted_user_idx` (`user_id`),
  CONSTRAINT `fk_trusted_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
