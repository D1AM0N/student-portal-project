<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/navbar.php";

// 1. Security Check
if (!isset($_SESSION['student_id'])) {
    $_SESSION['error'] = "Please login to continue.";
    header("Location: login.php");
    exit();
}

// 2. Email Masking Helper
function maskEmail($email) {
    $parts = explode("@", $email);
    $name = $parts[0];
    $domain = $parts[1];
    // Shows first letter + asterisks + domain
    return substr($name, 0, 1) . str_repeat("*", 5) . "@" . $domain;
}

try {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY name ASC");
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error");
}
?>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom: 20px;"><i class="fas fa-user-shield"></i> Student Management</h2>

        <div style="overflow-x: auto;">
            <table class="styled-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['name']) ?></strong>
                                <?php if($row['role'] === 'admin'): ?>
                                    <span class="admin-badge">Admin</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php 
                                // --- PRIVACY LOGIC ---
                                if ($_SESSION['role'] === 'admin') {
                                    // Admins see everything
                                    echo htmlspecialchars($row['email']);
                                } else {
                                    // Students see masked emails of others
                                    // (Unless it's their own email)
                                    if ($_SESSION['student_id'] == $row['id']) {
                                        echo htmlspecialchars($row['email']);
                                    } else {
                                        echo htmlspecialchars(maskEmail($row['email']));
                                    }
                                }
                                ?>
                            </td>

                            <td><?= htmlspecialchars($row['course']) ?></td>

                            <td style="text-align: center;">
                                <?php if($_SESSION['role'] === 'admin' || $_SESSION['student_id'] == $row['id']): ?>
                                    <a href="edit.student.php?id=<?= $row['id'] ?>" class="btn-edit" style="margin-right:10px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php 
                                // --- DELETE POWER LOGIC ---
                                // ONLY Admins get the delete button
                                if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="delete_student.php?id=<?= $row['id'] ?>" 
                                       style="color: #ef4444;" 
                                       onclick="return confirm('WARNING: Are you sure you want to delete this student?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <i class="fas fa-lock" style="color: #cbd5e1;" title="Only Admins can delete"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>