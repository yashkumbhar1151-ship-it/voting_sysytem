<?php
   session_start();
   include("connect.php");

   $name = $_POST['name'];
   $Password = isset($_POST['Password']) ? $_POST['Password'] : (isset($_POST['password']) ? $_POST['password'] : '');
   $role = $_POST['role'];

   $check = mysqli_query($connect, "SELECT * FROM user WHERE name='$name' AND role='$role' LIMIT 1");

   if(mysqli_num_rows($check)>0){
     $row = mysqli_fetch_array($check);
     $stored = $row['password'];
     if (!(password_verify($Password, $stored) || $Password === $stored)) {
       // invalid password -> fall through to error
     } else {
     $userdata = $row;
     $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
     $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);

     $_SESSION['userdata'] = $userdata;
     $_SESSION['candidatedata'] = $candidatedata;

     echo'
     <script>
          window.location = "../routes/Dashboard.php";
     </script>
    '; 
     exit;
     }
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
         <style>
             body {
                 font-family: Arial, sans-serif;
                 display: flex;
                 align-items: center;
                 justify-content: center;
                 height: 100vh;
                 margin: 0;
             }
         </style>
     </head>
     <body>
     
     <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             showAlert();
         });
     
         function showAlert() {
             Swal.fire({
                 title: 'INVALID DATA',
                 text: 'please register or check info!',
                 icon: 'error',
                 confirmButtonText: 'OK'
               }).then(() => {
                 window.location.href = '../index.html';
             });
         }
     </script>
     
     </body>
     </html>
         
         "; 
   }

?>