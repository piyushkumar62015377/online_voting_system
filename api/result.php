<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voting_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get all parties and vote count
function getVoteCounts() {
    global $conn;
    $sql = "SELECT parties.party_id, parties.party_name, parties.logo, COUNT(votes.party_id) AS vote_count
            FROM parties
            LEFT JOIN votes ON parties.party_id = votes.party_id
            GROUP BY parties.party_id";
    $result = $conn->query($sql);
    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Results</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('https://png.pngtree.com/background/20210710/original/pngtree-india-flag-vector-background-picture-image_990763.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.8); /* Slight opacity for better contrast */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        /* Header Section with Indian Flag Colors */
        #headerSection {
            background: linear-gradient(to right, #FF9933, #FFFFFF, #138808); /* Indian flag colors */
            padding: 20px;
            text-align: center;
            color: #000080; /* Navy Blue for text */
            font-size: 2em;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
        }

        .results-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns layout */
            gap: 20px;
            margin-top: 20px;
        }

        .result-box {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .result-box:hover {
            background-color: #e9ecef;
        }

        .result-party-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-bottom: 10px;
        }

        .result-logo img {
            width: 100px;
            height: 100px;
            object-fit: contain; /* Ensures logo doesn't get distorted */
            margin-bottom: 20px;
        }

        .result-votes {
            font-size: 1.2em;
            color: #495057;
            text-align: center;
        }

        .no-votes {
            text-align: center;
            font-size: 1.2em;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div id="headerSection">
        Election Results
    </div>

    <div class="results-section">
        <?php
        // Fetch the vote counts for each party
        $voteCounts = getVoteCounts();
        if ($voteCounts->num_rows > 0) {
            while ($row = $voteCounts->fetch_assoc()) {
                // Determine the party logo URL
                $partyLogoUrl = match ($row['party_name']) {
                    'Bharatiya Janata Party' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Lotus_flower_symbol.svg/75px-Lotus_flower_symbol.svg.png',
                    'Indian National Congress' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Hand_INC.svg/75px-Hand_INC.svg.png',
                    'Aam Aadmi Party' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/AAP_Symbol.png/75px-AAP_Symbol.png',
                    'Bahujan Samaj Party' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Indian_Election_Symbol_Elephant.png/75px-Indian_Election_Symbol_Elephant.png',
                    'Communist Party of India' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/CPIM_election_symbol.png/75px-CPIM_election_symbol.png',
                    'National People\'s Party' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Indian_Election_Symbol_Book.svg/75px-Indian_Election_Symbol_Book.svg.png',
                    default => '../uploads/' . htmlspecialchars($row['logo'])
                };

                echo '
                    <div class="result-box">
                        <div class="result-party-name">' . htmlspecialchars($row['party_name']) . '</div>
                        <div class="result-logo">
                            <img src="' . $partyLogoUrl . '" alt="' . htmlspecialchars($row['party_name']) . ' Logo" onerror="this.onerror=null;this.src=\'https://via.placeholder.com/80\';">
                        </div>
                        <div class="result-votes">Votes: ' . htmlspecialchars($row['vote_count']) . '</div>
                    </div>';
            }
        } else {
            echo '<p class="no-votes">No votes yet!</p>';
        }

        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
