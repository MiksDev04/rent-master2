<?php
// Database connection 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['user_id']) && !empty($_POST['property_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);

        session_start();
        // In your create.php file, update the INSERT statement:
        $landlordId = $_SESSION['landlord_id']; // Assuming landlord_id is stored in session
        // Insert new tenant
        $queryInsertTenant = " INSERT INTO tenants (user_id, property_id, landlord_id, tenant_status, tenant_date_created, tenant_terminated_at) 
                                VALUES ('$user_id', '$property_id', '$landlordId', 'active', NOW(), NULL)
                                ";
        mysqli_query($conn, $queryInsertTenant);


        // Mark user as a tenant
        $queryUpdateUser = "UPDATE users SET user_role = 'tenant' WHERE user_id = '$user_id'";
        mysqli_query($conn, $queryUpdateUser);

        // Mark property as unavailable
        $queryUpdateProperty = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = '$property_id'";
        mysqli_query($conn, $queryUpdateProperty);

        header("Location: /rent-master2/admin/?page=tenants/index&message=Tenant assigned successfully");
        exit();
    } else {
        echo "Both user and property are required.";
    }
}

// Fetch available properties
$queryProperties = "SELECT property_id, property_name FROM properties WHERE property_status = 'available' AND landlord_id = $landlordId";
$propertiesResult = mysqli_query($conn, $queryProperties);
$properties = [];
while ($row = mysqli_fetch_assoc($propertiesResult)) {
    $properties[] = $row;
}

// Fetch users with 'visitor' status
$queryUsers = "SELECT user_id, user_name FROM users WHERE user_role = 'visitor'";
$usersResult = mysqli_query($conn, $queryUsers);
$users = [];
while ($row = mysqli_fetch_assoc($usersResult)) {
    $users[] = $row;
}

?>

<div class="container px-lg-5 mb-3">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Tenants / Assign Tenant</h4>
    </header>

    <form id="tenant-form" action="tenants/create.php" method="POST">
        <div class="mt-3">
            <label for="user-id" class="form-label">Select User</label>
            <select id="user-id" name="user_id" class="form-control" required>
                <option value="" disabled selected>Select Visitor User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id']; ?>" data-user-name="<?= htmlspecialchars($user['user_name']); ?>">
                        <?= htmlspecialchars($user['user_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mt-3">
            <label for="property-id" class="form-label">Select Property</label>
            <select id="property-id" name="property_id" class="form-control" required>
                <option value="" disabled selected>Select Available Property</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?= $property['property_id']; ?>" data-property-name="<?= htmlspecialchars($property['property_name']); ?>">
                        <?= htmlspecialchars($property['property_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="button" id="submit-btn" class="btn btn-success mt-4 px-4 rounded-5">Submit</button>
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Tenant Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to assign this user as a new tenant?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-5" id="confirmed-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("submit-btn").addEventListener("click", function() {
        const userId = document.getElementById("user-id").value.trim();
        const propertyId = document.getElementById("property-id").value.trim();

        if (!userId || !propertyId) {
            alert("Both user and property must be selected.");
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById("tenantModal"));
        modal.show();
    });

    document.getElementById("confirmed-btn").addEventListener("click", function() {
        document.getElementById("tenant-form").submit();
    });
</script>