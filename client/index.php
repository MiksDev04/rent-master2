<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Master</title>
    <link href="/rent-master2/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <!-- Navbar -->
    <div class="bg-white shadow-sm position-sticky top-0 z-1 px-lg-5 px-md-3  px-2" >
        <nav class=" container navbar navbar-expand-lg">
            <a class="navbar-brand fw-bolder text-primary" href="#"><img src="./assets/images/image.png" alt="Logo" style="height: 30px;"> RentMaster</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/home">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/property">Property</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/">Contact</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/">Payment</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link fw-medium" href="?page=src/register">Register</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <?php
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'src/home';

    $allowed_pages = [
        'src/home',
        'src/login',
        'src/register',
        'src/property',
        'src/property-details',
        'src/logout',
        'src/contact',
        'src/payment',
        'src/about'
    ];
    
    // Get the page from the query string
    $page = isset($_GET['page']) ? $_GET['page'] : 'src/home'; // Default to 'home' if not set

    // Check if the page is in the allowed list
    if (in_array($page, $allowed_pages)) {
        include($page . '.php');
    } else {
        echo "Page not found!";
    }
    include('src/footer.php'); // Include the footer file
    ?>
    
    <!-- Bootstrap 4 JS and dependencies -->
     <script src="./js/script.js"></script>
    <script src="/rent-master2/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
</body>

</html>