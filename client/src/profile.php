<?php
require_once '../database/config.php';

// Fetch user data (assuming user is logged in and ID is in session)
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['user_name'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone_number'];
    $address = $_POST['user_address'];
    $description = $_POST['user_description'];
    $imagePath = $user['user_image']; // default to existing image

    // Define the target directory
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

        // Sanitize the user name for the filename
        $sanitized_name = preg_replace("/[^a-zA-Z0-9_-]/", "", strtolower($name));

        // Generate filename based on the sanitized user name
        $new_filename = $sanitized_name . '.' . $image_file_type;
        $target_file = $target_dir . $new_filename;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
            // If there's an old image, delete it
            if (!empty($user['user_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['user_image'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $user['user_image']);
            }

            // Store the relative path to the new image in the database
            $imagePath = $upload_folder . $new_filename; // Update image path to new image
        } else {
            header("Location: ?page=src/profile&error=Sorry, there was an error uploading the file..");
            exit();
        }
    }


    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE users SET user_name=?, user_email=?, user_phone_number=?, user_address=?, user_description=?, user_image=? WHERE user_id=?");
    $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $description, $imagePath, $user_id);
    $stmt->execute();
    $_SESSION['user_name'] = $name; // Update session variable
    $_SESSION['user_image'] = $imagePath; // Update session variable
    header("Location: ?page=src/profile&success=Profile updated successfully.");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #0d6efd;
            --bs-primary-rgb: 13, 110, 253;
        }

        body {
            background-color: #f8f9fa;
        }

        .account-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .account-header {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #1e88e5 100%);
            color: white;
            padding: 2rem;
            position: relative;
        }

        .account-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            margin-top: -50px;
            background-color: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bs-primary);
            font-size: 2.5rem;
            font-weight: bold;
        }

        .action-btn {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background-color: var(--bs-primary);
        }

        .nav-pills .nav-link {
            color: #495057;
        }

        .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
        }
    </style>
</head>

<body>
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="account-card bg-white mb-4">
                    <div class="account-header text-center">
                        <h2>My Account</h2>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show"><?= $_GET['success'] ?>
                                <button class=" btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show"><?= $_GET['error'] ?>
                                <button class=" btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-center mb-4">
                            <div class="account-avatar mt-1">
                                <?php if ($user && !empty($user['user_image'])): ?>
                                    <img src="<?= htmlspecialchars($user['user_image']) ?>" alt="Profile" class="w-100 h-100 rounded-circle">
                                <?php else: ?>
                                    <?= strtoupper(substr($user['user_name'] ?? 'U', 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <h3><?= htmlspecialchars($user['user_name'] ?? 'User') ?></h3>
                            <p class="text-muted"><?= htmlspecialchars($user['user_role'] ?? 'Role') ?></p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                            <a href="?page=src/register" class="btn btn-outline-primary action-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-plus me-1" viewBox="0 0 16 16">
                                    <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                                    <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                                </svg>
                                Register
                            </a>
                            <a href="?page=src/login" class="btn btn-outline-primary action-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right me-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
                                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                                </svg>
                                Login
                            </a>

                            <a href="?page=src/your-property" class="btn btn-primary action-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door me-1" viewBox="0 0 16 16">
                                    <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
                                </svg>
                                My Properties
                            </a>
                        </div>

                        <!-- Edit Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="user_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?= htmlspecialchars($user['user_email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="user_phone_number" name="user_phone_number" value="<?= htmlspecialchars($user['user_phone_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="user_address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="user_address" name="user_address" value="<?= htmlspecialchars($user['user_address'] ?? '') ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="user_description" class="form-label">About Me</label>
                                    <textarea class="form-control" id="user_description" name="user_description" rows="3"><?= htmlspecialchars($user['user_description'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="user_image" class="form-label">Profile Image</label>
                                    <input type="file" name="user_image" id="user_image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-primary px-4 py-2">
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
            </div>
        </div>
    </div>

</body>

</html>