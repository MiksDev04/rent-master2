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
    // Get the next month payment
    header("Location: /rent-master2/admin/?page=payments/index&message=Payment status updated to 'Paid'. The tenant has completed the payment.");
    exit();
}

// Fetch payment record
$sql = "SELECT * FROM payments WHERE payment_id = ?";
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
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Payment Details</h4>
    </header>

    <?php if ($payment): ?>
    <div class="card mt-3 shadow-sm p-4">
        <h5 class="mb-3">Payment Information</h5>
        <p><strong>Payment ID:</strong> <?php echo 'Pay_' . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Tenant ID:</strong> <?php echo htmlspecialchars($payment['tenant_id']); ?></p>
        <p><strong>Payment Date:</strong> <?php echo htmlspecialchars($payment['payment_date']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']) ? htmlspecialchars($payment['payment_method']) : "N/A"; ?></p>
        <p><strong>Payment Period:</strong> 
            <?php echo date('M d, Y', strtotime($payment['payment_start_date'])) . " - " . date('M d, Y', strtotime($payment['payment_end_date'])); ?>
        </p>
        <p><strong>Status:</strong> 
            <span class="<?php echo ($payment['payment_status'] == 'Paid') ? 'text-success' : 'text-warning'; ?> fw-bold">
                <?php echo htmlspecialchars($payment['payment_status']); ?>
            </span>
        </p>

        <?php if ($payment['payment_status'] != 'Paid'): ?>
        <form method="POST" action="payments/paid.php" class="d-flex gap-2 mt-4">
            <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
            <button type="submit" class="btn btn-success flex-fill">Mark as Paid</button>
            <a href="?page=payments/index" class="btn btn-secondary flex-fill">Cancel</a>
        </form>
        <?php else: ?>
            <div class="alert alert-success mt-4" role="alert">
                This payment has already been marked as <strong>Paid</strong>.
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

<?php $conn->close(); ?>
