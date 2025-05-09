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
if (isset($_POST['action']) && isset($_POST['id'])) {
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
            exit;
        } else {
            echo "Error updating tenant: " . mysqli_error($conn);
        }
    } elseif ($action == 'reject') {
        // Reject the tenant and delete
        $sql = "DELETE FROM tenants WHERE tenant_id = '$tenant_id'";
        if (mysqli_query($conn, $sql)) {
            // Update user status to tenant
            $user_sql = "UPDATE users SET user_role = 'visitor' WHERE user_id = (SELECT user_id FROM tenants WHERE tenant_id = '$tenant_id')";
            mysqli_query($conn, $user_sql);
            sendEmail($tenant_email, $action);
            exit;
        } else {
            echo "Error deleting tenant: " . mysqli_error($conn);
        }
    }

}

function sendEmail($tenant_email, $action) {
    $status = ($action === 'approve') ? 'Approved' : 'Rejected';
    $admin_message = ($action === 'approve')
        ? "Congratulations! Your rental request has been approved."
        : "We're sorry to inform you that your rental request has been rejected.";

    // Redirect to FormSubmit after updating
    $formSubmitUrl = "https://formsubmit.co/{$tenant_email}";

    echo '<form id="redirectForm" action="' . $formSubmitUrl . '" method="POST">';
    echo '<input type="hidden" name="_next" value="http://localhost/rent-master2/admin/?page=reports/index">';
    echo '<input type="hidden" name="_subject" value="Rental Request Update">';
    echo '<input type="hidden" name="_captcha" value="false">';
    echo '<input type="hidden" name="Maintenance Status" value="' . htmlspecialchars(ucfirst($status)) . '">';
    echo '<input type="hidden" name="Message" value="' . htmlspecialchars($admin_message) . '">';
    // echo '<input type="hidden" name="Request ID" value="'.htmlspecialchars($request_id).'">';
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
                                        <!-- Approve Form -->
                                        <form action="/rent-master2/admin/?page=reports/index" method="POST">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="id" value="<?php echo $row['tenant_id']; ?>">
                                            <button type="submit" class="rounded-5 btn btn-primary px-3 fw-medium">Approve</button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form action="/rent-master2/admin/?page=reports/index" method="POST">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="id" value="<?php echo $row['tenant_id']; ?>">
                                            <input type="hidden" name="email" value="<?php echo $row['user_email']; ?>">
                                            <button type="submit" class="rounded-5 btn btn-secondary px-3 fw-medium">Reject</button>
                                        </form>
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
    checkAll.addEventListener('input', function(e) {
        rentRequest.forEach(r => {
            r.checked = e.target.checked ? true : false;
        })
    })
</script>

<?php mysqli_close($conn); ?>