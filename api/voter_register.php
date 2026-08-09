<?php
  session_start();
  include("connect.php");

  $name = $_POST['name'];
  $div_roll_no = $_POST['div_roll_no'];
  $branch = $_POST['branch'];

  // Check if user already registered (prevents duplicate voting by name + div_roll_no)
  $stmt = $connect->prepare("SELECT * FROM user WHERE name = ? AND div_roll_no = ?");
  $stmt->bind_param("ss", $name, $div_roll_no);
  $stmt->execute();
  if($stmt->get_result()->num_rows > 0){
    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Already Registered</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11'>
</head>
<body>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Already Registered!',
            text: 'You have already registered. Duplicate voting is not allowed.',
            icon: 'warning',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../index.html';
        });
    });
</script>
</body>
</html>
    ";
    exit;
  }

  // Insert voter (role=1, no password, no photo)
  $stmt = $connect->prepare("INSERT INTO user (name, password, photo, role, status, vote, div_roll_no, branch) VALUES (?, '', 'default.png', 1, 0, 0, ?, ?)");
  $stmt->bind_param("sss", $name, $div_roll_no, $branch);

  if($stmt->execute()){
    // auto-login: fetch the newly created voter and create session
    $check = mysqli_query($connect, "SELECT * FROM user WHERE name='$name' AND div_roll_no='$div_roll_no' ORDER BY id DESC LIMIT 1");
    if(mysqli_num_rows($check) > 0){
      $userdata = mysqli_fetch_array($check);
      $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
      $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);

      $_SESSION['userdata'] = $userdata;
      $_SESSION['candidatedata'] = $candidatedata;

      echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Registration Successful</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11'>
</head>
<body>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Registration Successful!!',
            text: 'You are now registered. You can vote now.',
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../routes/Dashboard.php';
        });
    });
</script>
</body>
</html>
      ";
    } else {
      echo '
        <script>
          alert("Some error occurred!");
          window.history.back();
        </script>
      ';
    }
  } else {
    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Error</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11'>
</head>
<body>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Error!',
            text: 'Some error occurred. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
          }).then(() => {
            history.back();
        });
    });
</script>
</body>
</html>
    ";
  }
?>
