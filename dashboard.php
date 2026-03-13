<?php
session_start();
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";

// Redirect if not logged in
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "You must be logged in!";
    header("Location: login.php");
    exit();
}

// Get logged-in user info from session
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];
$userRole = $_SESSION['role'];

// Fetch total students from database
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
    $row = $stmt->fetch();
    $totalStudents = $row ? $row['total'] : 0;
} catch(PDOException $e){
    $totalStudents = 0;
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="container">

<h2 style="margin-bottom:25px;">Dashboard</h2>

<div class="dashboard-grid">

    <div class="card">
        <h3>Welcome</h3>
        <p>Welcome, <?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($userRole) ?>)</p>
    </div>

    <div class="card">
        <h3>Total Students</h3>
        <p style="font-size:28px; font-weight:bold;"><?= $totalStudents ?></p>
    </div>

    <div class="card">
        <h3>Your Email</h3>
        <p><?= htmlspecialchars($userEmail) ?></p>
    </div>

</div>

<div class="card" style="margin-top:30px;">
    <h3>Quick Actions</h3>
    <a href="students.php" class="btn-edit">View Students</a>
    <?php if($userRole === 'admin'): ?>
        <a href="reg.php" class="btn-edit" style="margin-left:10px;">Register Student</a>
    <?php endif; ?>
    <a href="logout.php" class="btn-delete" style="margin-left:10px;">Logout</a>
</div>

<?php
// Display database error if any
if(isset($error)){
    echo "<div class='error' style='margin-top:15px;'>$error</div>";
}
?>

</div>

<?php include "includes/footer.php"; ?>