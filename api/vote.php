<?php

  session_start();
  include('connect.php');

  // Election gate: only allow voting while election is LIVE (status=1)
  $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  $electionData = mysqli_fetch_array($election);
  $electionStatus = $electionData ? $electionData['status'] : 0;

  if ($electionStatus != 1) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Voting Closed</title>
        <link rel='stylesheet' href='../sweetalert/sweetalert.css'>
    </head>
    <body>
    <script src='../sweetalert/sweetalert.all.min.js'></script>
    <script>
        Swal.fire({
            title: 'Voting is Closed',
            text: 'The election is not currently open for voting.',
            icon: 'info',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../routes/Dashboard.php';
        });
    </script>
    </body>
    </html>
    ";
    exit;
  }

$Cvotes = $_POST['Cvotes'];
$total_votes = $Cvotes+1;
$Cid = $_POST['Cid'];
$uid = $_SESSION['userdata']['id'];

// Prevent duplicate vote at DB level too
$userCheck = mysqli_query($connect, "SELECT status FROM user WHERE id='$uid'");
$urow = mysqli_fetch_assoc($userCheck);

if ($urow && $urow['status'] == 1) {
  echo '<script>alert("You have already voted!"); window.location = "../routes/Dashboard.php";</script>';
  exit;
}

$update_votes = mysqli_query($connect, "UPDATE user SET vote='$total_votes' WHERE id='$Cid' AND role=2");
$votes_affected = mysqli_affected_rows($connect);
$update_user_status = mysqli_query($connect,"UPDATE user SET status=1 WHERE id='$uid'");
$status_affected = mysqli_affected_rows($connect);

if($update_votes and $update_user_status and $votes_affected > 0 and $status_affected > 0){

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
        <title>Voting Successful</title>
        <link rel='stylesheet' href='../sweetalert/sweetalert.css'>
    </head>
    <body>
    <script src='../sweetalert/sweetalert.all.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Voting Successful',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.href = '../routes/Dashboard.php';
            });
        });
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
