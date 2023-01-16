use baddb;
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `username` varchar(100) PRIMARY KEY,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roles` smallint DEFAULT 0,
  `last_name` varchar (100) DEFAULT NULL,
  `first_name` varchar (100) DEFAULT NULL,
  `phone` varchar (100) DEFAULT NULL,
  `active_flag` smallint DEFAULT 0,
  `last_active` timestamp DEFAULT 0,
  `created_date` timestamp DEFAULT 0
) ;



use chattest;

DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `users`;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roles` smallint(6) DEFAULT '0',
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `active_flag` smallint(6) DEFAULT '0',
  `last_active` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `text` varchar(1024) DEFAULT NULL,
  `senttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

