CREATE TABLE fe_users (
    `telephone` text,
    `email` text,

    `lat` varchar(25) default NULL,
    `lng` varchar(25) default NULL

    `categories` int(11) unsigned NOT NULL default '0',
);
