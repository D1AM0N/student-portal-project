<?php
// 1. Session must be at the very top
if (session_status() === PHP_SESSION_NONE) session_start();

// 2. Includes - Ensure these paths are correct for your folder structure
include "includes/db.php";
include "includes/header.php";
include "includes/navbar.php";

$error_msg = "";

try {
    if(isset($_POST['submit'])){
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if(empty($email) || empty($password)){
            throw new Exception("Please fill in all fields.");
        }

        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify user and password
        if($user && password_verify($password, $user['password'])){
            
            // --- ADMIN & SESSION CAPTURE ---
            $_SESSION['student_id'] = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Ensure 'role' exists in your DB table; defaults to 'student' if empty
            $_SESSION['role'] = !empty($user['role']) ? $user['role'] : 'student';

            $_SESSION['success'] = "Logged in as " . ucfirst($_SESSION['role']);
            header("Location: dashboard.php");
            exit();
        } else {
            throw new Exception("Invalid email or password!");
        }
    }
} catch(Exception $ex){
    $error_msg = $ex->getMessage();
}
?>

<div class="container">
    <div class="card" style="max-width: 450px; margin: 60px auto;">
        <h2 style="text-align: center; margin-bottom: 25px;">
            <i class="fa-solid fa-shield-halved"></i> Login Portal
        </h2>

        <?php if(!empty($error_msg)): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="field-wrapper" style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Email Address</label>
                <input type="email" name="email" id="loginEmail" placeholder="name@example.com" required>
                <small id="email-status" class="hint" style="margin-top: 5px;"></small>
            </div>
            
            <div class="field-wrapper" style="margin-bottom: 25px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="loginPassword" placeholder="••••••••" required style="padding-right: 45px;">
                    <i class="fas fa-eye toggle-icon" id="toggleLoginPass"></i>
                </div>
                <small id="pass-status" class="hint" style="margin-top: 5px;"></small>
            </div>

            <button type="submit" name="submit" id="loginBtn" class="login-btn-styled" style="width: 100%; border: none; cursor: pointer; padding: 14px; font-weight: 600;">
                Sign In to Portal
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 25px; font-size: 13px; color: #64748b;">
            New to the portal? <a href="register.php" style="color: #2563eb; font-weight: 600; text-decoration: none;">Create an Account</a>
        </p>
    </div>
</div>

<script>
// Logic for Eye Toggle and Live Feedback
const emailInput = document.getElementById('loginEmail');
const emailStatus = document.getElementById('email-status');
const passInput = document.getElementById('loginPassword');
const passStatus = document.getElementById('pass-status');
const toggleBtn = document.getElementById('toggleLoginPass');

// 1. Toggle Password Visibility
toggleBtn.addEventListener('click', function() {
    const isPass = passInput.type === 'password';
    passInput.type = isPass ? 'text' : 'password';
    this.classList.toggle('fa-eye-slash');
});

// 2. Live Email Format Check (No Reload)
emailInput.addEventListener('input', function() {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (this.value === "") {
        emailStatus.innerText = "";
        this.style.borderColor = "#ddd";
    } else if (pattern.test(this.value)) {
        emailStatus.innerText = "✓ Valid format";
        emailStatus.className = "hint success-text";
        this.style.borderColor = "#10b981";
    } else {
        emailStatus.innerText = "✗ Enter a valid email";
        emailStatus.className = "hint error-text";
        this.style.borderColor = "#ef4444";
    }
});

// 3. Simple Password Length Check
passInput.addEventListener('input', function() {
    if (this.value.length >= 8) {
        passStatus.innerText = "✓ Sufficient length";
        passStatus.className = "hint success-text";
        this.style.borderColor = "#10b981";
    } else if (this.value.length > 0) {
        passStatus.innerText = "! Minimum 8 characters";
        passStatus.className = "hint error-text";
        this.style.borderColor = "#f59e0b";
    }
});
</script>

<?php include "includes/footer.php"; ?>