<?php

$servername = "localhost";
$username = "root";
$password = "";  
$dbname = "voting_system";

session_start();


$connect = new mysqli($servername, $username, $password, $dbname);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}


$mobile = $_POST['mobile'];
$password = $_POST['password'];
$role = $_POST['role'];


$stmt = $connect->prepare("SELECT * FROM users WHERE mobile = ? AND role = ?");
$stmt->bind_param("ss", $mobile, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userdata = $result->fetch_assoc();

    if (password_verify($password, $userdata['password'])) {
       
        $groups = $connect->query("SELECT * FROM users WHERE role=2");
        $groupsdata = $groups->fetch_all(MYSQLI_ASSOC);

        
        $_SESSION['userdata'] = $userdata;
        $_SESSION['groupsdata'] = $groupsdata;

        echo '
        <script>
            window.location = "../routes/dashboard.php";
        </script>
        ';
    } else {
        echo '
        <script>
            alert("Invalid Credentials!");
            window.location = "../";
        </script>
        ';
    }
} else {
    echo '
    <script>
        alert("Invalid Mobile or Password!");
        window.location = "../";
    </script>
    ';
}

$stmt->close();
$connect->close();
?>
