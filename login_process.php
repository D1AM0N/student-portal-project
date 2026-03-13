<?php
session_start();
include 'includes/db.php';

if(isset($_POST['email'], $_POST['password'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $student = $stmt->fetch();

        if($student && password_verify($password, $student['password'])){
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['role'] = $student['role']; // 'admin' or 'student'
        $_SESSION['name'] = $student['name']; // optional, for dashboard display
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password!";
            header("Location: login.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}
?>