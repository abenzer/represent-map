CREATE TABLE IF NOT EXISTS `events` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `id_eventbrite` varchar(15) NOT NULL,
  `title` varchar(200) NOT NULL,
  `created` int(14) NOT NULL,
  `organizer_name` varchar(100) NOT NULL,
  `uri` varchar(200) NOT NULL,
  `start_date` int(14) NOT NULL,
  `end_date` int(14) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `address` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

