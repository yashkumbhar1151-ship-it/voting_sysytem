<?php
  session_start();
  include("connect.php");

  if (!isset($_SESSION['admindata'])) {
    header("location: ../welcome.html");
    exit;
  }

  // Reset test data: clear votes and voter statuses, set election live
  mysqli_query($connect, "UPDATE user SET vote=0 WHERE role=2");
  mysqli_query($connect, "UPDATE user SET status=0 WHERE role=1");
  mysqli_query($connect, "UPDATE election SET status=1, started_at=NOW(), ended_at=NULL WHERE id=(SELECT id FROM (SELECT id FROM election ORDER BY id DESC LIMIT 1) t)");

  header('Content-Type: application/json');
  echo json_encode(['reset' => 'ok', 'election' => 'live']);
?>
