SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `adminmsgs` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`userid` int(11) NOT NULL,
		`datetime` int(10) unsigned NOT NULL,
		`message` varchar(61000) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `hideresults` (
		`userid` int(11) NOT NULL,
		`hidden` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='$userid hides person $hidden from search results';

CREATE TABLE IF NOT EXISTS `users` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`username` varchar(60) NOT NULL,
		`password` char(65) NOT NULL,
		`fburl` varchar(200) NOT NULL,
		`isadmin` tinyint(4) NOT NULL DEFAULT '0',
		`name` varchar(75) NOT NULL DEFAULT '',
		`gender` tinyint(4) NOT NULL DEFAULT '0',
		`lookingfor` tinyint(4) NOT NULL DEFAULT '0',
		`longitude` double NOT NULL DEFAULT '0',
		`latitude` double NOT NULL DEFAULT '0',
		`interestarea` int(11) NOT NULL DEFAULT '0',
		`available` tinyint(4) NOT NULL DEFAULT '1',
		`freetext` varchar(15000) NOT NULL DEFAULT '',
		`yob` smallint(6) NOT NULL DEFAULT '0',
		`yobfrom` mediumint(9) NOT NULL,
		`yobto` mediumint(9) NOT NULL,
		`image` varchar(32) DEFAULT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `username` (`username`),
		UNIQUE KEY `fburl` (`fburl`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

