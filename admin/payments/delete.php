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

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];
    
    $delete_sql = "DELETE FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    
    header("Location: /rent-master2/admin/?page=payments/index");
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
        <h4 class="fw-medium">Delete Payment</h4>
    </header>

    <?php if ($payment): ?>
    <div class="card mt-3 shadow-sm p-4">
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading">Warning!</h5>
            <p>You are about to delete this payment record. This action cannot be undone.</p>
        </div>
        
        <h5 class="mb-3">Payment Information</h5>
        <p><strong>Payment ID:</strong> <?php echo 'Pay_' . str_pad($payment['payment_id'], 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Payment Period:</strong> 
            <?php echo date('M d, Y', strtotime($payment['payment_start_date'])) . " - " . date('M d, Y', strtotime($payment['payment_end_date'])); ?>
        </p>
        <p><strong>Status:</strong> 
            <span class="<?php echo ($payment['payment_status'] == 'Paid') ? 'text-success' : 'text-warning'; ?> fw-bold">
                <?php echo htmlspecialchars($payment['payment_status']); ?>
            </span>
        </p>

        <form method="POST" action="payments/delete.php" class="mt-4">
            <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill">Confirm Delete</button>
                <a href="?page=payments/index" class="btn btn-secondary flex-fill">Cancel</a>
            </div>
        </form>
    </div>
    <?php else: ?>
        <div class="alert alert-warning mt-5 text-center">
            No payment record found.
        </div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>