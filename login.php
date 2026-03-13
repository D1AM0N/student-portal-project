<?php
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";
session_start();

try {
    if(isset($_POST['submit'])){
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if(empty($email) || empty($password)){
            throw new Exception("Email and password are required!");
        }

        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if(!$user || !password_verify($password,$user['password'])){
            throw new Exception("Invalid email or password!");
        }

        // Set session
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        $_SESSION['success'] = "Welcome back, ".$user['name']."!";
        header("Location: dashboard.php");
        exit();
    }
} catch(Exception $ex){
    $_SESSION['error'] = $ex->getMessage();
}
?>

<div class="container">
    <div class="card">
        <h2>Login</h2>

        <?php
        if(isset($_SESSION['success'])){
            echo "<div class='success'>".$_SESSION['success']."</div>"; unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
            echo "<div class='error'>".$_SESSION['error']."</div>"; unset($_SESSION['error']);
        }
        ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit">Login</button>
        </form>
    </div>
</div>