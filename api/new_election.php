<?php
  session_start();
  include("connect.php");

  if (!isset($_SESSION['admindata'])) {
    header("location: ../welcome.html");
    exit;
  }

  // Only allowed when current election has ended (status=2)
  $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  $data = mysqli_fetch_array($election);

  if ($data['status'] != 2) {
    echo '<script>alert("A new election can only be started after the current election has ended."); window.location = "../routes/AdminDashboard.php";</script>';
    exit;
  }

  // Reset for a new election:
  // 1. Clear all votes and reset voter statuses
  mysqli_query($connect, "UPDATE user SET vote=0 WHERE role=2");
  mysqli_query($connect, "UPDATE user SET status=0 WHERE role=1");

  // 2. Reset the election record to "not started"
  $newElectionName = 'Election ' . date('d M Y');
  mysqli_query($connect, "UPDATE election SET name='" . mysqli_real_escape_string($connect, $newElectionName) . "', status=0, started_at=NULL, ended_at=NULL WHERE id='" . $data['id'] . "'");

  header("location: ../routes/AdminDashboard.php");
?>
