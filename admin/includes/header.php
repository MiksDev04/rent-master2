<?php

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Get admin user info
if (!isset($_SESSION['user_id'])) {
    die("Admin not logged in");
}

$id = intval($_SESSION['user_id']);
$sql = "SELECT * FROM users WHERE user_id = $id AND user_role = 'landlord'"; // Ensure only admin
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$name = $user['user_name'];
$imagePath = $user['user_image'];

// ADMIN-SPECIFIC: Get all unread notifications count
$notifCountQuery = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
$notifCountResult = mysqli_query($conn, $notifCountQuery);
$notifCount = $notifCountResult ? mysqli_fetch_assoc($notifCountResult)['count'] : 0;

// ADMIN-SPECIFIC: Get all recent notifications
$notifQuery = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 20";
$notifResult = mysqli_query($conn, $notifQuery);
$notifications = [];
if ($notifResult) {
    while ($row = mysqli_fetch_assoc($notifResult)) {
        $notifications[] = $row;
    }
}
?>


<header style="z-index: 111;" class="bg-body-tertiary shadow-sm d-flex align-items-center justify-content-between py-1 px-3 position-sticky top-0">
    <!-- Toggle Button -->
    <button class="btn border-0 toggle-btn d-lg-none py-2 px-3 rounded-2 d-flex align-items-center" id="toggleSidebar">
        <svg width="1.2rem" fill="#555555" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
        </svg>
    </button>

    <!-- Search Form -->
    <form class="input-group" style="max-width: 300px;">
        <input type="text" class="form-control" id="search" placeholder="Search">
        <button type="submit" class="btn btn-outline-primary ms-2">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="blue">
                <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
            </svg>
        </button>
    </form>

    <!-- Notification and Profile Area -->
    <div class="d-flex align-items-center gap-1">
        <!-- Notification Dropdown -->
        <div class="dropdown">
            <button class="btn position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <svg class="notif-bell" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                    <path d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/>
                </svg>
                <?php if ($notifCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                        <?= $notifCount ?>
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                <?php endif; ?>
            </button>
            
            <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                </div>

                <?php if (empty($notifications)): ?>
                    <div class="p-3 text-center text-muted">
                        No notifications available
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $notification): ?>
                            <?php
                            $linkMap = [
                                'payment' => "payments/index&payment_id={$notification['related_id']}",
                                'maintenance' => "maintenance/index&request_id={$notification['related_id']}",
                                'property' => "reports/index&property_id={$notification['related_id']}",
                                'general' => "dashboard/index"
                            ];
                            $link = $linkMap[$notification['type']] ?? 'dashboard.php';
                            ?>
                            <a href="includes/mark_as_read.php?id=<?= $notification['notification_id'] ?>&redirect=<?= urlencode($link) ?>" class="list-group-item list-group-item-action <?= $notification['is_read'] ? '' : 'bg-body-tertiary' ?>">
                                 <div class="d-flex align-items-start">
                                    <div class="me-2">
                                        <?php 
                                            $icons = [
                                                'payment' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/></svg>',
                                                'maintenance' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.5 1.5A1.5 1.5 0 0 1 10 0h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h6c-.314.418-.5.937-.5 1.5v6h-2a.5.5 0 0 0-.354.854l2.5 2.5a.5.5 0 0 0 .708 0l2.5-2.5A.5.5 0 0 0 10.5 7.5h-2v-6z"/></svg>',
                                                'property' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5Z"/><path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z"/></svg>',
                                                'default' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>'
                                            ];
                                            $colors = [
                                                'payment' => 'text-success',
                                                'maintenance' => 'text-warning',
                                                'property' => 'text-primary',
                                                'default' => 'text-info'
                                            ];
                                            $icon = $icons[$notification['type']] ?? $icons['default'];
                                            $color = $colors[$notification['type']] ?? $colors['default'];
                                        ?>
                                        <span class="<?= $color ?>"><?= $icon ?></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= ucfirst($notification['type']) ?></strong>
                                            <small class="text-muted"><?= date('M j, g:i a', strtotime($notification['created_at'])) ?></small>
                                        </div>
                                        <div class="text-muted"><?= htmlspecialchars($notification['message']) ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Profile Link -->
        
        |<a href="?page=account/index" class="btn d-flex align-items-center gap-2 <?= (strpos($_SERVER['REQUEST_URI'], 'account') !== false) ? 'active' : '' ?>">
            <?php if (!empty($imagePath)): ?>
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 512 512">
                    <path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>
                </svg>
            <?php endif; ?>
            <span class="d-md-block d-none"><?= !empty($name) ? htmlspecialchars($name) : 'admin' ?></span>
        </a>
    </div>
</header>