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

        if ($user_data['user_role'] == 'landlord') {
            header("Location: /rent-master2/admin/?page=dashboard/index&message=Welcome back! You’ve successfully logged in.");
            exit();
        }
        header("Location: ?page=src/home&message=Welcome back! You’ve successfully logged in.");
        exit();
    } else {
        $login_error = "Invalid email or password. Please try again";
    }
}

mysqli_close($conn);
?>

<head>
    <!-- Add this meta tag -->
    <meta name="google-signin-client_id" content="1026066784511-sar1efnso405dvu7v77irdtdsa636trs.apps.googleusercontent.com">

    <!-- Load Google script FIRST -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
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
                        <button class="btn btn-light border" type="button" id="togglePassword">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="#6C757D">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z" />
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z" />
                                <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884l-12-12 .708-.708 12 12-.708.708z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Login</button>

                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a href="/rent-master2/client/?page=src/register" class="text-decoration-none">Register</a></p>
                </div>
            </form>
            <p class="text-center">or</p>

            <div class="mb-3 text-center">
                <button id="googleSignInBtn" class="btn btn-outline-dark w-100 py-2 d-flex align-items-center justify-content-center">
                    <img src="assets/icons/Google__G__logo.svg.png" alt="Google Logo" style="height: 20px; margin-right: 10px;">
                    Sign in with Google
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // This will DEFINITELY work
    let googleReady = false;

    // 1. First make absolutely sure Google is loaded
    function checkGoogle() {
        if (typeof google !== 'undefined' && google.accounts) {
            googleReady = true;
            console.log("Google is 100% ready!");
            initGoogleAuth();
        } else {
            setTimeout(checkGoogle, 100);
        }
    }

    // 2. Initialize Google Auth
    function initGoogleAuth() {
        google.accounts.id.initialize({
            client_id: "1026066784511-sar1efnso405dvu7v77irdtdsa636trs.apps.googleusercontent.com",
            callback: handleGoogleSignIn,
            ux_mode: "popup",
            auto_select: false
        });
    }

    // 3. Button click handler - THIS WILL SHOW THE POPUP
    document.getElementById('googleSignInBtn').addEventListener('click', function() {
        if (!googleReady) {
            alert("Please wait - Google sign-in is still loading");
            return;
        }

        console.log("Showing Google popup now!");
        google.accounts.id.prompt();

        // Fallback - if popup doesn't show after 1 second
        setTimeout(function() {
            if (!document.querySelector('.g_id_signin iframe')) {
                google.accounts.id.renderButton(
                    document.getElementById('googleSignInBtn'), {
                        theme: 'outline',
                        size: 'large',
                        width: '100%'
                    }
                );
            }
        }, 1000);
    });

    // 4. Handle the sign-in response
    function handleGoogleSignIn(response) {
        console.log("Got Google response:", response);

        // Send to your backend
        fetch('includes/process-google-signin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    credential: response.credential
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || '/rent-master2/client/?page=src/login&message=Welcome back! You’ve successfully logged in.';
                } else {
                    alert(data.message || "Login failed");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Sign-in failed. Please try again.');
            });
    }

    // Start checking when page loads
    window.addEventListener('load', checkGoogle);
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