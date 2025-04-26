<?php
// Database connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle approval or rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $tenant_id = $_GET['id'];
    if ($_GET['action'] == 'approve') {
        // Approve the tenant
        $sql = "UPDATE tenants SET tenant_status = 'active' WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $sql)) {
            // Update property status to unavailable
            $property_sql = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = (SELECT property_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $property_sql);

            // Update user status to tenant
            $user_sql = "UPDATE users SET user_role = 'tenant' WHERE user_id = (SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $user_sql);

            header("Location: /rent-master2/admin/?page=reports/index");
            exit;
        } else {
            echo "Error updating tenant: " . mysqli_error($conn);
        }
    } elseif ($_GET['action'] == 'reject') {
        // Reject the tenant and delete
        $sql = "DELETE FROM tenants WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $sql)) {
            // Update user status to tenant
            $user_sql = "UPDATE users SET user_role = 'visitor' WHERE user_id = (SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $user_sql);
            header("Location: /rent-master2/admin/?page=reports/index");
            exit;
        } else {
            echo "Error deleting tenant: " . mysqli_error($conn);
        }
    }
}

// Fetch pending rental requests
$sql = "SELECT *
        FROM tenants
        JOIN users ON tenants.user_id = users.user_id
        JOIN properties ON tenants.property_id = properties.property_id
        WHERE tenants.tenant_status = 'pending'
        ORDER BY tenants.tenant_date_created DESC";

$result = mysqli_query($conn, $sql);

// Check if there are any rental requests
if (mysqli_num_rows($result) == 0) {
    $no_requests = true;
} else {
    $no_requests = false;
}

?>

<div class="container px-lg-5 mb-4">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Rental Request/s</h4>
        <button class="btn btn-primary fw-bold rounded-5 px-4">
            Send Email
        </button>
    </header>
    
    <?php if ($no_requests): ?>
        <div class="text-center text-bg-warning mt-3">No record found</div>
    <?php else: ?>
        <div class="container d-flex align-items-center justify-content-end gap-3 mt-2">
            <span class="text-black-50">Select All</span>
            <input type="checkbox" name="all-rental-request" class="form-check-input" id="check-all">
        </div>

        <div class="container mt-3">
            <div class="row gap-3">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12 d-flex justify-content-between align-items-center gap-3">
                        <div class="card">
                            <div class="card-header d-sm-flex d-grid gap-2 justify-content-between">
                                <div class="d-flex">
                                    <div class="card-img d-flex align-items-center gap-3">
                                        <img width="70" height="70" class="rounded-circle"
                                             src="<?php echo htmlspecialchars($row['user_image'] ?? '/rent-master2/admin/reports/images/default.jpg'); ?>" 
                                             alt="User Image">
                                        <div>
                                            <h4 class="fw-medium card-title mb-1"><?php echo htmlspecialchars($row['user_name']); ?></h4>
                                            <span class="opacity-75 d-block card-subtitle"><?php echo htmlspecialchars($row['user_email']); ?></span>
                                            <span class="opacity-75 d-block card-subtitle"><?php echo htmlspecialchars($row['user_phone_number']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-black-50"><?php echo date("F d, Y h:i A", strtotime($row['tenant_date_created'])); ?></div>
                            </div>
                            <div class="card-body">
                                <blockquote class="blockquote">
                                    <p class="fs-6 fw-medium lh-1">Dear Mr. Caricot</p>
                                    <p class="opacity-75 fs-6 ps-3">
                                        I’m interested in renting <?php echo htmlspecialchars($row['property_name']); ?> located at 
                                        <?php echo htmlspecialchars($row['property_location']); ?>. Could we schedule a viewing at your earliest convenience? Please let me know the next steps.
                                    </p>
                                </blockquote>
                                <div class="d-sm-flex d-grid justify-content-between">
                                    <div>
                                        <p class="fs-6 fw-medium lh-1">Property</p>
                                        <ul class="list-unstyled ps-3">
                                            <li><span class="fw-medium">ID: </span><?php echo $row['property_id']; ?></li>
                                            <li><span class="fw-medium">Name: </span><?php echo htmlspecialchars($row['property_name']); ?></li>
                                            <li><span class="fw-medium">Location: </span><?php echo htmlspecialchars($row['property_location']); ?></li>
                                        </ul>
                                    </div>
                                    <div class="d-flex gap-3 align-self-end">
                                        <!-- Approve Button -->
                                        <a href="/rent-master2/admin/reports/index.php?action=approve&id=<?php echo $row['tenant_id']; ?>" class="rounded-5 btn btn-primary px-3 fw-medium">Approve</a>
                                        <!-- Reject Button -->
                                        <a href="/rent-master2/admin/reports/index.php?action=reject&id=<?php echo $row['tenant_id']; ?>" class="rounded-5 btn btn-secondary px-3 fw-medium">Reject</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="checkbox" name="rental-request" class="rental-request form-check-input">
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const checkAll = document.getElementById("check-all");
    const rentRequest = document.querySelectorAll(".rental-request");
    checkAll.addEventListener('input', function (e) {
        rentRequest.forEach(r => {
            r.checked = e.target.checked ? true : false;
        })
    })
</script>

<?php mysqli_close($conn); ?>
