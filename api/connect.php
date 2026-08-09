<?php

  $host = getenv('MYSQLHOST') ?: 'localhost';
  $user = getenv('MYSQLUSER') ?: 'root';
  $pass = getenv('MYSQLPASSWORD') ?: '';
  $db   = getenv('MYSQLDATABASE') ?: 'voting';
  $port = getenv('MYSQLPORT') ?: '3306';

  $connect = mysqli_connect($host, $user, $pass, $db, $port) or die("connection failed!");

?>
