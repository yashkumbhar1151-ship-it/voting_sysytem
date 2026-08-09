<?php
  session_start();
  include("connect.php");

  if(!isset($_SESSION['admindata'])){
    header("location: ../welcome.html");
    exit;
  }

  $name = $_POST['name'];
  $image = $_FILES['photo']['name'];
  $tmp_name = $_FILES['photo']['tmp_name'];

  // Check if candidate name already exists
  $stmt = $connect->prepare("SELECT * FROM user WHERE name= ?");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  if($stmt->get_result()->num_rows > 0){
    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SweetAlert Example</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11'>
</head>
<body>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Candidate Already Exist!',
            text: 'Please choose another name',
            icon: 'info',
            confirmButtonText: 'OK'
          }).then(() => {
            history.back();
        });
    });
</script>
</body>
</html>
    ";
    exit;
  }

  move_uploaded_file($tmp_name, "../Uploads/$image");

  // Insert candidate (role=2)
  $stmt = $connect->prepare("INSERT INTO user (name, password, photo, role, status, vote) VALUES (?, '', ?, 2, 0, 0)");
  $stmt->bind_param("ss", $name, $image);

  if($stmt->execute()){
    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SweetAlert Example</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11'>
</head>
<body>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Candidate Added!',
            text: 'Candidate registered successfully',
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../routes/AdminDashboard.php';
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
?>
