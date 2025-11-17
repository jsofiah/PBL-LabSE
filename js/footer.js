function loadFooter() {
    fetch('footer.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
}

document.addEventListener('DOMContentLoaded', loadFooter);