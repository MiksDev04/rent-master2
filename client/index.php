<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/rent-master2/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/rent-master2/client/assets/icons/image.ico" type="image/x-icon">
    
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php
    include('src/navbar.php'); // Include the navbar file
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'src/home';

    $allowed_pages = [
        'src/home',
        'src/login',
        'src/register',
        'src/properties',
        'src/profile',
        'src/properties-details',
        'src/logout',
        'src/contact',
        'src/your-property',
        'src/about',
        'src/rating-property',
        'src/login-successful',
        'src/register-successful',
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

    include('includes/chat.php');

    ?>

    <!-- Bootstrap 4 JS and dependencies -->
    <script src="js/script.js?v=<?php echo time(); ?>"></script>
    <script src="/rent-master2/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
</body>

</html>