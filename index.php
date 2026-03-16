<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navbar.php";

// Fetch Stats ONLY if logged in
$isLoggedIn = isset($_SESSION['student_id']);
$countTotal = $countAdmins = $countStudents = 0;

if ($isLoggedIn) {
    try {
        $countTotal = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $countAdmins = $pdo->query("SELECT COUNT(*) FROM students WHERE role = 'admin'")->fetchColumn();
        $countStudents = $countTotal - $countAdmins;
    } catch (PDOException $e) {
        // Handle error silently
    }
}
?>

<div class="container">
    <div class="flash-container">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="success flash-msg">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error flash-msg">
                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$isLoggedIn): ?>
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-lock" style="font-size: 50px; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h1 style="font-size: 28px;">Portal Access Restricted</h1>
            <p style="color: #64748b; margin: 15px auto; max-width: 400px;">
                Please log in or create an account to view the student directory and system statistics.
            </p>
            <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
                <a href="login.php" class="login-btn-styled" style="text-decoration: none; padding: 12px 30px;">Login</a>
                <a href="register.php" class="register-btn-styled" style="text-decoration: none; padding: 12px 30px;">Register</a>
            </div>
        </div>

    <?php else: ?>
        <div class="card" style="margin-bottom: 30px; border-left: 6px solid #3b82f6;">
            <h1>Welcome Back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
            <p style="color: #64748b; margin-top: 5px;">You are currently logged in as a <strong><?= ucfirst($_SESSION['role']) ?></strong>.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div class="card" style="text-align: center;">
                <i class="fas fa-users" style="font-size: 2rem; color: #3b82f6; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px;"><?= $countTotal ?></h3>
                <p style="color: #64748b; font-size: 14px;">Total Registered</p>
            </div>

            <div class="card" style="text-align: center;">
                <i class="fas fa-user-graduate" style="font-size: 2rem; color: #10b981; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px;"><?= $countStudents ?></h3>
                <p style="color: #64748b; font-size: 14px;">Active Students</p>
            </div>

            <div class="card" style="text-align: center;">
                <i class="fas fa-user-shield" style="font-size: 2rem; color: #fbbf24; margin-bottom: 10px;"></i>
                <h3 style="font-size: 24px;"><?= $countAdmins ?></h3>
                <p style="color: #64748b; font-size: 14px;">System Admins</p>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
            <a href="students.php" class="login-btn-styled" style="text-decoration: none; padding: 12px 25px;">
                <i class="fas fa-list"></i> View Student List
            </a>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="register.php" class="register-btn-styled" style="text-decoration: none; padding: 12px 25px;">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>