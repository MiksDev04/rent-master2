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

$tenant_id = $_GET['tenant_id'] ?? null;
if (!$tenant_id) {
    die("Invalid tenant ID.");
}

// Fetch current tenant info
$queryTenant = "SELECT * FROM tenants WHERE tenant_id = '$tenant_id'";
$resultTenant = mysqli_query($conn, $queryTenant);
$tenant = mysqli_fetch_assoc($resultTenant);

if (!$tenant) {
    die("Tenant not found.");
}

$current_user_id = $tenant['user_id'];
$current_property_id = $tenant['property_id'];
$current_date_created = $tenant['tenant_date_created']; // Get the tenant's creation date

// Fetch users with 'visitor' status and current assigned user
$queryUsers = "SELECT user_id, user_name FROM users WHERE user_role = 'visitor' OR user_id = '$current_user_id'";
$usersResult = mysqli_query($conn, $queryUsers);
$users = [];
while ($row = mysqli_fetch_assoc($usersResult)) {
    $users[] = $row;
}

// Fetch properties with 'available' status and current assigned property
$queryProperties = "SELECT property_id, property_name FROM properties WHERE property_status = 'available' OR property_id = '$current_property_id'";
$propertiesResult = mysqli_query($conn, $queryProperties);
$properties = [];
while ($row = mysqli_fetch_assoc($propertiesResult)) {
    $properties[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
    $new_date_created = mysqli_real_escape_string($conn, $_POST['tenant_date_created']); // Get the updated date_created

    // Revert old property and user status if changed
    if ($new_property_id !== $current_property_id) {
        // Set previous property to available
        $queryRevertProperty = "UPDATE properties SET property_status = 'available' WHERE property_id = '$current_property_id'";
        mysqli_query($conn, $queryRevertProperty);

        // Set new property to unavailable
        $queryAssignProperty = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = '$new_property_id'";
        mysqli_query($conn, $queryAssignProperty);
    }

    if ($new_user_id !== $current_user_id) {
        // Set previous user to visitor
        $queryRevertUser = "UPDATE users SET user_role = 'visitor' WHERE user_id = '$current_user_id'";
        mysqli_query($conn, $queryRevertUser);

        // Set new user to tenant
        $queryAssignUser = "UPDATE users SET user_role = 'tenant' WHERE user_id = '$new_user_id'";
        mysqli_query($conn, $queryAssignUser);
    }

    // Update the tenant record
    $queryUpdateTenant = "UPDATE tenants SET user_id = '$new_user_id', property_id = '$new_property_id', tenant_date_created = '$new_date_created' WHERE tenant_id = '$tenant_id'";
    mysqli_query($conn, $queryUpdateTenant);

    header("Location: /rent-master2/admin/?page=tenants/index&message=Tenant updated successfully");
    exit();
}

mysqli_close($conn);
?>

<div class="container px-lg-5 mb-3">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Tenants / Update Tenant</h4>
    </header>

    <form id="tenant-form" action="tenants/update.php?tenant_id=<?php echo htmlspecialchars($tenant_id); ?>&page=tenants" method="POST">

        <div class="mt-3">
            <label for="user-id" class="form-label">Select User</label>
            <select id="user-id" name="user_id" class="form-control" required>
                <?php foreach ($users as $user): ?>
                    <option 
                        value="<?= $user['user_id']; ?>" 
                        data-user-name="<?= htmlspecialchars($user['user_name']); ?>"
                        <?= $user['user_id'] == $current_user_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['user_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mt-3">
            <label for="property-id" class="form-label">Select Property</label>
            <select id="property-id" name="property_id" class="form-control" required>
                <?php foreach ($properties as $property): ?>
                    <option 
                        value="<?= $property['property_id']; ?>" 
                        data-property-name="<?= htmlspecialchars($property['property_name']); ?>"
                        <?= $property['property_id'] == $current_property_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($property['property_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mt-3">
            <label for="tenant_date_created" class="form-label">Created On</label>
            <input type="date" id="tenant_date_created" name="tenant_date_created" class="form-control" value="<?= htmlspecialchars($current_date_created); ?>" required>
        </div>

        <button type="button" id="submit-btn" class="btn btn-success mt-4 px-4 rounded-5">Update</button>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Tenant Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update the tenant?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-5" id="confirmed-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById("submit-btn").addEventListener("click", function () {
    const userSelect = document.getElementById("user-id");
    const propertySelect = document.getElementById("property-id");
    const dateCreatedInput = document.getElementById("tenant_date_created");

    const userId = userSelect.value.trim();
    const propertyId = propertySelect.value.trim();
    const dateCreated = dateCreatedInput.value.trim();

    if (!userId || !propertyId || !dateCreated) {
        alert("All fields must be filled.");
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById("tenantModal"));
    modal.show();
});

document.getElementById("confirmed-btn").addEventListener("click", function () {
    document.getElementById("tenant-form").submit();
});

</script>
