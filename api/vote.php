<?php
// Include the database connection
include('../api/db_connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userdata'])) {
    echo "<script>
            alert('You are not logged in!');
            window.location = '../routes/login.php';
          </script>";
    exit();
}

$userdata = $_SESSION['userdata'];

// Check if the user has already voted
if (isset($userdata['vote_status']) && $userdata['vote_status'] == 1) {
    echo "<script>
            alert('You have already voted!');
            window.location = '../routes/dashboard.php'; // Redirect to dashboard
          </script>";
    exit();
}

// Check if the form is submitted and party_id is provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['party_id'])) {
    $party_id = $_POST['party_id'];
    $user_id = $userdata['user_id']; // Assuming user_id is stored in session data

    // Insert the vote into the votes table
    $insertVoteQuery = "INSERT INTO votes (user_id, party_id) VALUES (?, ?)";

    if ($stmt = $conn->prepare($insertVoteQuery)) {
        $stmt->bind_param('ii', $user_id, $party_id); // Bind user_id and party_id as integers
        if ($stmt->execute()) {
            // Update the user's vote_status in the users table
            $updateVoteStatusQuery = "UPDATE users SET vote_status = 1, party_id = ? WHERE user_id = ?";
            if ($stmtUpdate = $conn->prepare($updateVoteStatusQuery)) {
                $stmtUpdate->bind_param('ii', $party_id, $user_id);
                $stmtUpdate->execute();

                // Update session with new vote status
                $_SESSION['userdata']['vote_status'] = 1; // Ensure the session reflects the new vote status

                // Close statements
                $stmtUpdate->close();
            } else {
                echo "<script>
                        alert('Error updating vote status!');
                      </script>";
            }

            // Close the vote insert statement
            $stmt->close();

            // Redirect or display success message
            echo "<script>
                    alert('Vote has been successfully recorded!');
                    window.location = '../routes/dashboard.php'; // Redirect to dashboard
                  </script>";
        } else {
            echo "<script>
                    alert('Error in recording your vote. Please try again.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('Error in preparing SQL query.');
              </script>";
    }
}

// Function to get all parties from the database
function getParties() {
    global $conn;
    $sql = "SELECT * FROM parties";
    return $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* Your CSS code here */
    </style>
</head>
<body>

    <div id="headerSection">
        <button id="backBtn" onclick="window.history.back();">Back</button>
        <h1>Your Vote, Your Voice: Honoring Our Nation's Duty</h1>
        <form action="../api/logout.php" method="POST">
            <button id="logoutBtn" type="submit">Logout</button>
        </form>
    </div>

    <div id="container">
        <div id="profileSection">
            <h2>Candidate Details</h2>
            <hr>
            <div class="profile-info">
                <!-- Profile Picture and User Info -->
                <p><strong>Name:</strong> <?php echo $userdata['name']; ?></p>
                <p><strong>Voting Status:</strong> <span id="votingStatus" class="<?php echo ($userdata['vote_status'] == 1) ? 'voted-status' : 'not-voted-status'; ?>">
                    <?php echo ($userdata['vote_status'] == 1) ? 'Voted' : 'Not Voted'; ?>
                </span></p>
            </div>
        </div>

        <div id="boxList">
            <?php
            // Retrieve all parties from the database
            $parties = getParties();
            while ($party = $parties->fetch_assoc()) {
                echo '
                    <form action="vote.php" method="POST">
                        <div class="box">
                            <div class="box-content">
                                <div class="box-name">' . $party['party_name'] . '</div>
                            </div>
                            <div class="box-logo">
                                <img src="' . $party['logo'] . '" class="box-logo" alt="' . $party['party_name'] . ' Logo">
                            </div>
                            <button class="vote-btn" type="submit" name="party_id" value="' . $party['party_id'] . '" ' . (($userdata['vote_status'] == 1) ? 'disabled' : '') . '>Vote</button>
                        </div>
                    </form>';
            }
            ?>
        </div>
    </div>

    <script>
        // Your JS code if needed
    </script>

</body>
</html>
