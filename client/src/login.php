<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem"; // Replace with your actual database name

// Establish the connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);

    // Prepare the SQL query to check the email and password
    $sql = "SELECT * FROM users WHERE user_email = '$user_email' AND user_password = '$user_password'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if any user matches the credentials
    if (mysqli_num_rows($result) > 0) {
        // Fetch the user data
        $user_data = mysqli_fetch_assoc($result);

        // Start session
        session_start();

        // Check user role and redirect accordingly
        if ($user_data['user_role'] == 'landlord') {
            echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=dashboard/index'>";
            exit();
        }

        // For regular users
        $_SESSION['user_email'] = $user_email;
        echo "<meta http-equiv='refresh' content='0;url=/rent-master2/client/?page=src/home'>";
        exit();
    } else {
        // No matching user found
        echo "<div class='alert alert-danger mt-3'>Invalid email or password. Please try again.</div>";
    }
}

// Close the connection
mysqli_close($conn);
?>

<div class="container mt-2">
    <h2 class="mb-4">Login</h2>
    <form method="post">
        <div class="mt-2">
            <label class="form-label" for="user_email">Email:</label>
            <input type="email" class="form-control" id="user_email" name="user_email" required>
        </div>

        <div class="mt-2">
            <label class="form-label" for="user_password">Password:</label>
            <input type="password" class="form-control" id="user_password" name="user_password" required>
        </div>

        <button type="submit" class="btn btn-primary my-2">Login</button>
    </form>
</div>
