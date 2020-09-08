ALTER TABLE `settings` ADD `pv_maker` INT(11) DEFAULT 0;
ALTER TABLE `settings` ADD `pv_maker_host` varchar(255) COLLATE utf8_bin DEFAULT NULL;


CREATE TABLE `allergens` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `ing` varchar(255) COLLATE utf8_bin NOT NULL,
 `name` varchar(255) COLLATE utf8_bin NOT NULL,
 `percentage` varchar(255) COLLATE utf8_bin NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;