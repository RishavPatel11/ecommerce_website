<?php
// Start the session to access session variables
session_start();

// Destroy all session data to log out the user
session_destroy();

// Redirect to the home page (index.php) after logout
header('Location: index.php');

// Ensure no further code is executed after redirect
exit();
?>