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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $tenant_email = $_POST['tenant_email'];
    $admin_message = $_POST['admin_message'];

    // Update status in the database
    $update_sql = "UPDATE maintenance_requests SET status = 'completed' WHERE request_id = '$request_id'";
    mysqli_query($conn, $update_sql);

    // Redirect to FormSubmit after updating
    $formSubmitUrl = "includes/send_email.php";

    echo '<form id="redirectForm" action="' . $formSubmitUrl . '" method="POST">';
    echo '<input type="hidden" name="submit-maintenance-form" value="1">';
    echo '<input type="hidden" name="status" value="' . htmlspecialchars(ucfirst($status)) . '">';
    echo '<input type="hidden" name="admin_message" value="' . htmlspecialchars($admin_message) . '">';
    echo '<input type="hidden" name="tenant_email" value="' . htmlspecialchars($tenant_email) . '">';
    echo '</form>';

    echo '<script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

if (!isset($_GET['request_id'])) {
    die("Request ID not specified");
}

$request_id = $_GET['request_id'];

// Fetch request details
$sql = "SELECT m.*, u.user_email , u.user_name, p.property_name
        FROM maintenance_requests m
        JOIN tenants t ON m.tenant_id = t.tenant_id
        JOIN users u ON u.user_id = t.user_id
        JOIN properties p ON p.property_id = t.property_id
        WHERE m.request_id = '$request_id'";
$result = $conn->query($sql);
$request = $result->fetch_assoc();
?>


<div class="container mt-2 px-lg-5">
    <header class="d-flex  gap-2 align-items-center mt-3">
        <a href="?page=maintenance/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Respond to Maintenance Request</h4>
    </header>

    <div class="card mt-3">
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                <input type="hidden" name="tenant_email" value="<?php echo htmlspecialchars($request['user_email']); ?>">
                <div class="card mb-3 bg-light">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send-fill text-primary" viewBox="0 0 16 16">
                                <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                            </svg>
                            <div>
                                <span class="fw-bold text-primary">Send to</span>:
                                <span class="fw-semibold"><?= $request['user_name'] ?></span> in
                                <span class="fw-semibold text-decoration-underline"><?= $request['property_name'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Maintenance Status</label>
                    <select class="form-select" name="status" required>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Response Message</label>
                    <textarea class="form-control" name="admin_message" rows="5" required
                        placeholder="Enter details about the maintenance schedule or reason for rejection"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.5.5 0 0 1-.928.086L7.5 12.5l-4.486 2.904a.5.5 0 0 1-.778-.416l.004-14.59a.5.5 0 0 1 .596-.479l14.5 3.5a.5.5 0 0 1 .028.967z" />
                        </svg>
                        Send Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $conn->close(); ?>