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

$payment_id = $_GET['payment_id'] ?? null;

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];
    $update_sql = "UPDATE payments SET payment_status = 'Paid', payment_date = CURDATE() WHERE payment_id = '$payment_id'";
    mysqli_query($conn, $update_sql);
    header("Location: /rent-master2/admin/?page=payments/index&message=Payment status updated to 'Paid'. The tenant has completed the payment.");
    exit();
}

// Fetch payment record with related information
$sql = "SELECT p.*, 
               pr.property_name, 
               pr.property_rental_price,
               u.user_name, 
               u.user_phone_number,
               u.user_email
        FROM payments p
        JOIN tenants t ON p.tenant_id = t.tenant_id
        JOIN properties pr ON t.property_id = pr.property_id
        JOIN users u ON t.user_id = u.user_id
        WHERE p.payment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
?>

<div class="container px-lg-5">
    <header class="d-flex gap-2 align-items-center mt-3">
        <a href="?page=payments/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Payment Details</h4>
    </header>

    <?php if ($payment): ?>
        <div class="card mt-3 shadow-sm p-4">
            <h5 class="mb-3">Payment Information</h5>

            <div class="mb-3">
                <strong>For Property:</strong> <?php echo htmlspecialchars($payment['property_name']); ?>
            </div>

            <div class="mb-3">
                <strong>Tenant:</strong> <?php echo htmlspecialchars($payment['user_name']); ?>
            </div>

            <div class="mb-3">
                <strong>Contact:</strong> <?php echo htmlspecialchars($payment['user_phone_number']); ?> | <?php echo htmlspecialchars($payment['user_email']); ?>
            </div>

            <div class="mb-3">
                <strong>Amount:</strong> ₱<?php echo number_format($payment['property_rental_price'], 2); ?>
            </div>

            <div class="mb-3">
                <strong>Payment Period:</strong>
                <?php echo date('M d, Y', strtotime($payment['payment_start_date'])) . " - " . date('M d, Y', strtotime($payment['payment_end_date'])); ?>
            </div>

            <div class="mb-3">
                <strong>Payment Method:</strong>
                <?php echo $payment['payment_method'] ? htmlspecialchars($payment['payment_method']) : "Not specified"; ?>
            </div>

            <div class="mb-3">
                <strong>Status:</strong>
                <span class="<?php
                                echo ($payment['payment_status'] == 'Paid') ? 'text-success' : ($payment['payment_status'] == 'Overdue' ? 'text-danger' : 'text-warning'); ?> fw-bold">
                    <?php echo htmlspecialchars($payment['payment_status']); ?>
                    <?php if ($payment['payment_status'] == 'Paid' && $payment['payment_date']): ?>
                        (on <?php echo date('M d, Y', strtotime($payment['payment_date'])); ?>)
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($payment['payment_status'] != 'Paid'): ?>
                <!-- Trigger Modal Instead of Immediate Submit -->
                <form id="markPaidForm" method="POST" action="payments/paid.php" class="d-flex gap-2 mt-4">
                    <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                    <button type="button" class="btn btn-success flex-fill" data-bs-toggle="modal" data-bs-target="#confirmPaidModal">
                        Mark as Paid
                    </button>
                    <a href="?page=payments/index" class="btn btn-secondary flex-fill">Cancel</a>
                </form>


            <?php else: ?>
                <div class="alert alert-success mt-4" role="alert">
                    This payment has been completed.
                </div>
                <a href="?page=payments/index" class="btn btn-primary mt-3">Back to Payments</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-5 text-center">
            No payment record found.
        </div>
    <?php endif; ?>
</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="confirmPaidModal" tabindex="-1" aria-labelledby="confirmPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPaidModalLabel">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to mark this payment as paid? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <!-- Submit the form via JS on confirm -->
                <button type="button" class="btn btn-success" onclick="document.getElementById('markPaidForm').submit();">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>