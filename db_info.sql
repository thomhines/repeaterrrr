--
-- Table structure for repeaterrrr
--

CREATE TABLE IF NOT EXISTS `sets` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`slug` tinytext,
	`json` text,
	`use_counter` int(11) DEFAULT '0',
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

