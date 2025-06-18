// app.js
document.addEventListener("DOMContentLoaded", function () {
    // Navigation links
    const links = document.querySelectorAll('.sidebar a');
    
    // Content Area
    const contentArea = document.getElementById('content-area');
    
    // Function to load page content dynamically
    function loadContent(page) {
        fetch(`${page}.html`)
            .then(response => response.text())
            .then(data => {
                contentArea.innerHTML = data;
            })
            .catch(error => console.error('Error loading content:', error));
    }

    // Event listener for sidebar links
    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const page = link.getAttribute('href').split('.')[0]; // Get the page name without extension
            loadContent(page);
        });
    });
    
    // Load the default content (Manage Courses)
    loadContent('manage_courses');
});
