// main.js

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Flash messages auto-fade ---
    const flashes = document.querySelectorAll('.flash');
    flashes.forEach(flash => {
        setTimeout(() => {
            flash.style.transition = "opacity 0.5s";
            flash.style.opacity = 0;
            setTimeout(() => flash.remove(), 500);
        }, 3000);
    });

    // --- 2. Live search for student table ---
    const searchInput = document.querySelector('#search');
    if(searchInput) {
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            document.querySelectorAll('#studentTable tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // --- 3. Custom delete confirmation ---
    const deleteLinks = document.querySelectorAll('.btn-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', e => {
            const nameCell = link.closest('tr').querySelector('td:nth-child(2)');
            const name = nameCell ? nameCell.innerText : 'this student';
            if(!confirm(`Are you sure you want to delete student: ${name}?`)) {
                e.preventDefault(); // cancel deletion
            }
        });
    });

});