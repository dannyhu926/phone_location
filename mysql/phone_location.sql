CREATE TABLE `phone_location` (
  `id` int(11) NOT NULL,
  `phone` int(7) DEFAULT NULL,
  `province` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `operators` varchar(16) DEFAULT NULL,
  `area_code` varchar(8) DEFAULT NULL,
  `post_code` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;