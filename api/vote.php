<?php

  session_start();
  include('connect.php');

$Cvotes = $_POST['Cvotes'];
$total_votes = $Cvotes+1;
$Cid = $_POST['Cid'];
$uid = $_SESSION['userdata']['id']; 

$update_votes = mysqli_query($connect, "UPDATE user SET vote='$total_votes' WHERE id='$Cid'");
$update_user_status = mysqli_query($connect,"UPDATE user SET status=1 WHERE id='$uid'");

if($update_votes and $update_user_status){

    $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);

    $_SESSION['userdata']['status'] = 1;
    $_SESSION['candidatedata'] = $candidatedata;

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
                position: 'top-end',
                icon: 'success',
                title: 'Voting Successful',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.href = '../routes/Dashboard.php';
            });
        }
    </script>
    
    </body>
    </html>
        
        ";
  

}
else{
    echo'
    <script>
         alert("Some error occured!");
         window.location = "../routes/Dashboard.php";
    </script>
   '; 
}

?>