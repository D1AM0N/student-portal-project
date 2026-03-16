<div class="flash-container" style="width: 90%; max-width: 1100px; margin: 10px auto;">
    <?php if(isset($_SESSION['success'])): ?>
        <div class="success flash-msg">
            <i class="fas fa-check-circle"></i> 
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="error flash-msg">
            <i class="fas fa-exclamation-triangle"></i> 
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
</div>