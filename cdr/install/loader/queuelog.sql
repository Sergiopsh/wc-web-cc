 
# DROP DATABASE asterisk;
# CREATE DATABASE asterisk;

#GRANT ALL
#  ON asterisk.*
#  TO asteriskuser@localhost
#  IDENTIFIED BY 'asterisk';

USE asterisk;

#DROP TABLE queuelog;
#DROP TABLE users;

CREATE TABLE queuelog (
  timestamp int(11),
  callid text,
  qname text, 
  agent text,
  action text,
  info1 text,
  info2 text,
  info3 text
); 

CREATE TABLE users (
  id int(10) unsigned NOT NULL auto_increment, 
  userid   VARCHAR( 40 ),
  password VARCHAR( 40 ), 
  level    VARCHAR( 2 ),
  state    VARCHAR( 1 ),
  PRIMARY KEY  (id),
  INDEX ( userid )
); 

insert into users (userid,password,level,state) values ('admin','admin','1','y');

