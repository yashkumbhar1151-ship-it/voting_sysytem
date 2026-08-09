<?php
   session_start();
   if(!isset($_SESSION['userdata'])){
    header("location: ../index.html");
    exit;
   }

   include("../api/connect.php");

   // Refresh user data from DB (in case status changed)
   $uid = $_SESSION['userdata']['id'];
   $uq = mysqli_query($connect, "SELECT * FROM user WHERE id='$uid'");
   if (mysqli_num_rows($uq) > 0) {
     $userdata = mysqli_fetch_array($uq);
     $_SESSION['userdata'] = $userdata;
   } else {
     $userdata = $_SESSION['userdata'];
   }

   // Election state
   $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
   $electionData = mysqli_fetch_array($election);
   $electionStatus = $electionData ? $electionData['status'] : 0;
   // 0 = Not started, 1 = Live, 2 = Ended

   $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2 ORDER BY vote DESC");
   $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);
   $_SESSION['candidatedata'] = $candidatedata;

   if($_SESSION['userdata']['status']==0){
      $status = '<b style="color:red" > Not Voted </b>';
   }
   else{
      $status = '<b style="color:green" > Voted </b>';
   }

   // Winner for ended election
   $winner = null;
   if ($electionStatus == 2) {
     $maxVotes = 0;
     foreach ($candidatedata as $cand) {
       if ($cand['vote'] > $maxVotes) $maxVotes = $cand['vote'];
     }
     if ($maxVotes > 0) {
       foreach ($candidatedata as $cand) {
         if ($cand['vote'] == $maxVotes) { $winner = $cand; break; }
       }
     }
   }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOE Voting - Dashboard</title>
<link rel="stylesheet" href="../css/brand.css">
<link rel="stylesheet" href="../css/Dashboard.css">
<style>
    .election-banner { text-align:center; padding: 12px; border-radius: 8px; margin: 15px 0; font-weight: 600; }
    .st-notstarted { background:#fff3cd; color:#856404; border:1px solid #ffc107; }
    .st-live { background:#d4edda; color:#155724; border:1px solid #28a745; }
    .st-ended { background:#f8d7da; color:#721c24; border:1px solid #dc3545; }
    .winner-box {
        text-align:center; background:linear-gradient(135deg, var(--scoe-gold) 0%, #b8860b 100%);
        color:#fff; padding:20px; border-radius:10px; margin:15px 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .winner-box img { border-radius:50%; border:3px solid #fff; object-fit:cover; }
    .winner-box h2 { margin:10px 0 5px 0; font-size:26px; }
</style>
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

      <?php if ($electionStatus == 0): ?>
        <div class="election-banner st-notstarted">Voting has not started yet. Please check back later.</div>
      <?php elseif ($electionStatus == 1): ?>
        <div class="election-banner st-live">Voting is LIVE - Cast your vote!</div>
      <?php else: ?>
        <div class="election-banner st-ended">Voting has ended.</div>
      <?php endif; ?>

      <?php if ($electionStatus == 2 && $winner): ?>
        <div class="winner-box">
            <h2>🏆 WINNER 🏆</h2>
            <?php $photoSrc = (strpos($winner['photo'], 'data:') === 0) ? $winner['photo'] : '../Uploads/' . $winner['photo']; ?>
            <img src="<?php echo $photoSrc; ?>" height="110" width="110" onerror="this.src='../Uploads/default.png'">
            <h2><?php echo htmlspecialchars($winner['name']); ?></h2>
            <p>Winner with <?php echo $winner['vote']; ?> votes</p>
        </div>
      <?php elseif ($electionStatus == 2): ?>
        <div class="winner-box" style="background:#6c757d;">
            <h2>Election Ended</h2>
            <p>No votes were cast.</p>
        </div>
      <?php endif; ?>

      <hr>
      <div id="mainpanel">
      <div id="Profile">
        <?php $photoSrc = (strpos($userdata['photo'], 'data:') === 0) ? $userdata['photo'] : '../Uploads/' . $userdata['photo']; ?>
        <center> <img id="profilephoto" src="<?php echo $photoSrc; ?>" onerror="this.src='../Uploads/default.png'" height="200" width="200"><br><br></center>
         <b>Name: </b><?php echo htmlspecialchars($userdata['name']) ?><br><br>
         <?php if(isset($userdata['div_roll_no']) && $userdata['div_roll_no']): ?>
         <b>Div - Roll No: </b><?php echo htmlspecialchars($userdata['div_roll_no']) ?><br><br>
         <?php endif; ?>
         <?php if(isset($userdata['branch']) && $userdata['branch']): ?>
         <b>Branch: </b><?php echo htmlspecialchars($userdata['branch']) ?><br><br>
         <?php endif; ?>
         <b>Status: </b><?php echo $status ?><br><br>
      </div>
      <div id="Candidate" style="float:right;">
   <?php
   if ($candidatedata) {
      for ($i = 0; $i < count($candidatedata); $i++) {
        $candPhoto = (strpos($candidatedata[$i]['photo'], 'data:') === 0) ? $candidatedata[$i]['photo'] : '../Uploads/' . $candidatedata[$i]['photo'];
   ?>
         <div>
            <img style="float: right;" src="<?php echo $candPhoto; ?>" onerror="this.src='../Uploads/default.png'" height="100" width="100">
<b>Candidate Name: </b><?php echo htmlspecialchars($candidatedata[$i]['name']) ?><br>
<?php if($candidatedata[$i]['branch']): ?><b>Branch: </b><?php echo htmlspecialchars($candidatedata[$i]['branch']) ?><br><?php endif; ?>
            <?php if ($electionStatus == 1 && $userdata['status'] == 0): ?>
            <form action="../api/vote.php" method="post">
               <input type="hidden" name="Cvotes" value="<?php echo $candidatedata[$i]['vote'] ?>">
               <input type="hidden" name="Cid" value="<?php echo $candidatedata[$i]['id'] ?>">
               <input type="submit" name="votebtn" value="Vote" id="votebtn">
            </form>
            <?php elseif ($electionStatus == 1): ?>
               <button disabled type="button" id="voted">You Voted</button>
            <?php elseif ($electionStatus == 2): ?>
               <b>Votes: </b><?php echo $candidatedata[$i]['vote'] ?><br>
            <?php else: ?>
               <button disabled type="button" id="voted">Voting Closed</button>
            <?php endif; ?>
</div>
         <hr>
   <?php
      }
   } else {
      echo '<p style="color:#888; text-align:center;">No candidates registered.</p>';
   }
   ?>
</div>

      </div>
      <footer class="brand-footer" style="margin-top:20px;">
        <p>&copy; 2026 Siddhant College of Engineering &middot; Powered by Student Council</p>
    </footer>
</body>
</html>
