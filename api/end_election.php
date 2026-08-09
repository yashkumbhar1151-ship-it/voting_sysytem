<?php
  session_start();
  include("connect.php");

  if (!isset($_SESSION['admindata'])) {
    header("location: ../welcome.html");
    exit;
  }

  $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  $data = mysqli_fetch_array($election);

  $update = mysqli_query($connect, "UPDATE election SET status=2, ended_at=NOW() WHERE id='" . $data['id'] . "'");

  if ($update) {
    header("location: ../routes/AdminDashboard.php");
  } else {
    echo '<script>alert("Error ending election"); window.location = "../routes/AdminDashboard.php";</script>';
  }
?>
