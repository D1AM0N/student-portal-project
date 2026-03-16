<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navbar.php";

// 1. Basic Login Check
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "Please login to continue.";
    header("Location: login.php");
    exit();
}

// 2. Validate GET ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    $_SESSION['error'] = "Invalid access.";
    header("Location: students.php");
    exit();
}

$student_id_to_edit = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id_to_edit]);
    $student = $stmt->fetch();

    if(!$student){
        $_SESSION['error'] = "Student not found.";
        header("Location: students.php");
        exit();
    }

    // 3. Permission Check (Admin OR the Owner)
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $isOwner = (int)$_SESSION['student_id'] === $student_id_to_edit;

    if(!$isAdmin && !$isOwner){
        $_SESSION['error'] = "Unauthorized: You can only edit your own profile.";
        header("Location: students.php");
        exit();
    }

} catch(PDOException $e){
    $_SESSION['error'] = "Database Error.";
    header("Location: students.php");
    exit();
}
?>

<div class="container">
    <div class="card" style="max-width:500px; margin: 40px auto;">
        <h2 style="text-align:center; margin-bottom:25px;">
            <i class="fas fa-user-edit"></i> Edit Profile
        </h2>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="update_student.php" id="editForm">
            <input type="hidden" name="id" value="<?= $student['id'] ?>">

            <div class="field-group" style="margin-bottom: 15px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Full Name</label>
                <input type="text" name="name" id="editName" value="<?= htmlspecialchars($student['name']) ?>" required>
                <small id="name-error" class="hint error-text" style="display:none;"></small>
            </div>

            <div class="field-group" style="margin-bottom: 15px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label style="font-size: 13px; color: #64748b; font-weight: 600;">Age</label>
                    <input type="number" name="age" value="<?= $student['age'] ?>" required>
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 13px; color: #64748b; font-weight: 600;">Course</label>
                    <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>
                </div>
            </div>

            <?php if($isAdmin): ?>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 13px; color: #64748b; font-weight: 600;">Account Role</label>
                <select name="role" class="styled-select">
                    <option value="student" <?= ($student['role'] == 'student') ? 'selected' : '' ?>>Student</option>
                    <option value="admin" <?= ($student['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <?php endif; ?>

            <button type="submit" name="submit" id="submitBtn" class="login-btn-styled" style="width:100%; border:none; cursor:pointer; padding: 14px;">
                Save Changes
            </button>
            
            <a href="students.php" style="display:block; text-align:center; margin-top:15px; font-size:13px; color:#64748b; text-decoration:none;">Cancel</a>
        </form>
    </div>
</div>

<script>
const nameInput = document.getElementById('editName');
const nameError = document.getElementById('name-error');
const submitBtn = document.getElementById('submitBtn');

nameInput.addEventListener('input', function() {
    const lettersOnly = /^[a-zA-Z ]+$/;
    const val = this.value;

    if (val.length > 0 && !lettersOnly.test(val)) {
        nameError.textContent = "Incorrect: Only letters are allowed.";
        nameError.style.display = "block";
        this.style.borderColor = "#ef4444";
        submitBtn.disabled = true;
    } else if (val.length > 0 && val.length < 4)