/** 
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */

DROP TABLE IF EXISTS `#__oneloginsaml_attrmap`;
DROP TABLE IF EXISTS `#__oneloginsaml_groupmap`;
DROP VIEW IF EXISTS `#__oneloginsaml_groupmapview`;


CREATE TABLE `#__oneloginsaml_attrmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` text	NOT NULL,
    `idp`   text	NOT NULL,
    `match` bool        DEFAULT 0,
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `#__oneloginsaml_groupmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` int(11)	NOT NULL,
    `idp`   text	NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE VIEW `#__oneloginsaml_groupmapview` AS
    SELECT 
	`#__oneloginsaml_groupmap`.`id` as `id`,
	`#__usergroups`.`title` as `localName`,
	`#__oneloginsaml_groupmap`.`local` as `local`, 
	`#__oneloginsaml_groupmap`.`idp` as `idp`
    FROM `#__oneloginsaml_groupmap`
    JOIN `#__usergroups` ON  `#__oneloginsaml_groupmap`.`local` = `#__usergroups`.`id`;