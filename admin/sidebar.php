<!-- Sidebar -->
<div class="sidebar border bg-body" id="sidebar" style="z-index: 222;">
    <div class="text-center d-flex align-items-center justify-content-between">
        <div>
            <img src="./assets/icons/group.png" width="140" height="70" alt="Logo-image">
        </div>
        <button id="closeSidebar" class=" btn d-lg-none">
            <svg xmlns="http://www.w3.org/2000/svg" heig opacity-75ht="24px" viewBox="0 -960 960 960" width="24px"
                fill="currentColor">
                <path
                    d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
            </svg>
        </button>
    </div>
    <hr class=" p-0">
    <a href="?page=dashboard/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'nav-active' : '' ?> fw-medium px-4 ">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path
                d="M448 160l-128 0 0-32 128 0 0 32zM48 64C21.5 64 0 85.5 0 112l0 64c0 26.5 21.5 48 48 48l416 0c26.5 0 48-21.5 48-48l0-64c0-26.5-21.5-48-48-48L48 64zM448 352l0 32-256 0 0-32 256 0zM48 288c-26.5 0-48 21.5-48 48l0 64c0 26.5 21.5 48 48 48l416 0c26.5 0 48-21.5 48-48l0-64c0-26.5-21.5-48-48-48L48 288z" />
        </svg>
        Dashboard</a>
    <a href="?page=properties/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'properties') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c.2 35.5-28.5 64.3-64 64.3l-320.4 0c-35.3 0-64-28.7-64-64l0-160.4-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24zM352 224a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zm-96 96c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16l192 0c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80l-64 0z" />
        </svg>
        Properties</a>
    <a href="?page=tenants/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'tenants') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z" />
        </svg>
        Tenants</a>
    <a href="?page=payments/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'payments') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M96 96l0 224c0 35.3 28.7 64 64 64l416 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64L160 32c-35.3 0-64 28.7-64 64zm64 160c35.3 0 64 28.7 64 64l-64 0 0-64zM224 96c0 35.3-28.7 64-64 64l0-64 64 0zM576 256l0 64-64 0c0-35.3 28.7-64 64-64zM512 96l64 0 0 64c-35.3 0-64-28.7-64-64zM288 208a80 80 0 1 1 160 0 80 80 0 1 1 -160 0zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120L0 360c0 66.3 53.7 120 120 120l400 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-400 0c-39.8 0-72-32.2-72-72l0-240z" />
        </svg>
        Payments</a>
    <a href="?page=reports/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'reports') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M0 96C0 60.7 28.7 32 64 32l448 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24l224 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-224 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l224 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-224 0zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24l224 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-224 0z" />
        </svg>
        Reports</a>
    <a href="?page=maintenance/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'maintenance') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M176 88l0 40 160 0 0-40c0-4.4-3.6-8-8-8L184 80c-4.4 0-8 3.6-8 8zm-48 40l0-40c0-30.9 25.1-56 56-56l144 0c30.9 0 56 25.1 56 56l0 40 28.1 0c12.7 0 24.9 5.1 33.9 14.1l51.9 51.9c9 9 14.1 21.2 14.1 33.9l0 92.1-128 0 0-32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 32-128 0 0-32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 32L0 320l0-92.1c0-12.7 5.1-24.9 14.1-33.9l51.9-51.9c9-9 21.2-14.1 33.9-14.1l28.1 0zM0 416l0-64 128 0c0 17.7 14.3 32 32 32s32-14.3 32-32l128 0c0 17.7 14.3 32 32 32s32-14.3 32-32l128 0 0 64c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64z" />
        </svg>
        Maintenance</a>
    <a href="?page=settings/index" class=" d-flex align-items-center justify-content-start navbar navbar-text <?= (strpos($_SERVER['REQUEST_URI'], 'settings') !== false) ? 'nav-active' : '' ?> fw-medium px-4">
        <svg class=" me-2 opacity-75" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
        </svg>
        Settings</a>
    <!-- Logout Link (Triggers Modal) -->
    <a href="#" class="fw-medium px-4 text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <svg class="me-2" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="#dc3545" viewBox="0 0 512 512">
            <path d="M502.6 273l-96 96c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l41.4-41.4H192c-13.3 0-24-10.7-24-24s10.7-24 24-24h221.1l-41.4-41.4c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l96 96c9.5 9.4 9.5 24.6.1 34zM192 432h-40V80h40c13.3 0 24-10.7 24-24s-10.7-24-24-24H96c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h96c13.3 0 24-10.7 24-24s-10.7-24-24-24z" />
        </svg>
        Logout
    </a>

</div>