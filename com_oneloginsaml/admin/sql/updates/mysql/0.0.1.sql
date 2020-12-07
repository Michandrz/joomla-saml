/** 
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */


CREATE TABLE IF NOT EXISTS `#__oneloginsaml_attrmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` text	NOT NULL,
    `idp`   text	NOT NULL,
    `match` bool        DEFAULT 0,
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__oneloginsaml_groupmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` int(11)	NOT NULL,
    `idp`   text	NOT NULL,
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE VIEW IF NOT EXISTS `#__oneloginsaml_groupmapview` AS
    SELECT 
	`#__oneloginsaml_groupmap`.`id` as `id`,
	`#__usergroups`.`title` as `localName`,
	`#__oneloginsaml_groupmap`.`local` as `local`, 
	`#__oneloginsaml_groupmap`.`idp` as `idp`
    FROM `#__oneloginsaml_groupmap`
    JOIN `#__usergroups` ON  `#__oneloginsaml_groupmap`.`local` = `#__usergroups`.`id`;


CREATE VIEW IF NOT EXISTS `lyqp8_oneloginsaml_groupmapview` AS
    SELECT 
	`lyqp8_oneloginsaml_groupmap`.`id` as `id`,
	`lyqp8_usergroups`.`title` as `localName`,
	`lyqp8_oneloginsaml_groupmap`.`local` as `local`, 
	`lyqp8_oneloginsaml_groupmap`.`idp` as `idp`
    FROM `lyqp8_oneloginsaml_groupmap`
    JOIN `lyqp8_usergroups` ON  `lyqp8_oneloginsaml_groupmap`.`local` = `lyqp8_usergroups`.`id`;

INSERT INTO `#__oneloginsaml_attrmap` (`local`,`idp`,`match`) VALUES ('Name','',0) ('Username','',0) ('Email','',1) ('Groups','',0);