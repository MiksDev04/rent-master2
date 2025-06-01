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

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user exists (select all needed fields)
    $stmt = $pdo->prepare("SELECT user_id, user_email, user_name, user_image, user_role FROM users WHERE user_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Existing user - set all session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['user_email'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_image'] = $user['user_image'];
        $_SESSION['user_role'] = $user['user_role'] ?? 'tenant';
        $userId = $user['user_id'];
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (user_name, user_email, user_image, user_role) VALUES (?, ?, ?, 'tenant')");
        $stmt->execute([$name, $email, $picture]);
        $userId = $pdo->lastInsertId();
        
        // Set session for new user
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_image'] = $picture;
        $_SESSION['user_role'] = 'tenant';
    }

    echo json_encode([
        'success' => true, 
        'user_id' => $userId,
        'redirect' => ($_SESSION['user_role'] === 'landlord') 
            ? '/rent-master2/admin/?page=dashboard/index&message=Welcome Landlord! You’ve successfully logged in.' 
            : '/rent-master2/client/?page=src/home&message=Welcome Tenant! You’ve successfully logged in.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}