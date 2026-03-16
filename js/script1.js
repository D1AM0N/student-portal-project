document.addEventListener("DOMContentLoaded", function() {
    const trigger = document.getElementById("userTrigger");
    const dropdown = document.getElementById("userDropdown");

    if(trigger && dropdown) {
        trigger.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdown.classList.toggle("active");
        });

        document.addEventListener("click", function(e) {
            if(!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove("active");
            }
        });
    }
});