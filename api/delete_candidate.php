<?php
  session_start();
  include("connect.php");

  if (!isset($_SESSION['admindata'])) {
    header("location: ../welcome.html");
    exit;
  }

  // Only allow deletion before election starts
  $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  $electionData = mysqli_fetch_array($election);
  if ($electionData && $electionData['status'] != 0) {
    echo '<script>alert("Candidates cannot be deleted after election starts."); window.location = "../routes/AdminDashboard.php";</script>';
    exit;
  }

  $cid = $_POST['cid'];
  $delete = mysqli_query($connect, "DELETE FROM user WHERE id='$cid' AND role=2");

  if ($delete) {
    header("location: ../routes/AdminDashboard.php");
  } else {
    echo '<script>alert("Error deleting candidate"); window.location = "../routes/AdminDashboard.php";</script>';
  }
?>
