<!-- Navbar -->
<?php 
session_start(); 
?>
<div class="bg-white shadow-sm position-sticky top-0 px-lg-5 px-md-3  px-2" style="z-index: 1111;">
    <nav class=" container navbar navbar-expand-lg">
        <a class="navbar-brand fw-bolder text-primary" href="#"><img src="./assets/images/image.png" alt="Logo" style="height: 30px;"> RentMaster</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/home">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/property">Properties</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/home#contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/your-property">Your Property</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- If user is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <!-- Profile image -->
                            <?php if (isset($_SESSION['user_image'])): ?>
                                <img src="<?php echo $_SESSION['user_image'] ?>" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                            <?php endif; ?>
                                <!-- Display name or username -->
                            <span><?= $_SESSION['user_name'] ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="?page=src/profile">My Account</a></li>
                            <li><a class="dropdown-item" href="?page=src/logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- If not logged in -->
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>
