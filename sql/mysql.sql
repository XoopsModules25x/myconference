## db ##
#Tablas: 
#    - conference_speakers
#    - conference_speech_type
#    - conference_speech
#    - conference_tracks
#    - conference_sponsors
#    - conference_speech_eval

CREATE TABLE myconference_speakers (
  speakerid INT(5)      NOT NULL AUTO_INCREMENT,
  name      VARCHAR(80) NOT NULL DEFAULT '' UNIQUE,
  email     VARCHAR(100)         DEFAULT '',
  descrip   MEDIUMTEXT,
  location  VARCHAR(100),
  company   VARCHAR(100)         DEFAULT '',
  photo     VARCHAR(200),
  url       VARCHAR(200),
  hits      INT(5)               DEFAULT 0,
  PRIMARY KEY (speakerid, name)
)
  ENGINE = MyISAM;

CREATE TABLE myconference_speeches (
  sid       INT(5)       NOT NULL AUTO_INCREMENT,
  stid      TINYINT               DEFAULT 1,
  title     VARCHAR(120) NOT NULL DEFAULT '' UNIQUE,
  summary   MEDIUMTEXT,
  stime     INT(10),
  etime     INT(10),
  duration  INT,
  speakerid INT(5)       NOT NULL,
  cid       TINYINT,
  tid       TINYINT,
  slides1   VARCHAR(200),
  slides2   VARCHAR(200),
  slides3   VARCHAR(200),
  slides4   VARCHAR(200),
  PRIMARY KEY (sid, title)
)
  ENGINE = MyISAM;

CREATE TABLE myconference_speech_types (
  stid    TINYINT     NOT NULL AUTO_INCREMENT,
  name    VARCHAR(50) NOT NULL,
  color   VARCHAR(7),
  plenary TINYINT              DEFAULT 0,
  PRIMARY KEY (stid, name)
)
  ENGINE = MyISAM;

INSERT INTO myconference_speech_types VALUES (1, 'NORMAL', '', 0);
INSERT INTO myconference_speech_types VALUES (2, 'KEYNOTE', '', 1);

CREATE TABLE myconference_tracks (
  tid     TINYINT      NOT NULL AUTO_INCREMENT,
  cid     TINYINT      NOT NULL,
  title   VARCHAR(200) NOT NULL,
  summary MEDIUMTEXT,
  PRIMARY KEY (tid)
)
  ENGINE = MyISAM;

CREATE TABLE myconference_sections (
  sid     TINYINT      NOT NULL AUTO_INCREMENT,
  cid     TINYINT      NOT NULL,
  title   VARCHAR(200) NOT NULL,
  summary MEDIUMTEXT,
  PRIMARY KEY (sid)
)
  ENGINE = MyISAM;

CREATE TABLE myconference_main (
  cid         TINYINT      NOT NULL AUTO_INCREMENT,
  title       VARCHAR(200) NOT NULL,
  subtitle    VARCHAR(200) NOT NULL,
  subsubtitle VARCHAR(200) NOT NULL,
  sdate       VARCHAR(10),
  edate       VARCHAR(10),
  summary     MEDIUMTEXT,
  isdefault   TINYINT(1)            DEFAULT 0,
  PRIMARY KEY (cid)
)
  ENGINE = MyISAM;
