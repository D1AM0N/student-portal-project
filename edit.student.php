<?php
session_start();
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";

// Check login
if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "You must be logged in!";
    header("Location: login.php");
    exit();
}

// Validate ID from GET
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    $_SESSION['error'] = "Invalid student ID.";
    header("Location: students.php");
    exit();
}

$student_id_to_edit = (int)$_GET['id'];

try {
    // Fetch student from DB
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id_to_edit]);
    $student = $stmt->fetch();

    if(!$student){
        $_SESSION['error'] = "Student not found.";
        header("Location: students.php");
        exit();
    }

    // Permission check
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $isOwner = $_SESSION['student_id'] === $student_id_to_edit;

    if(!$isAdmin && !$isOwner){
        $_SESSION['error'] = "You cannot edit another user's account!";
        header("Location: students.php");
        exit();
    }

} catch(PDOException $e){
    $_SESSION['error'] = "Database error: ".$e->getMessage();
    header("Location: students.php");
    exit();
}
?>

<div class="container">
    <div class="card" style="max-width:500px; margin:auto;">
        <h2 style="text-align:center; margin-bottom:25px;">Edit Student</h2>

        <!-- Flash messages -->
        <?php
        if(isset($_SESSION['success'])){
            echo "<div class='success'>".$_SESSION['success']."</div>";
            unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
            echo "<div class='error'>".$_SESSION['error']."</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form method="POST" action="update_student.php">
            <input type="hidden" name="id" value="<?= $student['id'] ?>">

            <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            <input type="number" name="age" value="<?= $student['age'] ?>" required>
            <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required>

            <button type="submit" name="submit">Update Student</button>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>