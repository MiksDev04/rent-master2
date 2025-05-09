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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];
    $payment_status = $_POST['payment_status'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    
    $update_sql = "UPDATE payments SET 
                  payment_status = ?,
                  payment_date = ?,
                  payment_method = ?
                  WHERE payment_id = ?";
                  
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $payment_status, $payment_date, $payment_method, $payment_id);
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
        <a href="?page=payments/index" class="p-2 rounded-circle bg-dark-subtle">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
            </svg>
        </a>
        <h4 class="fw-medium">Update Payment</h4>
    </header>

    <?php if ($payment): ?>
    <div class="card mt-3 shadow-sm p-4">
        <form method="POST" action="payments/update.php">
            <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
            
            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo htmlspecialchars($payment['payment_date']); ?>">
            </div>
            
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select class="form-select" id="payment_method" name="payment_method">
                    <option value="Cash" <?php echo ($payment['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                    <option value="Bank Transfer" <?php echo ($payment['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Credit Card" <?php echo ($payment['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                    <option value="Mobile Payment" <?php echo ($payment['payment_method'] == 'Mobile Payment') ? 'selected' : ''; ?>>Mobile Payment</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="payment_status" class="form-label">Status</label>
                <select class="form-select" id="payment_status" name="payment_status">
                    <option value="Pending" <?php echo ($payment['payment_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Paid" <?php echo ($payment['payment_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="Failed" <?php echo ($payment['payment_status'] == 'Failed') ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary flex-fill">Update Payment</button>
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