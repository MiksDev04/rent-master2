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
// Force UTF-8 character set
mysqli_set_charset($conn, "utf8mb4");
// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_id = $_POST['id'];
    $tenant_email = $_POST['email'] ?? null; // Get email if available
    $action = $_POST['action'];
    $info_sql = "SELECT 
                    u.user_name, 
                    u.user_address, 
                    u.user_phone_number, 
                    u.user_email,
                    p.property_name,
                    p.property_location,
                    p.property_rental_price
                FROM tenants t
                JOIN users u ON t.user_id = u.user_id
                JOIN properties p ON t.property_id = p.property_id
                WHERE t.tenant_id = '$tenant_id AND t.landlord_id = $landlordId'
            ";

    $result = mysqli_query($conn, $info_sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        $tenantDetails = [
            'name' => $data['user_name'],
            'address' => $data['user_address'] ?? 'N/A',
            'phone' => $data['user_phone_number'] ?? 'N/A',
            'email' => $data['user_email'],
            'property_name' => $data['property_name'],
            'property_address' => $data['property_location'],
            'lease_start' => date('Y-m-d'), // Optional: replace with real lease start date
            'monthly_rent' => $data['property_rental_price']
        ];
    }
    if ($action == 'approve') {
        // Approve the tenant
        $sql = "UPDATE tenants SET tenant_status = 'active', tenant_terminated_at = null WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $sql)) {
            // Update property status to unavailable
            $property_sql = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = (SELECT property_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $property_sql);

            // Update user status to tenant
            $user_sql = "UPDATE users SET user_role = 'tenant' WHERE user_id = (SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $user_sql);


            require_once __DIR__ . '/../includes/send_email.php'; // Include the email sending function
            sendTenantDecisionEmail($data['user_email'], $action, $tenantDetails);
        } else {
            echo "Error updating tenant: " . mysqli_error($conn);
        }
    } elseif ($action == 'reject') {
        // Get user_id BEFORE deleting tenant
        $user_id_sql = "SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id'";
        $user_result = mysqli_query($conn, $user_id_sql);
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = $user_row['user_id'] ?? null;

        // Check if tenant has payment records
        $check_payment_sql = "SELECT * FROM payments WHERE tenant_id = '$tenant_id'";
        $check_result = mysqli_query($conn, $check_payment_sql);

        if (mysqli_num_rows($check_result) > 0) {
            // If payments exist, mark as terminated instead of deleting
            $sql = "UPDATE tenants SET tenant_status = 'terminated' WHERE tenant_id = '$tenant_id'";
            mysqli_query($conn, $sql);
            require_once __DIR__ . '/../includes/send_email.php'; // Include the email sending function
            sendTenantDecisionEmail($data['user_email'], $action, $tenantDetails);
        } else {
            // Delete tenant
            $sql = "DELETE FROM tenants WHERE tenant_id = '$tenant_id'";
            if (mysqli_query($conn, $sql)) {
                // Set user role to visitor, only if we successfully retrieved user_id
                if ($user_id) {
                    $user_sql = "UPDATE users SET user_role = 'visitor' WHERE user_id = '$user_id'";
                    mysqli_query($conn, $user_sql);
                    require_once __DIR__ . '/../includes/send_email.php'; // Include the email sending function
                    sendTenantDecisionEmail($data['user_email'], $action, $tenantDetails);
                }
            } else {
                echo "Error deleting tenant: " . mysqli_error($conn);
                exit();
            }
        }

        // Send rejection email and redirect via For mSubmit
    }
}


// Fetch pending rental requests
$sql = "SELECT *
        FROM tenants
        JOIN users ON tenants.user_id = users.user_id
        JOIN properties ON tenants.property_id = properties.property_id
        WHERE tenants.tenant_status = 'pending'
        AND properties.landlord_id = $landlordId
        ORDER BY tenants.tenant_date_created DESC";

$result = mysqli_query($conn, $sql);

$property_id = null;
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
}

// Check if there are any rental requests
if (mysqli_num_rows($result) == 0) {
    $no_requests = true;
} else {
    $no_requests = false;
}

?>

<div class="container px-lg-5 mb-4">
     <header class="d-flex justify-content-between my-3">
        <h4 class="fw-medium">Rental Requests</h4>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4"><?= $_GET['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4"><?= $_GET['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($no_requests): ?>
        <div class="text-center text-bg-warning mt-3">No requests found</div>
    <?php else: ?>
        <div class="mt-3">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $tenantId = $row['tenant_id'];
            ?>
                <div class="card mb-3 <?= $row['property_id'] == $property_id ? 'border-primary' : '' ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <img width="50" height="50" class="rounded-circle"
                                src="<?= htmlspecialchars($row['user_image'] ?? '/rent-master2/admin/reports/images/default.jpg') ?>"
                                alt="User">
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($row['user_name']) ?></h5>
                                <small><?= htmlspecialchars($row['user_email']) ?></small>
                                <small class="d-block"><?= htmlspecialchars($row['user_phone_number']) ?></small>
                            </div>
                        </div>
                        <small><?= date("M d, Y", strtotime($row['tenant_date_created'])) ?></small>
                    </div>

                    <div class="card-body">
                        <div>
                            <h5 class="fw-medium mb-1">Property:</h5>
                            <ul class="list-unstyled ms-3">
                                <li><strong>Name:</strong> <?= htmlspecialchars($row['property_name']) ?></li>
                                <li><strong>Location:</strong> <?= htmlspecialchars($row['property_location']) ?></li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <!-- Approve Button Trigger -->
                            <button type="button" class="btn btn-primary fw-bold rounded-5" data-bs-toggle="modal" data-bs-target="#approveModal<?= $tenantId ?>">Approve</button>

                            <!-- Reject Button Trigger -->
                            <button type="button" class="btn btn-secondary fw-bold rounded-5" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $tenantId ?>">Reject</button>
                        </div>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal<?= $tenantId ?>" tabindex="-1" aria-labelledby="approveModalLabel<?= $tenantId ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="reports/index.php" id="approve-form">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="approveModalLabel<?= $tenantId ?>">Confirm Approval</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to <strong>approve</strong> this rental request?
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $tenantId ?>">
                                    <input type="hidden" name="email" value="<?= $row['user_email'] ?>">
                                    <button type="submit" id="approve-btn" class="btn btn-primary">Yes, Approve</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal<?= $tenantId ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $tenantId ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="reports/index.php" id="reject-form">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel<?= $tenantId ?>">Confirm Rejection</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to <strong>reject</strong> this rental request?
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $tenantId ?>">
                                    <input type="hidden" name="email" value="<?= $row['user_email'] ?>">
                                    <button type="submit" id="reject-btn" class="btn btn-danger">Yes, Reject</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('approve-form');
        const submitBtn = document.getElementById('approve-btn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reject-form');
        const submitBtn = document.getElementById('reject-btn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        });
    });
</script>
<?php mysqli_close($conn); ?>