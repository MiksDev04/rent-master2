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

    $id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE user_id = $id";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    $name = $user['user_name'];
    $imagePath = $user['user_image'];
?>
            
<header style="z-index: 111;" class=" bg-body-tertiary shadow-sm d-flex align-items-center justify-content-between py-1 px-3 position-sticky top-0">
    <!-- Toggle Button (Only for Small Screens) -->
    <button class=" btn border-0 toggle-btn d-lg-none py-2 px-3 rounded-2 d-flex align-items-center" id="toggleSidebar">
        <svg width="1.2rem" fill="#555555" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
            <path
                d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z" />
        </svg>
    </button>
    <form class="input-group " style="max-width: 300px;">
        <input type="text" class="form-control" id="search" placeholder="Search">
        <button type="submit" class="btn btn-outline-primary ms-2">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                fill="blue">
                <path
                    d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
            </svg>
        </button>
    </form>
    <div class=" d-flex align-items-center">
        <details>
            <summary class="list-unstyled btn">
                <svg class="notif-bell" fill="black" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                    width="24px" fill="#555555">
                    <path
                        d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z" />
                </svg>
            </summary>
            <div class=" container bg-white rounded-3 shadow-sm position-fixed w-75 px-3 py-2 z-3 " style="right: 2rem;">
                <ul class=" list-unstyled text-black-50 d-grid gap-2">
                    <li class="border-bottom border-1"> <strong> New Payment Received</strong> : Tenant John Doe has paid ₱10,000 for March 2025 on March 28, 2025 at 10:30 AM. Please verify the transaction.</li>
                    <li class="border-bottom border-1"> <strong> New Payment Received</strong> : Tenant George Peterson has paid ₱7,000 for April 2025 on April 28, 2025 at 2:15 PM. Please verify the transaction.</li>
                    <li class="border-bottom border-1"> <strong> New Payment Received</strong> : Tenant Marc Eihenburg has paid ₱13,000 for March 2025 on March 13, 2025 at 8:45 AM. Please verify the transaction.</li>
                </ul>
            </div>
        </details>
        |<a href="?page=account/index" class="btn d-flex align-items-center gx-2 <?= (strpos($_SERVER['REQUEST_URI'], 'account') !== false) ? 'active' : '' ?> ">
            <!-- Profile image -->

            <?php if (!empty($imagePath)): ?>
                <img src="<?php echo $imagePath ?>" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z" />
                </svg>
            <?php endif; ?>
            <!-- Display name or username -->
            <span class=" d-md-block d-none"><?= !empty($name) ? $name : 'admin' ?></span>
        </a>
    </div>
</header>