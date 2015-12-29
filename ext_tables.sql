CREATE TABLE fe_users (
    `telephone` varchar(225) NOT NULL default '',
    `email` varchar(225) NOT NULL default '',

    `lat` varchar(25) default NULL,
    `lng` varchar(25) default NULL

    `categories` int(11) unsigned NOT NULL default '0',
);
