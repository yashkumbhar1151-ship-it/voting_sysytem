<?php
   session_start();
   if(!isset($_SESSION['admindata'])){
    header("location: ../welcome.html");
   }

   include("../api/connect.php");

   $candidate = mysqli_query($connect, "SELECT * FROM user WHERE role=2 ORDER BY vote DESC");
   $candidatedata = mysqli_fetch_all($candidate, MYSQLI_ASSOC);

   $voter = mysqli_query($connect, "SELECT * FROM user WHERE role=1");
   $votercount = mysqli_num_rows($voter);

   $voted = mysqli_query($connect, "SELECT * FROM user WHERE role=1 AND status=1");
   $votedcount = mysqli_num_rows($voted);

   $election = mysqli_query($connect, "SELECT * FROM election ORDER BY id DESC LIMIT 1");
   $electionData = mysqli_fetch_array($election);
   $electionStatus = $electionData ? $electionData['status'] : 0;
   // 0 = Not started, 1 = Live, 2 = Ended

   // Winner calculation for announcement
   $winner = null;
   if ($electionStatus == 2) {
     $maxVotes = 0;
     foreach ($candidatedata as $cand) {
       if ($cand['vote'] > $maxVotes) {
         $maxVotes = $cand['vote'];
       }
     }
     if ($maxVotes > 0) {
       foreach ($candidatedata as $cand) {
         if ($cand['vote'] == $maxVotes) {
           $winner = $cand;
           break;
         }
       }
     }
   }
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
        /* Election status banner */
        #electionBanner {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 600;
            font-size: 16px;
        }
        .status-notstarted { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-live { background: #d4edda; color: #155724; border: 1px solid #28a745; }
        .status-ended { background: #f8d7da; color: #721c24; border: 1px solid #dc3545; }

        #electionActions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        #electionActions button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
        }
        .btn-start { background: linear-gradient(135deg, #1b5e20, #2e7d32); }
        .btn-end { background: linear-gradient(135deg, #b71c1c, #d32f2f); }
        .btn-new { background: linear-gradient(135deg, #0d47a1, #1976d2); }
        .btn-start:hover, .btn-end:hover, .btn-new:hover { opacity: 0.9; }

        /* Winner announcement */
        #winnerBox {
            text-align: center;
            background: linear-gradient(135deg, var(--scoe-gold) 0%, #b8860b 100%);
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        #winnerBox img {
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
        }
        #winnerBox h2 { margin: 10px 0 5px 0; font-size: 28px; }
        #winnerBox p { margin: 3px 0; font-size: 16px; }

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
        #candidateList { margin-top: 30px; }
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
        #candidateCard img { border-radius: 50%; object-fit: cover; }
        #candidateCard .vote-count {
            margin-left: auto;
            background: var(--scoe-maroon);
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            min-width: 80px;
            text-align: center;
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
        .top-actions { margin: 15px 0; display: flex; justify-content: space-between; }
        .hidden { display: none; }
        #deleteForm { display: inline; }
        .delete-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        #noVotesMsg { color: #888; text-align: center; }
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

        <!-- Election status banner -->
        <?php if ($electionStatus == 0): ?>
            <div id="electionBanner" class="status-notstarted">Election Status: NOT STARTED</div>
        <?php elseif ($electionStatus == 1): ?>
            <div id="electionBanner" class="status-live">Election Status: LIVE - Voting is open</div>
        <?php else: ?>
            <div id="electionBanner" class="status-ended">Election Status: ENDED</div>
        <?php endif; ?>

        <!-- Election controls -->
        <div id="electionActions">
            <?php if ($electionStatus == 0): ?>
                <a href="../api/start_election.php"><button class="btn-start">Start Election</button></a>
            <?php elseif ($electionStatus == 1): ?>
                <a href="../api/end_election.php"><button class="btn-end">End Election</button></a>
            <?php else: ?>
                <a href="../api/new_election.php" onclick="return confirm('Start a new election?\n\nThis will:\n- Clear all vote counts\n- Reset all voters so they can vote again\n- Add candidates for the new election');"><button class="btn-new">Conduct Another Election</button></a>
            <?php endif; ?>
        </div>

        <!-- Winner announcement -->
        <?php if ($electionStatus == 2 && $winner): ?>
            <div id="winnerBox">
                <h2>🏆 WINNER ANNOUNCEMENT 🏆</h2>
                <?php
                $photoSrc = (strpos($winner['photo'], 'data:') === 0) ? $winner['photo'] : '../Uploads/' . $winner['photo'];
                ?>
                <img src="<?php echo $photoSrc; ?>" height="120" width="120" onerror="this.src='../Uploads/default.png'">
                <h2><?php echo htmlspecialchars($winner['name']); ?></h2>
                <p>Won with <strong><?php echo $winner['vote']; ?></strong> votes</p>
                <p style="font-size:13px; opacity:0.85;">Election: <?php echo htmlspecialchars($electionData['name']); ?></p>
            </div>
        <?php elseif ($electionStatus == 2 && !$winner): ?>
            <div id="winnerBox" style="background: #6c757d;">
                <h2>Election Ended</h2>
                <p>No votes were cast. No winner to announce.</p>
            </div>
        <?php endif; ?>

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
            <div id="statBox">
                <h2><?php echo $votercount > 0 ? round(($votedcount / $votercount) * 100) . '%' : '0%'; ?></h2>
                <p>Turnout</p>
            </div>
        </div>

        <hr>

        <!-- Add candidate form (only when election not started) -->
        <?php if ($electionStatus == 0): ?>
        <h3 style="text-align:center; color: var(--scoe-maroon);">Add New Candidate</h3>
        <form id="addCandidateForm" action="../api/candidate_register.php" method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Candidate Name" required>
            <select name="branch" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ced4da; border-radius:4px; box-sizing:border-box;">
                <option value="">Select Branch (optional)</option>
                <option value="AIML">AIML</option>
                <option value="AIDS">AIDS (Artificial Intelligence & Data Science)</option>
                <option value="Computer Engineering">Computer Engineering</option>
                <option value="IT">Information Technology</option>
                <option value="Mechanical">Mechanical Engineering</option>
                <option value="Civil">Civil Engineering</option>
                <option value="ENTC">ENTC (Electronics & Telecomm)</option>
                <option value="Electrical">Electrical Engineering</option>
                <option value="Chemical">Chemical Engineering</option>
            </select>
            <input type="file" name="photo" accept="image/*">
            <small style="display:block; text-align:center; color:#888; margin-bottom:10px;">Photo is optional - a default avatar will be used.</small>
            <center><button type="submit">Add Candidate</button></center>
        </form>
        <?php else: ?>
        <p id="noVotesMsg">Candidate registration is locked while the election is <?php echo $electionStatus == 1 ? 'live' : 'ended'; ?>.</p>
        <?php endif; ?>

        <div id="candidateList">
            <h3 style="text-align:center; color: var(--scoe-maroon);">Registered Candidates</h3>
            <?php
            if(count($candidatedata) > 0){
                foreach($candidatedata as $cand){
                    $photoSrc = (strpos($cand['photo'], 'data:') === 0) ? $cand['photo'] : '../Uploads/' . $cand['photo'];
            ?>
                <div id="candidateCard">
                    <img src="<?php echo $photoSrc; ?>" height="80" width="80" onerror="this.src='../Uploads/default.png'">
                    <div>
                        <b>Name:</b> <?php echo htmlspecialchars($cand['name']) ?><br>
                        <b>Branch:</b> <?php echo htmlspecialchars($cand['branch'] ?? 'N/A') ?>
                        <?php if ($electionStatus == 2): ?>
                            <br><b>Votes:</b> <?php echo $cand['vote'] ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($electionStatus == 0): ?>
                    <div class="vote-count"><?php echo $cand['vote']; ?> votes</div>
                    <?php endif; ?>
                    <?php if ($electionStatus == 0): ?>
                    <form id="deleteForm" action="../api/delete_candidate.php" method="post" onsubmit="return confirm('Delete this candidate?');">
                        <input type="hidden" name="cid" value="<?php echo $cand['id']; ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                    <?php endif; ?>
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
