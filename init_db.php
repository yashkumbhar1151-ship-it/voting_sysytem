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

  // Migration: drop appar_id, add branch, enlarge photo (handles older DBs)
  $cols = mysqli_query($connect, "SHOW COLUMNS FROM user LIKE 'appar_id'");
  if ($cols && mysqli_num_rows($cols) > 0) {
    mysqli_query($connect, "ALTER TABLE user DROP COLUMN appar_id");
  }
  $cols = mysqli_query($connect, "SHOW COLUMNS FROM user LIKE 'branch'");
  if ($cols && mysqli_num_rows($cols) == 0) {
    mysqli_query($connect, "ALTER TABLE user ADD COLUMN branch varchar(100) DEFAULT NULL AFTER div_roll_no");
  }
  $cols = mysqli_query($connect, "SHOW COLUMNS FROM user LIKE 'photo'");
  if ($cols) {
    $ph = mysqli_fetch_array($cols);
    if (strtoupper(substr($ph['Type'], 0, 3)) !== 'LON') {
      mysqli_query($connect, "ALTER TABLE user MODIFY COLUMN photo LONGTEXT");
    }
  }

  $createElection = "CREATE TABLE IF NOT EXISTS `election` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT 'General Election',
    `status` int(11) NOT NULL DEFAULT 0,
    `started_at` datetime DEFAULT NULL,
    `ended_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

  mysqli_query($connect, $createElection);

  $checkElection = mysqli_query($connect, "SELECT * FROM election LIMIT 1");
  if (mysqli_num_rows($checkElection) == 0) {
    mysqli_query($connect, "INSERT INTO election (name, status) VALUES ('General Election', 0)");
  }

  $checkAdmin = mysqli_query($connect, "SELECT * FROM user WHERE role=3 LIMIT 1");
  if (mysqli_num_rows($checkAdmin) == 0) {
    mysqli_query($connect, "INSERT INTO user (name, password, photo, role, status, vote) VALUES ('admin', 'admin123', 'default.png', 3, 0, 0)");
  }

  $checkTable = mysqli_query($connect, "SHOW TABLES LIKE 'user'");
  if (mysqli_num_rows($checkTable) > 0) {
    echo "Database initialized successfully! Admin login: admin / admin123";
  } else {
    echo "Error: Table creation failed.";
  }

  mysqli_close($connect);

?>
