document.addEventListener('DOMContentLoaded', function() {
    showSection('profile'); // Show the default section
});

function showSection(sectionId) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.style.display = 'none'; 
        if (section.id === sectionId) {
            section.style.display = 'block';
        }
    });
}

function logout() {
    if (confirm('Are you sure you want to log out?')) {
        window.location.href = 'logout.php';  // Redirect to logout.php
    }
}
function showSection(id) {
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.style.display = 'none');

    const selected = document.getElementById(id);
    if (selected) {
        selected.style.display = 'block';
    }
}

// Show profile section by default
window.onload = () => showSection('profile');
