<?php
  include("connect.php");

  header('Content-Type: application/json');

  // Reset test data: clear candidates/voters added during testing, reset election
  mysqli_query($connect, "DELETE FROM user WHERE role=1 AND name IN ('Student1','TestVoter2','aryan')");
  mysqli_query($connect, "DELETE FROM user WHERE role=2");
  mysqli_query($connect, "UPDATE election SET status=0, started_at=NULL, ended_at=NULL WHERE id=(SELECT id FROM (SELECT id FROM election ORDER BY id DESC LIMIT 1) t)");

  echo json_encode(['ok' => true]);
?>
