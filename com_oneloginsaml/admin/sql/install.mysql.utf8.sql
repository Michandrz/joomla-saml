/** 
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */

DROP TABLE IF EXISTS `#__oneloginsaml_config`;
DROP TABLE IF EXISTS `#__oneloginsaml_attrmap`;
DROP TABLE IF EXISTS `#__oneloginsaml_groupmap`;

CREATE TABLE `#__oneloginsaml_config` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `param` varchar(25)	NOT NULL,
    `value` text	DEFAULT '',
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `#__oneloginsaml_attrmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` text	NOT NULL,
    `idp`   text	NOT NULL,
)
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `#__oneloginsaml_groupmap` (
    `id`    int(11)	NOT NULL AUTO_INCREMENT,
    `local` int(11)	NOT NULL,
    `idp`   text	NOT NULL
)
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
DEFAULT COLLATE=utf8mb4_unicode_ci;
