

CREATE TABLE IF NOT EXISTS `#__auth0_joomla_connect` (
  `joomla_userid` int(15) NOT NULL,
  `auth0_userid` varchar(30) NOT NULL,
  `joined_date` int(15) NOT NULL,
  `linked` smallint(4) NOT NULL,
  PRIMARY KEY (`joomla_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
