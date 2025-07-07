<?php
header('Content-Type: application/json');
session_start();

// Database configuration
$dbHost = 'localhost';
$dbName = 'rentsystem';
$dbUser = 'root';
$dbPass = '';

// Get the credential from POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['credential'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No credential received']);
    exit;
}

// Extract user data from Google JWT
$jwt = $data['credential'];
$jwtParts = explode('.', $jwt);
if (count($jwtParts) !== 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid credential format']);
    exit;
}

$payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[1]));
$userData = json_decode($payload, true);

if (!$userData || !isset($userData['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user data']);
    exit;
}

// Extract user info
$email = $userData['email'];
$name = $userData['name'] ?? '';
$picture = $userData['picture'] ?? '';

// Connect to database
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    http_response_code(500);
    error_log("Connection failed: " . mysqli_connect_error());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Escape user input to prevent SQL injection
$emailEscaped = mysqli_real_escape_string($conn, $email);

// Check if user exists
$sql = "SELECT user_id, user_email, user_name, user_image, user_role FROM users WHERE user_email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    // Existing user - set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['user_image'] = $user['user_image'];
    $_SESSION['user_role'] = $user['user_role'];
    $userId = $user['user_id'];
} else {
    // Insert new user
    $insertSql = "INSERT INTO users (user_name, user_email, user_image, user_role) VALUES (?, ?, ?, 'visitor')";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($insertStmt, "sss", $name, $email, $picture);
    if (!mysqli_stmt_execute($insertStmt)) {
        http_response_code(500);
        error_log("Insert failed: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'User creation failed']);
        exit;
    }

    $userId = mysqli_insert_id($conn);

    // Set session for new user
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_image'] = $picture;
    $_SESSION['user_role'] = 'visitor';
}

// Check landlord role
if ($_SESSION['user_role'] === 'landlord') {
    $landlordSql = "SELECT landlord_id FROM landlords WHERE user_id = ? AND landlord_status = 'active'";
    $landlordStmt = mysqli_prepare($conn, $landlordSql);
    mysqli_stmt_bind_param($landlordStmt, "i", $userId);
    mysqli_stmt_execute($landlordStmt);
    $landlordResult = mysqli_stmt_get_result($landlordStmt);
    $landlordRow = mysqli_fetch_assoc($landlordResult);

    if ($landlordRow) {
        $_SESSION['landlord_id'] = $landlordRow['landlord_id'];
    }
}

echo json_encode([
    'success' => true,
    'user_id' => $userId,
    'redirect' => ($_SESSION['user_role'] === 'landlord')
        ? '/rent-master2/admin/?page=dashboard/index&message=Welcome! You’ve successfully logged in.'
        : '/rent-master2/client/?page=src/home&message=Welcome! You’ve successfully logged in.'
]);

mysqli_close($conn);
?>
