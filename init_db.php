<?php

  $host = getenv('MYSQLHOST') ?: 'localhost';
  $user = getenv('MYSQLUSER') ?: 'root';
  $pass = getenv('MYSQLPASSWORD') ?: '';
  $db   = getenv('MYSQLDATABASE') ?: 'railway';
  $port = getenv('MYSQLPORT') ?: '3306';

  $connect = mysqli_connect($host, $user, $pass, $db, $port);

  if (!$connect) {
    die("connection failed: " . mysqli_connect_error());
  }

  $createTable = "CREATE TABLE IF NOT EXISTS `user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL DEFAULT '',
    `photo` varchar(255) NOT NULL DEFAULT 'default.png',
    `role` int(11) NOT NULL DEFAULT 0,
    `status` int(11) NOT NULL DEFAULT 0,
    `vote` int(11) NOT NULL DEFAULT 0,
    `div_roll_no` varchar(100) DEFAULT NULL,
    `branch` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

  mysqli_query($connect, $createTable);

  $checkAdmin = mysqli_query($connect, "SELECT * FROM user WHERE role=3 LIMIT 1");
  if (mysqli_num_rows($checkAdmin) == 0) {
    mysqli_query($connect, "INSERT INTO user (name, password, photo, role, status, vote) VALUES ('admin', 'admin123', 'default.png', 3, 0, 0)");
  }

  $checkTable = mysqli_query($connect, "SHOW TABLES LIKE 'user'");
  if (mysqli_num_rows($checkTable) > 0) {
    echo "Database initialized successfully! Table 'user' is ready. Admin login: admin / admin123";
  } else {
    echo "Error: Table creation failed.";
  }

  mysqli_close($connect);

?>
