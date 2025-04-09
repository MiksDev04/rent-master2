<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}


// Fetch available properties
$queryProperties = "SELECT property_id, property_name FROM properties WHERE property_status = 'available'";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['user_id']) && !empty($_POST['property_id'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);

        // Insert tenant
        $queryInsertTenant = "INSERT INTO tenants (user_id, property_id) VALUES ('$user_id', '$property_id')";
        mysqli_query($conn, $queryInsertTenant);

        // Mark user as a tenant
        $queryUpdateProperty = "UPDATE users SET user_role = 'tenant' WHERE user_id = '$user_id'";
        mysqli_query($conn, $queryUpdateProperty);

        // Mark property as unavailable
        $queryUpdateProperty = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = '$property_id'";
        mysqli_query($conn, $queryUpdateProperty);

        echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=tenants/index'>";
        exit();
    } else {
        echo "Both user and property are required.";
    }
}

mysqli_close($conn);
?>


<div class="container px-lg-5 mb-3">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class="p-2 rounded-circle bg-dark-subtle">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
            </svg>
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

<!-- Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Tenant Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>User:</strong> <span id="modal-user-name"></span></p>
                <p><strong>Property:</strong> <span id="modal-property-name"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmed-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("submit-btn").addEventListener("click", function () {
    const userSelect = document.getElementById("user-id");
    const propertySelect = document.getElementById("property-id");

    const userId = userSelect.value.trim();
    const propertyId = propertySelect.value.trim();
    const userName = userSelect.options[userSelect.selectedIndex]?.getAttribute("data-user-name") || "";
    const propertyName = propertySelect.options[propertySelect.selectedIndex]?.getAttribute("data-property-name") || "";

    if (!userId || !propertyId) {
        alert("Both user and property must be selected.");
        return;
    }

    document.getElementById("modal-user-name").innerText = userName;
    document.getElementById("modal-property-name").innerText = propertyName;

    const modal = new bootstrap.Modal(document.getElementById("tenantModal"));
    modal.show();
});

document.getElementById("confirmed-btn").addEventListener("click", function () {
    document.getElementById("tenant-form").submit();
});
</script>
