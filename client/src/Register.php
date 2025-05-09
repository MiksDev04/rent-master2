<?php
// Database connection with error handling
require_once '../database/config.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);
    $user_phone_number = mysqli_real_escape_string($conn, $_POST['user_phone_number']);
    $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);
    $user_description = mysqli_real_escape_string($conn, $_POST['user_description']);

    $user_image = ''; // Default to an empty string if no image is uploaded

    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        // Define the target directory
        $upload_folder = "/rent-master2/admin/assets/tenants/";
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . $upload_folder;

        // Get the image file extension
        $image_file_type = strtolower(pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION));

        // Generate unique filename
        $unique_filename = uniqid() . '.' . $image_file_type;
        $target_file = $target_dir . $unique_filename;

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
            // ✅ Store only the relative path in DB
            $user_image = $upload_folder . $unique_filename;
        } else {
            echo "<div class='alert alert-danger mt-3'>Sorry, there was an error uploading the file.</div>";
        }
    }


    // Default user status
    $user_role = 'visitor'; // Default status

    // Prepare the SQL query
    $sql = "INSERT INTO users (user_name, user_email, user_password, user_phone_number, user_address, user_description, user_image, user_role)
            VALUES ('$user_name', '$user_email', '$user_password', '$user_phone_number', '$user_address', '$user_description', '$user_image', '$user_role')";

    // Execute the query and check for success
    if (mysqli_query($conn, $sql)) {
        if (mysqli_query($conn, $sql)) {
            $user_id = mysqli_insert_id($conn); // Get the last inserted ID

            // (Optional) Fetch user info from DB using the ID if you want to display it

            // ✅ Fetch newly created user to set session
            $fetch_sql = "SELECT * FROM users WHERE user_id = $user_id LIMIT 1";
            $fetch_result = mysqli_query($conn, $fetch_sql);

            if ($user_data = mysqli_fetch_assoc($fetch_result)) {
                // ✅ Set session variables to log in user
                $_SESSION['user_email'] = $user_data['user_email'];
                $_SESSION['user_role'] = $user_data['user_role'];
                $_SESSION['user_name'] = $user_data['user_name'];
                $_SESSION['user_id']   = $user_data['user_id'];
                $_SESSION['user_image'] = $user_data['user_image'];
            }

            // Redirect with inserted ID and name
            header("Location: /rent-master2/client/?page=src/register-successful");
            exit();
        } else {
            echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
        }
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Close the connection
mysqli_close($conn);
?>

<!-- Register Page -->
<div class="container">
    <div class="row align-items-center min-vh-100">
        <!-- Form Column -->
        <div class="col-lg-6 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create Account</h2>
                <a href="/rent-master2/client/?page=src/login" class="btn btn-outline-primary">Sign In</a>
            </div>

            <p class="text-muted mb-4">Join us to find your perfect rental property</p>

            <form method="post" enctype="multipart/form-data" class="mt-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_name">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                                </svg>
                            </span>
                            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z" />
                                </svg>
                            </span>
                            <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter your email" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                </svg>
                            </span>
                            <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Create a password" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_phone_number">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z" />
                                </svg>
                            </span>
                            <input type="number" class="form-control" id="user_phone_number" name="user_phone_number" placeholder="Enter phone number" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user_address">Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                            </svg>
                        </span>
                        <input type="text" class="form-control" id="user_address" name="user_address" placeholder="Enter your address" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="user_description">About You</label>
                    <textarea class="form-control" id="user_description" name="user_description" rows="3" placeholder="Tell us about yourself"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="user_image">Profile Photo</label>
                    <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Create Account</button>

                <div class="text-center">
                    <p class="text-muted">Already have an account? <a href="/rent-master2/client/?page=src/login" class="text-decoration-none">Sign in</a></p>
                </div>
            </form>
        </div>

        <!-- Illustration Column -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <div class="bg-primary h-100 d-flex align-items-center justify-content-center">
                <svg width="80%" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M300,200 C300,89.54 210.46,0 100,0 C-10.46,0 -100,89.54 -100,200 C-100,310.46 -10.46,400 100,400 C210.46,400 300,310.46 300,200 Z" transform="translate(250 50)" />
                    <path fill="#FFFFFF" d="M50,0 L450,0 C472.09,0 490,17.91 490,40 L490,360 C490,382.09 472.09,400 450,400 L50,400 C27.91,400 10,382.09 10,360 L10,40 C10,17.91 27.91,0 50,0 Z" transform="translate(50 50)" />
                    <path fill="#1971C2" d="M150,100 L450,100 C472.09,100 490,117.91 490,140 L490,240 C490,262.09 472.09,280 450,280 L150,280 C127.91,280 110,262.09 110,240 L110,140 C110,117.91 127.91,100 150,100 Z" transform="translate(0 50)" />
                    <circle fill="#FFFFFF" cx="300" cy="190" r="40" />
                    <path fill="#1971C2" d="M250,300 L350,300 C375.23,300 395.77,320.54 395.77,345.77 L395.77,395.77 C395.77,420.99 375.23,441.54 350,441.54 L250,441.54 C224.77,441.54 204.23,420.99 204.23,395.77 L204.23,345.77 C204.23,320.54 224.77,300 250,300 Z" transform="translate(0 -50)" />
                </svg>
            </div>
        </div>
    </div>
</div>