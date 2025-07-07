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
    $payment_start_date = $_POST['payment_start_date'];
    $payment_end_date = $_POST['payment_end_date'];

    $update_sql = "UPDATE payments SET 
                  payment_start_date = ?,
                  payment_end_date = ?
                  WHERE payment_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $payment_start_date, $payment_end_date, $payment_id);
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
        <h4 class="fw-medium">Update Payment Period</h4>
    </header>

    <?php if ($payment): ?>
    <div class="card mt-3 shadow-sm p-4">
        <form method="POST" action="payments/update.php">
            <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">

            <div class="mb-3">
                <label for="payment_start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="payment_start_date" name="payment_start_date" value="<?php echo htmlspecialchars($payment['payment_start_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="payment_end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="payment_end_date" name="payment_end_date" value="<?php echo htmlspecialchars($payment['payment_end_date']); ?>" required>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary flex-fill">Update Dates</button>
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

