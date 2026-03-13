<?php
session_start();
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";
?>

<div class="container">

<div class="card">

<h1>Welcome to Student Portal</h1>

<p>
This system allows students and admins to manage student profiles.
</p>

<?php
// Show logged-in info
if(isset($_SESSION['user_name'])){
    echo "<p style='margin-top:15px;'>Logged in as <strong>".htmlspecialchars($_SESSION['user_name'])."</strong> (".htmlspecialchars($_SESSION['role']).")</p>";
}

// Flash messages
if(isset($_SESSION['success'])){
    echo "<div class='success'>".$_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
    echo "<div class='error'>".$_SESSION['error']."</div>";
    unset($_SESSION['error']);
}
?>

</div>

</div>

<?php include "includes/footer.php"; ?>