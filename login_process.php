<?php
session_start();
include "includes/db.php"; // Crucial fix: include your DB connection

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])){
            // Standardizing sessions across the whole site
            $_SESSION['student_id'] = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['profile_pic']= $user['profile_pic'] ?? ''; // Placeholder for image

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: login.php");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "DB Error: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}