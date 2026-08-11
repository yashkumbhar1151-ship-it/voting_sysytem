<?php
  include("connect.php");

  $name = $_POST['name'];
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];
  $image = $_FILES['photo']['name'];
  $tmp_name = $_FILES['photo']['tmp_name'];
  $role = $_POST['role'];

  // Preparing the SQL and binding parameters
  $stmt = $connect->prepare("SELECT * FROM user WHERE name= ?");
  $stmt->bind_param("s", $name);
  
  //Executing the statement
  $stmt->execute();
  
  //Checking if username exists
  if($stmt->get_result()->num_rows > 0) {
    

echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SweetAlert Example</title>
    <link rel='stylesheet' href='../sweetalert/sweetalert.css'>
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

<script src='../sweetalert/sweetalert.all.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert();
    });

    function showAlert() {
        Swal.fire({
            title: 'Username Already exist!',
            text: 'Please choose another username',
            icon: 'info',
            confirmButtonText: 'OK'
          }).then(() => {
            history.back();
        });
    }
</script>

</body>
</html>
    
    ";


    exit;
  }

  if($password == $cpassword){
    move_uploaded_file($tmp_name, "../Uploads/$image");
  
    // Preparing the SQL and binding parameters
    $stmt = $connect->prepare("INSERT INTO user (name, password, photo, role, status, vote) VALUE (?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("ssss", $name, $password, $image, $role);
    
    //Executing the insert statement
    if($stmt->execute()){
      echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SweetAlert Example</title>
    <link rel='stylesheet' href='../sweetalert/sweetalert.css'>
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

<script src='../sweetalert/sweetalert.all.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert();
    });

    function showAlert() {
        Swal.fire({
            title: 'Registration Successful!!',
            text: 'Please login to vote',
            icon: 'success',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = '../index.html';
        });
    }
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
        <title>SweetAlert Example</title>
        <link rel='stylesheet' href='../sweetalert/sweetalert.css'>
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
    
    <script src='../sweetalert/sweetalert.all.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert();
        });
    
        function showAlert() {
            Swal.fire({
                title: 'Password Mismatch!!',
                text: 'Password and confirm password are different',
                icon: 'error',
                confirmButtonText: 'OK'
              }).then(() => {
                history.back();
            });
        }
    </script>
    
    </body>
    </html>
        
        ";
  }
?>

