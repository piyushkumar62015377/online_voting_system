<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";  // Replace with your MySQL password if necessary
$dbname = "voting_system";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $aadhaar_number = mysqli_real_escape_string($conn, $_POST['aadhaar_number']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $eci_id = mysqli_real_escape_string($conn, $_POST['eci']);
    $status = 1;  // Default active status
    $votes = 0;   // Default vote count
    $vote_status = 0; // Has not voted

    // Mobile number validation (server-side)
    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        echo "<script>
                alert('Please enter a valid mobile number (10 digits).');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Check if the phone number already exists
    $query = "SELECT * FROM users WHERE mobile = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('This phone number is already registered. Please use a different number.');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }


    // Aadhaar number validation
    if (!preg_match("/^[2-9]{1}[0-9]{11}$/", $aadhaar_number)) {
        echo "<script>
                alert('Please enter a valid Aadhaar number (12 digits, must not start with 0 or 1).');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Check if the Aadhaar number already exists
    $query = "SELECT * FROM users WHERE aadhaar_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $aadhaar_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('This Aadhaar number is already registered. Please use a different number.');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Password validation
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Password and Confirm Password do not match!');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // File upload validation: Check if the file is an image
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = pathinfo($image, PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_extension), $allowed_extensions)) {
        echo "<script>
                alert('Please upload a valid image file (JPG, PNG, GIF).');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Limit file size (e.g., 5MB max)
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        echo "<script>
                alert('File size exceeds the 5MB limit!');
                window.location = '../routes/register.html'; 
              </script>";
        exit();
    }

    // Handle file upload
    $upload_dir = "../uploads/";
    $upload_path = $upload_dir . basename($image);

    if (move_uploaded_file($tmp_name, $upload_path)) {
        // Insert user data into the database
        $sql = "INSERT INTO users (name, mobile, password, address, image, role, state, city, aadhaar_number, status, votes, dob, father_name, gender, eci_id, vote_status) 
                VALUES ('$name', '$mobile', '$hashed_password', '$address', '$image', '$role', '$state', '$city', '$aadhaar_number', '$status', '$votes', '$dob', '$father_name', '$gender', '$eci_id', '$vote_status')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Registration Successful!');
                    window.location = '../index.html'; 
                  </script>";
        } else {
            // Log error for debugging
            error_log("SQL Error: " . $conn->error . " | Query: " . $sql);
            echo "<script>
                    alert('Error: " . addslashes($conn->error) . "');
                    window.location = '../routes/register.html'; 
                  </script>";
        }
    } else {
        // Log upload error for debugging
        error_log("Image Upload Error: Failed to move file from $tmp_name to $upload_path");
        echo "<script>
                alert('Failed to upload image: Check file permissions or path!');
                window.location = '../routes/register.html'; 
              </script>";
    }

    // Close the connection
    $conn->close();
}
?>
