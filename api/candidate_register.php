<?php
  session_start();
  include("connect.php");

  if(!isset($_SESSION['admindata'])){
    header("location: ../welcome.html");
    exit;
  }

  // Election gate: can only add candidates when election not started
  $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
  $electionData = mysqli_fetch_array($election);
  if ($electionData && $electionData['status'] != 0) {
    echo '<script>alert("Candidates can only be added before the election starts."); window.location = "../routes/AdminDashboard.php";</script>';
    exit;
  }

  $name = $_POST['name'];
  $branch = isset($_POST['branch']) ? $_POST['branch'] : '';

  // Photo is optional; default avatar when not uploaded
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $imageData = file_get_contents($_FILES['photo']['tmp_name']);
    $imageType = mime_content_type($_FILES['photo']['tmp_name']);
    if (strpos($imageType, 'image/') !== 0) {
      echo '<script>alert("Please upload a valid image file."); window.location = "../routes/AdminDashboard.php";</script>';
      exit;
    }
    $photo = 'data:' . $imageType . ';base64,' . base64_encode($imageData);
  } else {
    $photo = 'default.png';
  }

  // Check if candidate name already exists
  $stmt = $connect->prepare("SELECT * FROM user WHERE name= ? AND role=2");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  if($stmt->get_result()->num_rows > 0){
    echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    Swal.fire({
        title: 'Candidate Already Exist!',
        text: 'Please choose another name',
        icon: 'info',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location = '../routes/AdminDashboard.php';
    });
</script>
    ";
    exit;
  }

  // Insert candidate (role=2)
  $stmt = $connect->prepare("INSERT INTO user (name, password, photo, role, status, vote, branch) VALUES (?, '', ?, 2, 0, 0, ?)");
  $stmt->bind_param("sss", $name, $photo, $branch);

  if($stmt->execute()){
    echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    Swal.fire({
        title: 'Candidate Added!',
        text: 'Candidate registered successfully',
        icon: 'success',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location = '../routes/AdminDashboard.php';
    });
</script>
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
