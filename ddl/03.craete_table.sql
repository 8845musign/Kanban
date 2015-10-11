CREATE TABLE `t_account` (
  `t_account_id` INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(255),
  `password` VARCHAR(255),
  `mail` VARCHAR(255),
  `created_at` DATETIME,
  `updated_at` DATETIME
) ENGINE=InnoDB;

CREATE TABLE `t_task` (
  `t_task_id` INT(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `t_account_id` INT(255) COMMENT "created by",
  `title` VARCHAR(255),
  `description` VARCHAR(4096),
  `created_at` DATETIME,
  `updated_at` DATETIME,
  FOREIGN KEY (`t_account_id`) REFERENCES `t_account` (`t_account_id`)
) ENGINE=InnoDB;
