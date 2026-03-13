<?php
session_start();
session_unset();
session_destroy();

// Optional: flash message for logout
session_start();
$_SESSION['success'] = "You have successfully logged out.";
header("Location: login.php");
exit();