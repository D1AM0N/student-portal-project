<?php
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";
session_start();

try {
    if(isset($_POST['submit'])){
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $age = (int)$_POST['age'];
        $course = trim($_POST['course']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = isset($_POST['role']) && $_POST['role'] === 'admin' ? 'admin' : 'student';

        if(empty($name) || empty($email) || empty($age) || empty($course) || empty($password)){
            throw new Exception("All fields are required!");
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception("Invalid email format!");
        }

        if($password !== $confirm_password){
            throw new Exception("Passwords do not match!");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO students (name,email,age,course,password,role) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$name,$email,$age,$course,$hashed_password,$role]);

        $_SESSION['success'] = ucfirst($role)." registered successfully!";
        header("Location: login.php");
        exit();
    }
} catch(Exception $ex){
    $_SESSION['error'] = $ex->getMessage();
}
?>

<div class="container">
    <div class="card">
        <h2>Register</h2>

        <?php
        if(isset($_SESSION['success'])){
            echo "<div class='success'>".$_SESSION['success']."</div>"; unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
            echo "<div class='error'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
        }
        ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="number" name="age" placeholder="Age" required>
            <input type="text" name="course" placeholder="Course" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <label>Role:</label>
            <select name="role">
                <option value="student">Student</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="submit">Register</button>
        </form>
    </div>
</div>