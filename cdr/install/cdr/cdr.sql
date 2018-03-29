
#DROP DATABASE asterisk;

CREATE DATABASE asterisk2;
 
GRANT ALL
  ON asterisk2.*
  TO asteriskuser@localhost
  IDENTIFIED BY 'asterisk';

USE asterisk;

CREATE TABLE cdr (
   id int(10) unsigned NOT NULL auto_increment,
   billed datetime NOT NULL default '0000-00-00 00:00:00',
   uniqueid varchar(32) NOT NULL default '',
   userfield varchar(255) NOT NULL default '',
   accountcode varchar(20) NOT NULL default '',
   src varchar(80) NOT NULL default '',
   dst varchar(80) NOT NULL default '',
   dcontext varchar(80) NOT NULL default '',
   clid varchar(80) NOT NULL default '',
   channel varchar(80) NOT NULL default '',
   dstchannel varchar(80) NOT NULL default '',
   lastapp varchar(80) NOT NULL default '',
   lastdata varchar(80) NOT NULL default '',
   calldate datetime NOT NULL default '0000-00-00 00:00:00',
   duration int(11) NOT NULL default '0',
   billsec int(11) NOT NULL default '0',
   disposition varchar(45) NOT NULL default '', 
   amaflags int(11) NOT NULL default '0',
   PRIMARY KEY (`id`)

); 

ALTER TABLE `cdr` ADD INDEX ( `calldate` );
ALTER TABLE `cdr` ADD INDEX ( `dst` );
ALTER TABLE `cdr` ADD INDEX ( `src` );
ALTER TABLE `cdr` ADD INDEX ( `accountcode` );

