<?php
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
$voted = isset($userdata['vote_status']) && $userdata['vote_status'] == 1 ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #headerSection {
            background: linear-gradient(to right, #FF9933, #FFFFFF, #138808);
            color: #000080;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #headerSection h1 {
            margin: 0;
        }

        #backBtn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        #backBtn:hover {
            background-color: #218838;
        }

        #logoutBtn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        #logoutBtn:hover {
            background-color: #c82333;
        }

        #container {
            display: flex;
            justify-content: space-between;
            padding: 1px;
        }

        #profileSection {
            width: 30%;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            justify-content: flex-start;
            min-height: 450px;
            text-align: center;
        }

        .profile-info {
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #138808;
            object-fit: cover;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2em;
            line-height: 1.2;
            width: 100%;
            text-align: left;
            margin-bottom: 0.000001cm;
        }

        .profile-info p strong {
            font-weight: bold;
        }

        h2 {
            color: #0A5EB0;
            margin-bottom: 10px;
            font-size: 1.5em;
        }

        hr {
            width: 100%;
            border: none;
            height: 1px;
            background-color: #007bff;
            margin: 10px 0;
        }

        #boxList {
            width: 65%;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5cm;
            justify-content: flex-start;
            margin-top: 30px;
        }

        .box {
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 20px;
            font-size: 1.2em;
            border-radius: 8px;
            width: 100%;
            justify-content: space-between; /* Ensures space between name, logo, and button */
        }

        .box-content {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Align name and logo on the left */
            flex-grow: 1;
        }

        .box-name {
            font-size: 1em;
            font-weight: bold;
            margin-right: 20px; /* Add space between the name and logo */
        }

        .box-logo {
            max-width: 70px;
            max-height: 70px;
            object-fit: contain;
            margin-right: 10px;/* Space between logo and button */
            margin-left: 200px;
            position: center;
            
        }

        .vote-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .vote-btn:hover {
            background-color: #218838;
        }

        .voted-status {
            color: green;
            font-weight: bold;
        }

        .not-voted-status {
            color: red;
            font-weight: bold;
        }

        .vote-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
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
                <!-- Profile Picture -->
                <?php if ($userdata['image']): ?>
                    <img src="../uploads/<?php echo $userdata['image']; ?>" alt="Profile Picture" class="profile-img">
                <?php else: ?>
                    <img src="../uploads/default.jpg" alt="Profile Picture" class="profile-img">
                <?php endif; ?>

                <!-- User Details -->
                <p><strong>Name:</strong> <?php echo $userdata['name']; ?></p>
                <p><strong>Father's Name:</strong> <?php echo $userdata['father_name']; ?></p>
                <p><strong>Gender:</strong> <?php echo $userdata['gender']; ?></p>
                <p><strong>Date of Birth:</strong> <?php echo $userdata['dob']; ?></p>
                <p><strong>Address:</strong> <?php echo $userdata['address']; ?></p>
                <p><strong>Aadhaar Number:</strong> <?php echo $userdata['aadhaar_number']; ?></p>
                <p><strong>Election Commission ID:</strong> <?php echo $userdata['eci_id']; ?></p>
                <p><strong>Mobile:</strong> <?php echo $userdata['mobile']; ?></p>
                <p><strong>State:</strong> <?php echo $userdata['state']; ?></p>
                <p><strong>Voting Status:</strong> <span id="votingStatus" class="<?php echo $voted ? 'voted-status' : 'not-voted-status'; ?>"><?php echo $voted ? 'Voted' : 'Not Voted'; ?></span></p>
            </div>
        </div>

        <div id="boxList">
            <?php
            // Define an array of political parties and logos for the boxes
            $parties = [
                ["id" => 1, "name" => "1. Bharatiya Janata Party", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Lotus_flower_symbol.svg/75px-Lotus_flower_symbol.svg.png"],
                ["id" => 2, "name" => "2. Indian National Congress", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Hand_INC.svg/75px-Hand_INC.svg.png"],
                ["id" => 3, "name" => "3.  Aam Aadmi Party (AAP)", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/AAP_Symbol.png/75px-AAP_Symbol.png"],
                ["id" => 4, "name" => "4. Bahujan Samaj Party", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Indian_Election_Symbol_Elephant.png/75px-Indian_Election_Symbol_Elephant.png"],
                ["id" => 5, "name" => "5. Communist Party of India", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/CPIM_election_symbol.png/75px-CPIM_election_symbol.png"],
                ["id" => 6, "name" => "6. National People's Party", "logo" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Indian_Election_Symbol_Book.svg/75px-Indian_Election_Symbol_Book.svg.png"]
            ];

            // Loop through the array and create boxes for each party
            foreach ($parties as $party) {
                echo '<div class="box">';
                echo '<div class="box-content">';
                echo '<h3 class="box-name">' . $party['name'] . '</h3>';
                echo '<img src="' . $party['logo'] . '" alt="' . $party['name'] . ' Logo" class="box-logo">';
                echo '</div>';
                echo '<form action="../api/vote.php" method="POST">';
                echo '<input type="hidden" name="party_id" value="' . $party['id'] . '">';
                echo '<button type="submit" class="vote-btn" ' . ($voted ? 'disabled' : '') . '>Vote</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

</body>
</html>
