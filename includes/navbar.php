<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <a href="index.php"><i class="fas fa-graduation-cap"></i> Portal</a>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="students.php">Students</a></li>
        </ul>

        <div class="user-controls">
            <button class="icon-btn" onclick="copyUrl()"><i class="fas fa-share-alt"></i></button>

            <div class="kebab-wrapper">
                <button class="icon-btn" id="userTrigger"><i class="fas fa-ellipsis-v"></i></button>
                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <?php if(isset($_SESSION['student_id'])): ?>
                            <div class="user-meta">
                                <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                                <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
                                <span class="role-badge <?= ($_SESSION['role'] === 'admin') ? 'admin-badge' : '' ?>">
                                    <?= ucfirst($_SESSION['role'] ?? 'Student') ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <strong>Guest User</strong>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-links">
                        <?php if(isset($_SESSION['student_id'])): ?>
                            <a href="index.php"><i class="fas fa-th-large"></i> Dashboard</a>
                            <a href="edit.student.php?id=<?= $_SESSION['student_id'] ?>"><i class="fas fa-user"></i> Profile</a>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                                <hr><a href="admin_panel.php" class="admin-panel-link"><i class="fas fa-user-shield"></i> Admin Panel</a>
                            <?php endif; ?>
                            <hr><a href="logout.php" class="logout-link"><i class="fas fa-power-off"></i> Sign Out</a>
                        <?php else: ?>
                            <a href="login.php" class="login-btn-styled">Login</a>
                            <a href="register.php" class="register-btn-styled">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="avatar-circle">
                <?php if(isset($_SESSION['student_id'])): ?>
                    <?= strtoupper($_SESSION['user_name'][0]) ?>
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
function copyUrl() { 
    navigator.clipboard.writeText(window.location.href); 
    alert("Copied!"); 
}
</script>
<script src="js/script1.js"></script>