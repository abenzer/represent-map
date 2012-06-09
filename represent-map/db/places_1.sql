CREATE TABLE IF NOT EXISTS `places` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `approved` int(1) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `lat` float NOT NULL,
  `lng` float NOT NULL,
  `address` varchar(200) NOT NULL,
  `uri` varchar(200) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
