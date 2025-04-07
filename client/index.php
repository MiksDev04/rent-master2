<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert User</title>
    <!-- Bootstrap 4 CSS -->
    <link href="/rent-master2/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
        $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'src/login';

        $allowed_pages = [
            'src/home',
            'src/login',
            'src/register'
        ];
        if (!in_array($page, $allowed_pages)) {
            $page = 'src/home';
        }

        include_once "$page.php";
    ?>

    <!-- Bootstrap 4 JS and dependencies -->
    <script src="/rent-master2/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
</body>
</html>
