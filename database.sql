CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT 'default.png',
  `role` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `vote` int(11) NOT NULL DEFAULT 0,
  `div_roll_no` varchar(100) DEFAULT NULL,
  `appar_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appar_id_unique` (`appar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: insert a default admin (change password after first login)
-- INSERT INTO `user` (name, password, photo, role, status, vote)
-- VALUES ('admin', 'admin123', 'default.png', 3, 0, 0);
