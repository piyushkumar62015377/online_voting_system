<?php

session_start();


if (!isset($_SESSION['userdata'])) {
   
    echo "<script>
            alert('You are not logged in!');
            window.location = '../index.html';  // Redirect to the login page
          </script>";
    exit();
}


session_unset();


session_destroy();


echo "<script>
        alert('You have logged out successfully!');
        window.location = '../index.html';  // Redirect to the login page
      </script>";
exit();
?>
