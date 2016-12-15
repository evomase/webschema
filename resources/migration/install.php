<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 15/12/2016
 * Time: 16:51
 */

return [
    WEB_SCHEMA_TABLE_TYPES => 'CREATE TABLE IF NOT EXISTS ' . WEB_SCHEMA_TABLE_TYPES . ' (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `comment` VARCHAR( 255 ) NULL,
        `name` VARCHAR( 50 ) NOT NULL,
        `label` VARCHAR( 50 ) NOT NULL,
        `url` VARCHAR( 255 ) NOT NULL,
        `parent` INT NOT NULL DEFAULT 0,
        UNIQUE (  `name`, `parent` ) 
    ) ENGINE=INNODB;',

    WEB_SCHEMA_TABLE_PROPERTIES => 'CREATE TABLE IF NOT EXISTS ' . WEB_SCHEMA_TABLE_PROPERTIES . ' (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `comment` VARCHAR( 255 ) NULL,
        `name` VARCHAR( 50 ) NOT NULL,
        `label` VARCHAR( 50 ) NOT NULL,
        `ranges` LONGTEXT NULL,
        UNIQUE (  `name` ) 
    ) ENGINE=INNODB;',

    WEB_SCHEMA_TABLE_TYPE_PROPERTIES => 'CREATE TABLE ' . WEB_SCHEMA_TABLE_TYPE_PROPERTIES . ' (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `type_id` INT NOT NULL,
        `property_id` INT NOT NULL,
        UNIQUE (  `type_id`, `property_id` ),
        FOREIGN KEY ( `type_id` ) REFERENCES ' . WEB_SCHEMA_TABLE_TYPES . ' (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY ( `property_id` ) REFERENCES ' . WEB_SCHEMA_TABLE_PROPERTIES . ' (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=INNODB;'
];