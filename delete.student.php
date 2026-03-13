<?php
session_start();
include "includes/db.php";

// Check login
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "You must be logged in!";
    header("Location: login.php");
    exit();
}

// Validate ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    $_SESSION['error'] = "Invalid request.";
    header("Location: students.php");
    exit();
}

$student_id_to_delete = (int)$_GET['id'];

try {
    // Check if student exists
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id_to_delete]);
    $student = $stmt->fetch();

    if(!$student){
        $_SESSION['error'] = "Student not found.";
        header("Location: students.php");
        exit();
    }

    // Permission check: admin can delete anyone, student can delete self
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $isOwner = $_SESSION['student_id'] === $student_id_to_delete;

    if(!$isAdmin && !$isOwner){
        $_SESSION['error'] = "You cannot delete another user's account!";
        header("Location: students.php");
        exit();
    }

    // Delete student
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$student_id_to_delete]);

    $_SESSION['success'] = "Student deleted successfully.";

    // If student deleted themselves, end session
    if($isOwner){
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        header("Location: students.php");
        exit();
    }

} catch(PDOException $e){
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: students.php");
    exit();
}
?>