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

// Handle approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_id = $_POST['id'];
    $tenant_email = $_POST['email'] ?? null; // Get email if available
    $action = $_POST['action'];
    if ($action == 'approve') {
        // Approve the tenant
        $sql = "UPDATE tenants SET tenant_status = 'active' WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $sql)) {
            // Update property status to unavailable
            $property_sql = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = (SELECT property_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $property_sql);

            // Update user status to tenant
            $user_sql = "UPDATE users SET user_role = 'tenant' WHERE user_id = (SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $user_sql);

            // Insert initial payment record
            $payment_sql = "INSERT INTO payments (tenant_id, payment_start_date, payment_end_date, payment_status, payment_date, payment_method)
                          VALUES (
                              '$tenant_id', 
                              CURDATE(), 
                              DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 
                              'Pending', 
                              '', 
                              ''
                          )";
            mysqli_query($conn, $payment_sql);
            sendEmail($tenant_email, $action);
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
            sendEmail($tenant_email, $action); // this ends the script via `exit()`

        } else {
            // Delete tenant
            $sql = "DELETE FROM tenants WHERE tenant_id = '$tenant_id'";
            if (mysqli_query($conn, $sql)) {
                // Set user role to visitor, only if we successfully retrieved user_id
                if ($user_id) {
                    $user_sql = "UPDATE users SET user_role = 'visitor' WHERE user_id = '$user_id'";
                    mysqli_query($conn, $user_sql);
                    sendEmail($tenant_email, $action); // this ends the script via `exit()`
                }
            } else {
                echo "Error deleting tenant: " . mysqli_error($conn);
                exit();
            }
        }

        // Send rejection email and redirect via For mSubmit
    }
}

function sendEmail($tenant_email, $action)
{
    if (!$tenant_email) {
        header("Location: /rent-master2/admin/?page=reports/index&error=Missing email address.");
        exit();
    }

    ob_clean(); // clear output buffer
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';
    $admin_message = ($action === 'approve')
        ? "Congratulations! Your rental request has been approved."
        : "Unfortunately! Your rental request has been rejected.";
    $message_feedback = ($action === 'approve')
        ? "&success=Tenant added successfully."
        : "&error=Tenant rejected successfully.";
    $formSubmitUrl = "https://formsubmit.co/{$tenant_email}";

    echo '<form id="redirectForm" action="' . $formSubmitUrl . '" method="POST">';
    echo '<input type="hidden" name="_next" value="http://localhost/rent-master2/admin/?page=reports/index' . $message_feedback . '">';
    echo '<input type="hidden" name="_subject" value="Rental Request Update">';
    echo '<input type="hidden" name="_captcha" value="false">';
    echo '<input type="hidden" name="Tenant Status" value="' . htmlspecialchars(ucfirst($status)) . '">';
    echo '<input type="hidden" name="Message" value="' . htmlspecialchars($admin_message) . '">';
    echo '<input type="hidden" name="Landlord Email" value="mikogapasan04@gmail.com">';
    echo '</form>';
    echo '<script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

// Fetch pending rental requests
$sql = "SELECT *
        FROM tenants
        JOIN users ON tenants.user_id = users.user_id
        JOIN properties ON tenants.property_id = properties.property_id
        WHERE tenants.tenant_status = 'pending'
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
    <h4 class="fw-medium mt-3">Rental Requests</h4>
    
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
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
                                <ul class=" list-unstyled ms-3">
                                    <li><strong>ID:</strong> <?= $row['property_id'] ?></li>
                                    <li><strong>Name:</strong> <?= htmlspecialchars($row['property_name']) ?></li>
                                    <li><strong>Location:</strong> <?= htmlspecialchars($row['property_location']) ?></li>
                                </ul>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-end">
                                <form method="POST" action="reports/index.php">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $row['tenant_id'] ?>">
                                    <input type="hidden" name="email" value="<?= $row['user_email'] ?>">
                                    <button type="submit" class="btn btn-primary fw-bold rounded-5 ">Approve</button>
                                </form>
                                
                                <form method="POST" action="reports/index.php">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $row['tenant_id'] ?>">
                                    <input type="hidden" name="email" value="<?= $row['user_email'] ?>">
                                    <button type="submit" class="btn btn-secondary fw-bold rounded-5">Reject</button>
                                </form>
                            </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const checkAll = document.getElementById("check-all");
    const rentRequest = document.querySelectorAll(".rental-request");
    checkAll.addEventListener('input', function(e) {
        rentRequest.forEach(r => {
            r.checked = e.target.checked ? true : false;
        })
    })
</script>

<?php mysqli_close($conn); ?>