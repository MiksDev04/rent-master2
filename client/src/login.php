<?php
session_start();

// 🔒 Prevent login page from being cached
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// // 🔁 Redirect already logged-in users
// if (isset($_SESSION['user_email'])) {
//     if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'landlord') {
//         header("Location: /rent-master2/admin/?page=dashboard/index");
//         exit();
//     } else {
//         header("Location: /rent-master2/client/?page=src/home");
//         exit();
//     }
// }
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
        $_SESSION['user_id'] = $user_data['user_id']; // ✅ Store user_id for tenant use

        if ($user_data['user_role'] == 'landlord') {
            header("Location: /rent-master2/admin/?page=dashboard/index");
        } else {
            header("Location: /rent-master2/client/?page=src/home");
        }
        exit();
    } else {
        $login_error = "<div class='alert alert-danger mt-3'>Invalid email or password. Please try again.</div>";
    }
}

mysqli_close($conn);
?>
<!-- Login Page -->
<div class="container">
    <div class="row align-items-center min-vh-100">
        <!-- Illustration Column -->
        <div class="col-lg-6 d-none d-lg-block p-0">
            <div class="bg-primary h-100 d-flex align-items-center justify-content-center">
                <svg width="80%" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M300,200 C300,89.54 210.46,0 100,0 C-10.46,0 -100,89.54 -100,200 C-100,310.46 -10.46,400 100,400 C210.46,400 300,310.46 300,200 Z" transform="translate(250 50)"/>
                    <path fill="#FFFFFF" d="M50,0 L450,0 C472.09,0 490,17.91 490,40 L490,360 C490,382.09 472.09,400 450,400 L50,400 C27.91,400 10,382.09 10,360 L10,40 C10,17.91 27.91,0 50,0 Z" transform="translate(50 50)"/>
                    <circle fill="#1971C2" cx="300" cy="150" r="60"/>
                    <path fill="#FFFFFF" d="M250,250 L350,250 C375.23,250 395.77,270.54 395.77,295.77 L395.77,345.77 C395.77,370.99 375.23,391.54 350,391.54 L250,391.54 C224.77,391.54 204.23,370.99 204.23,345.77 L204.23,295.77 C204.23,270.54 224.77,250 250,250 Z" transform="translate(0 -50)"/>
                </svg>
            </div>
        </div>
        
        <!-- Form Column -->
        <div class="col-lg-6 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Welcome Back</h2>
                <a href="/rent-master2/client/?page=src/register" role="" class="btn btn-outline-primary">Create Account</a>
            </div>
            
            <p class="text-muted mb-4">Sign in to manage your properties and rentals</p>
            
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?= $login_error ?></div>
            <?php endif; ?>
            
            <form method="post" class="mt-4">
                <div class="mb-3">
                    <label class="form-label" for="user_email">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
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
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                            </svg>
                        </span>
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>
                
                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a href="/rent-master2/client/?page=src/register" class="text-decoration-none">Sign up</a></p>
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