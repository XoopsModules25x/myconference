## db ##
#Tablas: 
#    - conference_cvs
#    - conference_speech_type
#    - conference_speech
#    - conference_tracks
#    - conference_sponsors
#    - conference_speech_eval

CREATE TABLE myconference_cvs (
  cvid int(5) NOT NULL auto_increment,
  name varchar(80) NOT NULL default '' UNIQUE,
  email varchar(100) default '',
  descrip mediumtext default '',
  location varchar(100) default '',
  company varchar(100) default '',
  photo varchar(200),
  url varchar(200),
  hits int(5) default 0,
  PRIMARY KEY  (cvid, name)
) TYPE=MyISAM;

CREATE TABLE myconference_speeches (
  sid int(5) NOT NULL auto_increment,
  stid tinyint DEFAULT 1,
  title varchar(120) NOT NULL default '' UNIQUE,
  abstract mediumtext default '',
  stime int(10),
  etime int(10),
  duration int,
  cvid int(5) NOT NULL,
  cid tinyint,
  tid  tinyint,
  slides1 varchar(200),
  slides2 varchar(200),
  slides3 varchar(200),
  slides4 varchar(200),
  PRIMARY KEY  (sid, title)
) TYPE=MyISAM;

CREATE TABLE myconference_speech_types (
  stid tinyint NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  color varchar(7),
  plenary tinyint DEFAULT 0,
  PRIMARY KEY  (stid, name)
) TYPE=MyISAM;

INSERT INTO myconference_speech_types VALUES(1,'NORMAL','',0);
INSERT INTO myconference_speech_types VALUES(2,'KEYNOTE','',1);

CREATE TABLE myconference_tracks (
  tid tinyint NOT NULL auto_increment,
  cid tinyint NOT NULL,
  title varchar(200) NOT NULL,
  abstract mediumtext default '',
  PRIMARY KEY (tid)
) TYPE=MyISAM;

CREATE TABLE myconference_sections (
  sid tinyint NOT NULL auto_increment,
  cid tinyint NOT NULL,
  title varchar(200) NOT NULL,
  abstract mediumtext default '',
  PRIMARY KEY (sid)
) TYPE=MyISAM;

CREATE TABLE myconference_main (
  cid tinyint NOT NULL auto_increment,
  title varchar(200) NOT NULL,
  subtitle varchar(200) NOT NULL,
  subsubtitle varchar(200) NOT NULL,
  sdate DATE NOT NULL default '0',
  edate DATE NOT NULL default '0',
  abstract mediumtext default '',
  isdefault tinyint(1) default 0,
  PRIMARY KEY  (cid)
) TYPE=MyISAM;
