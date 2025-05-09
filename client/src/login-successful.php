<?php
require_once '../database/config.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Determine if this is a login or registration success
$isRegistration = isset($_GET['registration']) && $_GET['registration'] === 'success';
$message = $isRegistration ? 'Registration Successful!' : 'Login Successful!';
?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center p-4">
                    <!-- Success icon -->
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                    </div>

                    <h2 class="card-title mb-3"><?php echo $message; ?></h2>
                    <p class="text-muted mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

                    <!-- User info -->
                    <div class="bg-light p-3 rounded mb-4">
                        <p class="mb-1"><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                        <?php if (isset($_SESSION['user_image'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_image']); ?>" alt="Profile Image" class="rounded-circle mt-2" style="width: 80px; height: 80px; object-fit: cover;">
                        <?php endif; ?>
                    </div>

                    <!-- Dashboard button -->
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'landlord'): ?>
                        <a href="/rent-master2/admin/?page=dashboard/index" class="btn btn-success w-100">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="/rent-master2/admin/?page=client/index" class="btn btn-success w-100">Go to Homepage</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>