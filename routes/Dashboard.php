<?php
   session_start();
   if(!isset($_SESSION['userdata'])){
    header("location: ../index.html");
   }

   $userdata = $_SESSION['userdata'];
   $candidatedata = $_SESSION['candidatedata'];

   if($_SESSION['userdata']['status']==0){
      $status = '<b style="color:red" > Not Voted </b>';
   }
   else{
      $status = '<b style="color:green" > Voted </b>';
   }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOE Voting - Dashboard</title>
<link rel="stylesheet" href="../css/Dashboard.css">
</head>
<body>
      <header class="brand-header">
        <div class="brand-container">
            <img src="../images and logo/logoSCOE.png" alt="SCOE Logo" class="brand-logo">
            <div class="brand-text">
                <h1>Siddhant College of Engineering</h1>
                <p>Online Voting System</p>
            </div>
        </div>
      </header>
      <div id="mainSection">
      <div class="top-actions">
        <a href="../welcome.html"><button id="backbtn">Home</button></a>
        <a href="logout.php"><button id="logoutbtn">Log Out</button></a>
      </div>
      <hr>
      <div id="mainpanel">
<div id="Profile">
        <center> <img id="profilephoto" src="../Uploads/<?php echo $userdata['photo'] ?>" onerror="this.src='../Uploads/default.png'" height="200" width="200"><br><br></center>
         <b>Name: </b><?php echo $userdata['name'] ?><br><br>
         <?php if(isset($userdata['div_roll_no'])): ?>
         <b>Div - Roll No: </b><?php echo $userdata['div_roll_no'] ?><br><br>
         <?php endif; ?>
         <?php if(isset($userdata['branch']) && $userdata['branch']): ?>
         <b>Branch: </b><?php echo htmlspecialchars($userdata['branch']) ?><br><br>
         <?php endif; ?>
         <b>Status: </b><?php echo $status ?><br><br>
      </div>
      <div id="Candidate" style="float:right;">
   <?php
   if ($_SESSION['candidatedata']) {
      for ($i = 0; $i < count($candidatedata); $i++) {
   ?>
         <div>
            <img style="float: right;" src="../Uploads/<?php echo $candidatedata[$i]['photo'] ?>" height="100" width="100">
<b>Candidate Name: </b><?php echo $candidatedata[$i]['name'] ?><br><br>
            <form action="../api/vote.php" method="post">
               <input type="hidden" name="Cvotes" value="<?php echo $candidatedata[$i]['vote'] ?>">
               <input type="hidden" name="Cid" value="<?php echo $candidatedata[$i]['id'] ?>">
               <?php
               if ($_SESSION['userdata']['status'] == 0) {
               ?>
                  <input type="submit" name="votebtn" value="Vote" id="votebtn">
               <?php
               } else {
               ?>
                  <button disabled type="button" name="votebtn" value="Vote" id="voted">Voted</button>
               <?php
               }
               ?>
            </form>
</div>
         <hr>
   <?php
      }
   } else {
   }
   ?>
</div>
 
      </div>
</body>
</html>
