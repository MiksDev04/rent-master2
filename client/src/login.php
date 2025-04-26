<?php
session_start();

// 🔒 Prevent login page from being cached
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// // 🔁 Redirect already logged-in users
// if (isset($_SESSION['user_email'])) {
//     if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'landlord') {
//         header("Location: /rent-master2/admin/?page=dashboard/index");
//         exit();
//     } else {
//         header("Location: /rent-master2/client/?page=src/home");
//         exit();
//     }
// }

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);

    $sql = "SELECT * FROM users WHERE user_email = '$user_email' AND user_password = '$user_password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        $_SESSION['user_email'] = $user_email;
        $_SESSION['user_role'] = $user_data['user_role'];
        $_SESSION['user_id'] = $user_data['user_id']; // ✅ Store user_id for tenant use

        if ($user_data['user_role'] == 'landlord') {
            header("Location: /rent-master2/admin/?page=dashboard/index");
        } else {
            header("Location: /rent-master2/client/?page=src/home");
        }
        exit();
    } else {
        $login_error = "<div class='alert alert-danger mt-3'>Invalid email or password. Please try again.</div>";
    }
}

mysqli_close($conn);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0">Login</h2>
        <a href="/rent-master2/client/src/logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <?php if (isset($login_error)) echo $login_error; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label" for="user_email">Email:</label>
            <input type="email" class="form-control form-control-sm" id="user_email" name="user_email" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="user_password">Password:</label>
            <input type="password" class="form-control" id="user_password" name="user_password" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
