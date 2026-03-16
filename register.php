<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navbar.php";

try {
    if(isset($_POST['submit'])){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $age = (int)$_POST['age'];
        $course = trim($_POST['course']);
        $password = $_POST['password'];
        $role = (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'admin' : 'student';

        // Server-side check for letters only
        if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
            throw new Exception("Incorrect: Name must only contain letters.");
        }
        
        $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $check->execute([$email]);
        if($check->rowCount() > 0) throw new Exception("Email already exists!");

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO students (name, email, age, course, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $age, $course, $hashed_password, $role]);

        // INSTANT LOGIN after registration
        $_SESSION['student_id'] = $pdo->lastInsertId();
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['role']       = $role; 

        $_SESSION['success'] = "Welcome to the portal, " . $name . "!";
        header("Location: index.php");
        exit();
    }
} catch(Exception $ex){
    $_SESSION['error'] = $ex->getMessage();
}
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h2 style="text-align:center;"><i class="fa-solid fa-user-plus"></i> Register</h2>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <div class="field-group" style="margin-bottom: 15px;">
                <input type="text" name="name" id="regName" placeholder="Full Name (Letters Only)" required>
                <small id="name-error" class="hint error-text" style="display:none;"></small>
            </div>
            
            <input type="email" name="email" placeholder="Email Address" required>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="number" name="age" placeholder="Age" required style="width: 30%;">
                <input type="text" name="course" placeholder="Course" required style="width: 70%;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-size: 12px; color: #64748b; font-weight: 600;">Account Type:</label>
                <select name="role" class="styled-select">
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="field-group" style="position: relative; margin-bottom: 15px;">
                <input type="password" name="password" id="regPassword" placeholder="Password" required>
                <i class="fas fa-eye toggle-icon" id="toggleRegPass"></i>
                
                <div class="requirements-box">
                    <div id="req-length" class="req-item"><i class="fas fa-circle"></i> 8+ Characters</div>
                    <div id="req-upper" class="req-item"><i class="fas fa-circle"></i> 1 Uppercase</div>
                    <div id="req-symbol" class="req-item"><i class="fas fa-circle"></i> 1 Number/Symbol</div>
                </div>
            </div>

            <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
            <small id="match-hint" class="hint" style="margin-top: 5px;"></small>

            <button type="submit" name="submit" id="submitBtn" class="login-btn-styled" style="width:100%; border:none; cursor:pointer; margin-top: 20px;" disabled>
                Create Account & Login
            </button>
        </form>
    </div>
</div>

<script>
const nameInput = document.getElementById('regName');
const nameError = document.getElementById('name-error');
const password = document.getElementById('regPassword');
const confirmInput = document.getElementById('confirmPassword');
const submitBtn = document.getElementById('submitBtn');
const toggleBtn = document.getElementById('toggleRegPass');

// Toggle Password Visibility
toggleBtn.addEventListener('click', function() {
    password.type = password.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye-slash');
});

function validate() {
    const valName = nameInput.value;
    const valPass = password.value;
    const valConf = confirmInput.value;

    // 1. Letters Only Validation
    const lettersOnly = /^[a-zA-Z ]+$/;
    let isNameValid = false;

    if (valName.length > 0) {
        if (!lettersOnly.test(valName)) {
            nameError.textContent = "Incorrect: Only letters allowed.";
            nameError.style.display = "block";
            nameInput.style.borderColor = "#ef4444";
        } else if (valName.length < 4) {
            nameError.textContent = "Min 4 characters.";
            nameError.style.display = "block";
            nameInput.style.borderColor = "#fbbf24";
        } else {
            nameError.style.display = "none";
            nameInput.style.borderColor = "#10b981";
            isNameValid = true;
        }
    }

    // 2. Password Requirements Validation
    const isLength = valPass.length >= 8;
    const isUpper = /[A-Z]/.test(valPass);
    const isSymbol = /[0-9!@#$%^&*]/.test(valPass);
    
    document.getElementById('req-length').className = isLength ? 'req-item valid' : 'req-item';
    document.getElementById('req-upper').className = isUpper ? 'req-item valid' : 'req-item';
    document.getElementById('req-symbol').className = isSymbol ? 'req-item valid' : 'req-item';

    // 3. Confirm Match Validation
    let isMatch = false;
    const matchHint = document.getElementById('match-hint');
    if(valConf !== "") {
        isMatch = valPass === valConf;
        matchHint.textContent = isMatch ? "✓ Passwords match" : "✗ Passwords do not match";
        matchHint.className = isMatch ? "hint success-text" : "hint error-text";
    }

    // ACTIVATE BUTTON only if all conditions are met
    if (isNameValid && isLength && isUpper && isSymbol && isMatch) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = "1";
    } else {
        submitBtn.disabled = true;
        submitBtn.style.opacity = "0.6";
    }
}

// Attach listeners to all relevant inputs
[nameInput, password, confirmInput].forEach(el => {
    el.addEventListener('input', validate);
});
</script>

<?php include "includes/footer.php"; ?>