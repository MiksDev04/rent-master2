<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id']) && isset($_GET['redirect'])) {
    $id = intval($_GET['id']);
    $redirect = urldecode($_GET['redirect']);

    $update = "UPDATE notifications SET is_read = 1 WHERE notification_id = $id";
    mysqli_query($conn, $update);

    header("Location: /rent-master2/admin/?page=$redirect");
    exit;
}
?>
