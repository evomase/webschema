<?php
return [
    WEB_SCHEMA_TABLE_TYPES => 'CREATE TABLE IF NOT EXISTS ' . WEB_SCHEMA_TABLE_TYPES . ' (
        `id` VARCHAR( 50 ) NOT NULL PRIMARY KEY ,
        `comment` VARCHAR( 255 ) NULL,
        `label` VARCHAR( 50 ) NOT NULL,
        `url` VARCHAR( 255 ) NOT NULL,
        `parent` VARCHAR( 50 ),
        UNIQUE (  `id`, `parent` ) 
    ) ENGINE=INNODB;',

    WEB_SCHEMA_TABLE_PROPERTIES => 'CREATE TABLE IF NOT EXISTS ' . WEB_SCHEMA_TABLE_PROPERTIES . ' (
        `id` VARCHAR( 50 ) NOT NULL PRIMARY KEY ,
        `comment` VARCHAR( 255 ) NULL,
        `label` VARCHAR( 50 ) NOT NULL,
        `ranges` LONGTEXT NULL
    ) ENGINE=INNODB;',

    WEB_SCHEMA_TABLE_TYPE_PROPERTIES => 'CREATE TABLE IF NOT EXISTS ' . WEB_SCHEMA_TABLE_TYPE_PROPERTIES . ' (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `type_id` VARCHAR( 50 ) NOT NULL,
        `property_id` VARCHAR ( 50 ) NOT NULL,
        UNIQUE (  `type_id`, `property_id` ),
        FOREIGN KEY ( `type_id` ) REFERENCES ' . WEB_SCHEMA_TABLE_TYPES . ' (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY ( `property_id` ) REFERENCES ' . WEB_SCHEMA_TABLE_PROPERTIES . ' (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=INNODB;'
];