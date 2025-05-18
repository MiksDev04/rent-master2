<?php

// 🔒 Prevent login page from being cached
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

require_once '../database/config.php';

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
        $_SESSION['user_name'] = $user_data['user_name'];
        $_SESSION['user_id'] = $user_data['user_id']; // ✅ Store user_id for tenant use
        $_SESSION['user_image'] = $user_data['user_image']; // ✅ Store user_id for tenant use

        header("Location: ?page=src/login-successful");
        exit();
    } else {
        $login_error = "Invalid email or password. Please try again";
    }
}

mysqli_close($conn);
?>
<!-- Login Page -->
<div class="container">
    <div class="row justify-content-center  min-vh-100">

        <!-- Form Column -->
        <div class="col-lg-8 col-12 col-md-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Welcome</h2>
                <a href="/rent-master2/client/?page=src/register" role="" class="btn btn-outline-primary">Create Account</a>
            </div>

            <p class="text-muted mb-4">Sign in to manage your properties and rentals</p>

            <?php if (isset($login_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= $login_error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <form method="post" class="mt-4">
                <div class="mb-3">
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

                <div class="mb-4">
                    <label class="form-label" for="user_password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                            </svg>
                        </span>
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>

                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a href="/rent-master2/client/?page=src/register" class="text-decoration-none">Register</a></p>
                </div>
            </form>
            <button role="button" type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="btn btn-outline-warning">
                Logout Account
            </button>

        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="/rent-master2/client/src/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

