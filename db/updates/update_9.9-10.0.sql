CREATE TABLE `inventory_compounds` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `description` TEXT NOT NULL , `batch_id` VARCHAR(255) NOT NULL DEFAULT '-' , `size` DOUBLE NOT NULL DEFAULT '0' , `updated` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `owner_id` INT NOT NULL DEFAULT '0' , `location` VARCHAR(255) NOT NULL , `label_info` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci; 
ALTER TABLE `documents` ADD `isBatch` INT NOT NULL DEFAULT '0' AFTER `docData`; 
