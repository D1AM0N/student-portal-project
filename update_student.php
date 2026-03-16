<?php
session_start();
include "includes/db.php";

// 1. Basic Security Gate
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "Access denied. Please login.";
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit'])){
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $age = (int) $_POST['age'];
    $course = trim($_POST['course']);
    
    // Captured role only if provided (typically hidden or admin-only field)
    $new_role = isset($_POST['role']) ? $_POST['role'] : null;

    // 2. Permission Check: Admin OR Owner
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $isOwner = (int)$_SESSION['student_id'] === $id;

    if(!$isAdmin && !$isOwner){
        $_SESSION['error'] = "Unauthorized action!";
        header("Location: students.php");
        exit();
    }

    // 3. Validation Logic (Consistent with Register/Login)
    try {
        if(empty($name) || empty($email) || empty($age) || empty($course)){
            throw new Exception("All fields are required!");
        }

        // LETTERS-ONLY Requirement (The part we added to Register)
        if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
            throw new Exception("Incorrect: Name must only contain letters.");
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception("Invalid email format!");
        }

        // 4. Update the Database
        // We use a flexible query in case an Admin is changing the role too
        if($isAdmin && $new_role){
            $sql = "UPDATE students SET name=?, email=?, age=?, course=?, role=? WHERE id=?";
            $params = [$name, $email, $age, $course, $new_role, $id];
        } else {
            $sql = "UPDATE students SET name=?, email=?, age=?, course=? WHERE id=?";
            $params = [$name, $email, $age, $course, $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // 5. UPDATE CURRENT SESSION (If user updated themselves)
        // This ensures the navbar name updates without logging out
        if($isOwner){
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            if($isAdmin && $new_role) $_SESSION['role'] = $new_role;
        }

        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: students.php");
        exit();

    } catch(Exception $e){
        $_SESSION['error'] = $e->getMessage();
        header("Location: edit.student.php?id=$id");
        exit();
    }

} else {
    header("Location: students.php");
    exit();
}