<?php
session_start();
include "includes/db.php";

// Only logged-in users can update
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "You must be logged in!";
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit'])){
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $age = (int) $_POST['age'];
    $course = trim($_POST['course']);

    // Validate
    if(empty($name) || empty($email) || empty($age) || empty($course)){
        $_SESSION['error'] = "All fields are required!";
        header("Location: edit.student.php?id=$id");
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Invalid email!";
        header("Location: edit.student.php?id=$id");
        exit();
    }

    // Permission check: admin or self
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $isOwner = $_SESSION['student_id'] === $id;

    if(!$isAdmin && !$isOwner){
        $_SESSION['error'] = "You cannot update another user's info!";
        header("Location: students.php");
        exit();
    }

    // Update DB
    try {
        $stmt = $pdo->prepare("UPDATE students SET name=?, email=?, age=?, course=? WHERE id=?");
        $stmt->execute([$name, $email, $age, $course, $id]);

        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: students.php");
        exit();
    } catch(PDOException $e){
        $_SESSION['error'] = "Database error: ".$e->getMessage();
        header("Location: edit.student.php?id=$id");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: students.php");
    exit();
}
?>