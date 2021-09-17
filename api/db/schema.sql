CREATE TABLE `contacts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstName` VARCHAR(255) NOT NULL,
    `lastName` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `contribution` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE `email` (`email`(255))
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci; 

CREATE TABLE `newsletter` (
    `email` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`email`)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci; 
