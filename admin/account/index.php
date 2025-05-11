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


// Fetch logged-in user
$user_id = $_SESSION['user_id'] ?? null;
$user = null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $name = $_POST['user_name'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone_number'];
    $address = $_POST['user_address'];
    $description = $_POST['user_description'];

    // Get the current user data first
    $stmt = $conn->prepare("SELECT user_image FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentUser = $result->fetch_assoc();

    // Default image path = existing image
    $imagePath = $currentUser['user_image'];

    // Upload config
    $upload_folder = "/rent-master2/admin/assets/tenants/";
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . $upload_folder;

    // If a new image is uploaded
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        // Ensure the target directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Get the image file extension
        $image_file_type = strtolower(pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION));

        // Generate a unique filename
        $unique_filename = uniqid() . '.' . $image_file_type;
        $target_file = $target_dir . $unique_filename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
            // If there's an old image, delete it
            if (!empty($currentUser['user_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentUser['user_image'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $currentUser['user_image']);
            }

            // Store the relative path to the new image in the database
            $imagePath = $upload_folder . $unique_filename;
        } else {
            header("Location: /rent-master2/admin/?page=account/index&error=Sorry, there was an error uploading the file.");
            exit();
        }
    }
    // Update user info
    $stmt = $conn->prepare("UPDATE users SET user_name=?, user_email=?, user_phone_number=?, user_address=?, user_description=?, user_image=? WHERE user_id=?");
    $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $description, $imagePath, $id);
    $stmt->execute();
    // Update session info
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_image'] = $imagePath;


    header("Location: /rent-master2/admin/?page=account/index&success=Profile updated successfully.");
    exit();
}

mysqli_close($conn);
?>



<div class="container  px-lg-5">
    <header class="mt-3">
        <h4 class="fw-medium">Your Account</h4>
    </header>
    <div class="container mb-3">
        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4"><?= $_GET['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4"><?= $_GET['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
            <!-- Profile Image -->
            <div class="me-4">
                <div class="border border-4 border-white rounded-circle bg-secondary overflow-hidden" style="width: 100px; height: 100px;">
                    <?php if ($user && !empty($user['user_image'])): ?>
                        <img src="<?= htmlspecialchars($user['user_image']) ?>" alt="Profile" class="w-100 h-100 object-fit-cover rounded-circle">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-white">
                            <span class="fs-1 fw-bold"><?= strtoupper(substr($user['user_name'] ?? 'U', 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Info -->
            <div>
                <h4 class="mb-1"><?= htmlspecialchars($user['user_name'] ?? 'User') ?></h4>
                <p class="text-muted mb-0"><?= htmlspecialchars($user['user_role'] ?? 'Role') ?></p>
            </div>
        </div>


        <!-- Action Buttons -->
        <div class="d-flex gap-3 mb-3">
            <a href="/rent-master2/client/src/logout.php" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <svg class="me-2" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="#dc3545" viewBox="0 0 512 512">
                    <path d="M502.6 273l-96 96c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l41.4-41.4H192c-13.3 0-24-10.7-24-24s10.7-24 24-24h221.1l-41.4-41.4c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l96 96c9.5 9.4 9.5 24.6.1 34zM192 432h-40V80h40c13.3 0 24-10.7 24-24s-10.7-24-24-24H96c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h96c13.3 0 24-10.7 24-24s-10.7-24-24-24z" />
                </svg>
                Logout
            </a>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="account/index.php" enctype="multipart/form-data">
            <div class="row row-cols-1 row-cols-lg-2 g-3">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>" required>
                <div class="col">
                    <label for="user_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label for="user_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?= htmlspecialchars($user['user_email'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label for="user_phone_number" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="user_phone_number" name="user_phone_number" value="<?= htmlspecialchars($user['user_phone_number'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label for="user_address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="user_address" name="user_address" value="<?= htmlspecialchars($user['user_address'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label for="user_description" class="form-label">About Me</label>
                    <textarea class="form-control" id="user_description" name="user_description" rows="3"><?= htmlspecialchars($user['user_description'] ?? '') ?></textarea>
                </div>
                <div class="col">
                    <label for="user_image" class="form-label">Profile Image</label>
                    <input type="file" name="user_image" id="user_image" class="form-control" accept="image/*">
                </div>
                <div class="col mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save me-1" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>