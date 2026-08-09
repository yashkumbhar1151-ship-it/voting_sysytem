<?php
  include("connect.php");

  header('Content-Type: application/json');

  $newPassword = 'SCOE@admin2026';

  $u = mysqli_query($connect, "SELECT * FROM user WHERE role=3 LIMIT 1");
  if ($u && mysqli_num_rows($u) > 0) {
    $admin = mysqli_fetch_array($u);
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    mysqli_query($connect, "UPDATE user SET Password='" . mysqli_real_escape_string($connect, $hash) . "' WHERE id='" . $admin['id'] . "'");
    echo json_encode(['ok' => true, 'new_admin_password' => $newPassword]);
  } else {
    echo json_encode(['ok' => false, 'error' => 'no admin found']);
  }
?>
