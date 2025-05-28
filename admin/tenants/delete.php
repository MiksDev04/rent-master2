<?php
// Database connection with error handling
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle the termination after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tenant_id']) && isset($_POST['user_id']) && isset($_POST['property_id'])) {
        $tenant_id = $_POST['tenant_id'];
        $user_id = $_POST['user_id'];
        $property_id = $_POST['property_id'];

        // Update tenant status to 'terminated'
        $queryTerminate = "UPDATE tenants SET tenant_status = 'terminated', tenant_terminated_at = NOW() WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $queryTerminate)) {
            // Update user status to 'visitor'
            mysqli_query($conn, "UPDATE users SET user_role = 'visitor' WHERE user_id = '$user_id'");

            // Update property status to 'available'
            mysqli_query($conn, "UPDATE properties SET property_status = 'available' WHERE property_id = '$property_id'");

            // Redirect to tenants list
            header("Location: /rent-master2/admin/?page=tenants/index&message=Tenant removed successfully");
            exit();
        } else {
            echo "Error terminating tenant: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid request.";
    }
} else {
    // Fetch tenant details for termination confirmation
    if (isset($_GET['tenant_id'])) {
        $tenant_id = $_GET['tenant_id'];

        $query = "SELECT t.*, u.user_name, p.property_name FROM tenants t
                JOIN users u ON t.user_id = u.user_id
                JOIN properties p ON t.property_id = p.property_id
                WHERE t.tenant_id = '$tenant_id'";
        $result = mysqli_query($conn, $query);
        $tenant = mysqli_fetch_assoc($result);

        if (!$tenant) {
            echo "Tenant not found.";
            exit();
        }
    } else {
        echo "No tenant ID provided.";
        exit();
    }
}

mysqli_close($conn);
?>

<!-- Tenant Termination Confirmation -->
<div class="container px-lg-5 mb-4">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Tenant / Terminate Tenant</h4>
    </header>

    <p class="text-warning">Are you sure you want to terminate the following tenant? This action cannot be undone.</p>

    <div class="mt-2">
        <label class="form-label fw-bold">User Name</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($tenant['user_name']); ?></div>
    </div>

    <div class="mt-2">
        <label class="form-label fw-bold">Property</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($tenant['property_name']); ?></div>
    </div>
    
    <form action="tenants/delete.php" method="POST">
        <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($tenant['tenant_id']); ?>">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($tenant['user_id']); ?>">
        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($tenant['property_id']); ?>">
        <div class="mt-3 d-flex gap-3">
            <button type="submit" class="btn btn-danger rounded-5">Terminate Tenant</button>
            <a href="?page=tenants/index" class="btn btn-secondary rounded-5">Cancel</a>
        </div>
    </form>
</div>
