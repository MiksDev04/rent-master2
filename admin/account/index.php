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
    $password = $_POST['user_password'];
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
    $stmt = $conn->prepare("UPDATE users SET user_name=?, user_email=?, user_password=?, user_phone_number=?, user_address=?, user_description=?, user_image=? WHERE user_id=?");
    $stmt->bind_param("sssssssi", $name, $email, $password, $phone, $address, $description, $imagePath, $id);
    $stmt->execute();
    // Update session info
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_image'] = $imagePath;


    header("Location: /rent-master2/admin/?page=account/index&success=Profile updated successfully.");
    exit();
}

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
        <div class="d-flex align-items-center p-3 bg-body-tertiary rounded mb-3">
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
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="/rent-master2/client/src/logout.php" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <svg class="me-2" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="#dc3545" viewBox="0 0 512 512">
                    <path d="M502.6 273l-96 96c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l41.4-41.4H192c-13.3 0-24-10.7-24-24s10.7-24 24-24h221.1l-41.4-41.4c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l96 96c9.5 9.4 9.5 24.6.1 34zM192 432h-40V80h40c13.3 0 24-10.7 24-24s-10.7-24-24-24H96c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h96c13.3 0 24-10.7 24-24s-10.7-24-24-24z" />
                </svg>
                Logout
            </a>
            <!-- Edit Profile Section -->
            <button id="editProfileBtn" type="button" class="btn btn-link bg-info-subtle">
                <svg xmlns="http://www.w3.org/2000/svg" class="me-2" height="16px" width="16px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" />
                </svg>Edit Profile
            </button>
        </div>


        <form method="POST" action="account/index.php" enctype="multipart/form-data" id="profileForm">
            <div class="row g-3">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>" required>
                <div class="col-md-6">
                    <label for="user_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="user_name" name="user_name"
                        value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" required disabled>
                </div>
                <div class="col-md-6">
                    <label for="user_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="user_email" name="user_email"
                        value="<?= htmlspecialchars($user['user_email'] ?? '') ?>" required disabled>
                </div>
                <div class="col-md-6">
                    <label for="user_phone_number" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="user_phone_number" name="user_phone_number"
                        value="<?= htmlspecialchars($user['user_phone_number'] ?? '') ?>" required disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="user_password">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="user_password" name="user_password" value="<?= htmlspecialchars($user['user_password'] ?? '') ?>" placeholder="Enter your password" required disabled>
                        <button class="btn btn-light border" type="button" id="togglePassword" disabled>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z" />
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z" />
                                <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <label for="user_address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="user_address" name="user_address"
                        value="<?= htmlspecialchars($user['user_address'] ?? '') ?>" required disabled>
                </div>
                <div class="col-12">
                    <label for="user_description" class="form-label">About Me</label>
                    <textarea class="form-control" id="user_description" name="user_description"
                        rows="3" disabled><?= htmlspecialchars($user['user_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label for="user_image" class="form-label">Profile Image</label>
                    <input type="file" name="user_image" id="user_image" class="form-control" accept="image/*" disabled>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" name="update_profile" id="submitBtn" class="rounded-5 btn btn-primary px-4 py-2" disabled>
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('user_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the eye icon
        this.innerHTML = type === 'password' ?
            `<svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
        <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
        <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z"/>
        </svg>` :
            `<svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        </svg>`;
    });
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input, textarea, button[type="submit"], button[type="button"]');

        inputs.forEach(input => {
            input.disabled = false;
        });

        // Optionally hide the Edit button after activating edit mode
        this.style.display = 'none';
    });
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input, textarea, button[type="submit"]');

        inputs.forEach(input => {
            input.disabled = false;
        });

        // Optionally hide the Edit button after activating edit mode
        this.style.display = 'none';
    });
</script>