document.querySelectorAll('.dropbtn').forEach(button => {
    button.addEventListener('click', function (e) {
    e.preventDefault();

    const dropdown = this.nextElementSibling;
    dropdown.classList.toggle('show');
    document.querySelectorAll('.dropdown-content').forEach(dc => {
        if (dc !== dropdown) dc.classList.remove('show');});
    });
});

window.addEventListener('click', function(e) {
    if (!e.target.matches('.dropbtn') && !e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-content').forEach(dc => {
            dc.classList.remove('show');
        });
    }
});

