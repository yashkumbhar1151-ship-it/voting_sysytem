<?php
   session_start();
   include("connect.php");

   $admin_name = $_POST['admin_name'];
   $admin_password = $_POST['admin_password'];

   // Admin is a user with role=3
   $check = mysqli_query($connect, "SELECT * FROM user WHERE name='$admin_name' AND role=3 LIMIT 1");

   if(mysqli_num_rows($check)>0){
     $admin_data = mysqli_fetch_array($check);
     $valid = password_verify($admin_password, $admin_data['Password']) || $admin_password === $admin_data['Password'];
     if(!$valid){
       echo 'INVALID';
       exit;
     }
     $_SESSION['admindata'] = $admin_data;

     echo'
     <script>
          window.location = "../routes/AdminDashboard.php";
     </script>
    ';
   }
   else{
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
            title: 'INVALID DATA',
            text: 'Invalid admin credentials!',
            icon: 'error',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../routes/AdminLogin.html';
        });
    });
</script>
</body>
</html>
     ";
   }
?>
