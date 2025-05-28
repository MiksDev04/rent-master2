
// DOM 
const sidebar = document.getElementById('sidebar');
const main_content = document.querySelector('.main-content');
const toggleBtn = document.getElementById('toggleSidebar');
const closeSidebar = document.getElementById('closeSidebar');

// Toggle Sidebar
toggleBtn.addEventListener('click', (event) => {
    sidebar.style.left = '0';
    event.stopPropagation(); // Prevents the click from propagating to the document
});

// Close Sidebar when clicking outside
document.addEventListener('click', (event) => {
    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target) && window.innerWidth <= 992) {
        sidebar.style.left = '-230px';
    }
});

// Close Sidebar when clicking the close button
closeSidebar.addEventListener('click', () => {
    sidebar.style.left = '-230px';
});

// Auto-hide Sidebar on Resize (Small Screens)
window.addEventListener('resize', () => {
    if (window.innerWidth > 992) {
        sidebar.style.left = '0';
    } else {
        sidebar.style.left = '-230px';
    }
});

setTimeout(function () {
    var alertEl = document.getElementById('addSuccess');
    if (alertEl) {
        var bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
        bsAlert.close();
    }
}, 2500); // 2.5 seconds