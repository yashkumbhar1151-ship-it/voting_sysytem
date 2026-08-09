<?php
   session_start();
   if(!isset($_SESSION['admindata'])){
    header("location: ../welcome.html");
   }

   include("../api/connect.php");

   $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
   $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);

   $voter = mysqli_query($connect, "SELECT * FROM user WHERE role=1");
   $votercount = mysqli_num_rows($voter);

   $voted = mysqli_query($connect, "SELECT * FROM user WHERE role=1 AND status=1");
   $votedcount = mysqli_num_rows($voted);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCOE Voting - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/brand.css">
    <link rel="stylesheet" href="../css/Dashboard.css">
    <style>
        #adminSection {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        #adminTitle {
            text-align: center;
            color: var(--scoe-maroon);
            font-family: 'Segoe UI', sans-serif;
            margin-top: 10px;
        }
        #addCandidateForm {
            max-width: 400px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        #addCandidateForm input[type="text"],
        #addCandidateForm input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #addCandidateForm button {
            background: linear-gradient(135deg, var(--scoe-maroon) 0%, var(--scoe-red) 100%);
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
        }
        #candidateList {
            margin-top: 30px;
        }
        #candidateCard {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        #candidateCard img {
            border-radius: 50%;
        }
        #stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            flex-wrap: wrap;
            gap: 10px;
        }
        #statBox {
            background: linear-gradient(135deg, var(--scoe-maroon) 0%, var(--scoe-maroon-dark) 100%);
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 150px;
            border-bottom: 3px solid var(--scoe-gold);
        }
        #statBox h2 { margin: 0; color: var(--scoe-gold-light); }
        .top-actions { margin: 15px 0; }
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
    <div id="adminSection">
        <div class="top-actions">
            <a href="../welcome.html"><button id="backbtn">Home</button></a>
            <a href="logout.php"><button id="logoutbtn">Log Out</button></a>
        </div>
        <h1 id="adminTitle">Admin Dashboard</h1>
        <hr>

        <div id="stats">
            <div id="statBox">
                <h2><?php echo count($candidatedata); ?></h2>
                <p>Total Candidates</p>
            </div>
            <div id="statBox">
                <h2><?php echo $votercount; ?></h2>
                <p>Total Voters</p>
            </div>
            <div id="statBox">
                <h2><?php echo $votedcount; ?></h2>
                <p>Voters Voted</p>
            </div>
        </div>

        <hr>

        <h3 style="text-align:center; color: var(--scoe-maroon);">Add New Candidate</h3>
        <form id="addCandidateForm" action="../api/candidate_register.php" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Candidate Name" required>
            <input type="file" name="photo" required>
            <center><button type="submit">Add Candidate</button></center>
        </form>

        <div id="candidateList">
            <h3 style="text-align:center; color: var(--scoe-maroon);">Registered Candidates</h3>
            <?php
            if(count($candidatedata) > 0){
                foreach($candidatedata as $cand){
            ?>
                <div id="candidateCard">
                    <img src="../Uploads/<?php echo $cand['photo'] ?>" height="80" width="80" onerror="this.src='../Uploads/default.png'">
                    <div>
                        <b>Name:</b> <?php echo htmlspecialchars($cand['name']) ?><br>
                        <b>Votes:</b> <?php echo $cand['vote'] ?>
                    </div>
                </div>
            <?php
                }
            } else {
            ?>
                <p style="text-align:center; color:#888;">No candidates added yet.</p>
            <?php
            }
            ?>
        </div>
    </div>
    <footer class="brand-footer">
        <p>&copy; 2026 Siddhant College of Engineering &middot; Powered by Student Council</p>
    </footer>
</body>
</html>
