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
    $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
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

        // Generate a unique filename
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
        header("Location: ?page=src/home&message='Registration complete! You can now access your account.'");
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
    }
    exit();
}

?>

<!-- Register Page -->
<div class="container">
    <div class="row justify-content-center  min-vh-100">

        <!-- Form Column -->
        <div class="col-lg-8 col-12 col-md-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Create Account</h2>
                <a href="?page=src/login" class="btn btn-outline-primary">Log In</a>
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
                        <label class="form-label" for="user_password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                </svg>
                            </span>
                            <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Create a password" required>
                            <button class="btn btn-light border" type="button" id="togglePassword">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z" />
                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z" />
                                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="row">

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
                <div class=" mb-3">
                    <label class="form-label" for="user_image">Profile Photo</label>
                    <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
                </div>
                
                <div class="mb-3">
                    <label class="form-label" for="user_description">About You</label>
                    <textarea class="form-control" id="user_description" name="user_description" rows="3" placeholder="Tell us about yourself"></textarea>
                </div>



                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Create Account</button>

                <div class="text-center">
                    <p class="text-muted">Already have an account? <a href="?page=src/login" class="text-decoration-none">Log in</a></p>
                </div>
            </form>
        </div>

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
</script>