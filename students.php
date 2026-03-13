<?php
session_start();
include "includes/header.php";
include "includes/navbar.php";
include "includes/db.php";

if(!isset($_SESSION['student_id'])){
    $_SESSION['error'] = "You must be logged in!";
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    $students = $stmt->fetchAll();
} catch(PDOException $e){
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    $students = [];
}
?>

<div class="container">

<!-- Flash messages -->
<?php
if(isset($_SESSION['success'])){ echo "<div class='success'>".$_SESSION['success']."</div>"; unset($_SESSION['success']); }
if(isset($_SESSION['error'])){ echo "<div class='error'>".$_SESSION['error']."</div>"; unset($_SESSION['error']); }
?>

<div class="card">
    <h2>Students List</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Age</th><th>Course</th><th>Date Registered</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if($students): ?>
                <?php foreach($students as $student): ?>
                    <tr>
                        <td><?= $student['id'] ?></td>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= $student['age'] ?></td>
                        <td><?= htmlspecialchars($student['course']) ?></td>
                        <td><?= $student['created_at'] ?></td>
                        <td>
                            <?php
                            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
                            $isOwner = $student['id'] == $_SESSION['student_id'];

                            if($isAdmin || $isOwner): ?>
                                <a href="edit.student.php?id=<?= $student['id'] ?>" class="btn-edit">Edit</a>
                                <a href="delete.student.php?id=<?= $student['id'] ?>" class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this student?')">
                                   Delete
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>
<?php include "includes/footer.php"; ?>