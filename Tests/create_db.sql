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

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES 
(1,'alex','alex@alex','$2y$10$2El2RAsBDW8O.S7S3RlCdO.ID253SsAcnK1UlRrSA2qIXncndSs5G',0,NULL,NULL,NULL,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),
(2,'sandy','','$2y$10$3Ph6ZpgvBK/L2L92frkHAuiA.xRu7wTtzDDrPGXe1d.Qq63aSlKXq',0,NULL,NULL,NULL,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),
(3,'bob','','$2y$10$tDNcwMA1keE93TqVnYe4oOypdtt7XaekWjZAGoPaNCHx6Lqc8sRYa',0,NULL,NULL,NULL,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),
(4,'ken','ken','$2y$10$P.OlaYtCbYjk39Et/YOG0uCdHLrkaIzmDf6oO27mUiBh65VquMcHu',0,NULL,NULL,NULL,0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
UNLOCK TABLES;


--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
INSERT INTO `messages` VALUES 
(1,1,'hello everyone','2018-12-04 03:06:06'),
(2,3,'hey, what\'s up Alex?','2018-12-04 03:06:25'),
(3,2,'lets party!','2018-12-04 03:06:59'),
(4,4,'Oh for goodness sake, pipe down','2018-12-04 03:07:23'),
(5,3,'Seriously?','2018-12-04 03:07:39');
UNLOCK TABLES;

--
-- Table structure for table `users`
--


-- Dump completed on 2018-12-04 13:51:36
