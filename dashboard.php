<?php
session_start();
include "includes/db.php"; 
include "includes/header.php";
include "includes/navbar.php";

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}
?>

<div class="container">
    <?php include "includes/flash.php"; ?>
    <div class="card">
        <h2>Welcome to your Dashboard</h2>
        <div class="dashboard-grid">
            <div class="card">
                <h3>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h3>
                <p>Status: <strong><?= ucfirst($_SESSION['role']) ?></strong></p>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>