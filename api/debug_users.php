<?php
  session_start();
  include("connect.php");

  if (!isset($_SESSION['admindata'])) {
    header("location: ../welcome.html");
    exit;
  }

  header('Content-Type: application/json');
  $c = mysqli_query($connect, "SELECT id, name, role, vote, status FROM user ORDER BY id");
  $out = [];
  while ($r = mysqli_fetch_assoc($c)) { $out[] = $r; }
  echo json_encode($out);
?>
