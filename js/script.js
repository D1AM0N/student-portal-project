document.addEventListener('DOMContentLoaded', function() {
    // 1. KEBAB DROPDOWN LOGIC
    const userTrigger = document.getElementById('userTrigger');
    const userDropdown = document.getElementById('userDropdown');

    if (userTrigger && userDropdown) {
        userTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !userTrigger.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // 2. AUTO-HIDE FLASH MESSAGES
    const flashMessages = document.querySelectorAll('.flash-msg');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            msg.style.transition = '0.5s ease';
            setTimeout(() => msg.remove(), 500);
        }, 4000); // Hides after 4 seconds
    });
});

// 3. COPY URL FUNCTION
function copyUrl() {
    const el = document.createElement('textarea');
    el.value = window.location.href;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    
    // Alert replacement (more modern)
    const toast = document.createElement('div');
    toast.innerHTML = "Link Copied!";
    toast.style = "position:fixed; bottom:20px; right:20px; background:#1e293b; color:white; padding:10px 20px; border-radius:8px; z-index:100000;";
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}