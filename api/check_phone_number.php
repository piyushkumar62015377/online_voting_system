<?php
// Include database connection
include('db_connect.php'); // Ensure you replace this with the actual database connection file

if (isset($_POST['mobile'])) {
    $mobile = $_POST['mobile'];

    // Query to check if the phone number already exists
    $query = "SELECT * FROM users WHERE mobile = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "exists"; // Phone number already exists
    } else {
        echo "available"; // Phone number is available
    }
}
?>
