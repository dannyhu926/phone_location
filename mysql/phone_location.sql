CREATE TABLE `phone_location` (
  `id` int(11) NOT NULL,
  `phone` int(7) DEFAULT NULL COMMENT '手机号段',
  `province` varchar(40) DEFAULT NULL COMMENT '省份',
  `city` varchar(40) DEFAULT NULL COMMENT '城市',
  `operators` varchar(16) DEFAULT NULL COMMENT '运营商',
  `post_code` varchar(16) DEFAULT NULL COMMENT '邮编',
  `area_code` varchar(8) DEFAULT NULL COMMENT '区号',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;