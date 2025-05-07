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
// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $tenant_email = $_POST['tenant_email'];
    $admin_message = $_POST['admin_message'];

    // Update status in the database
    $update_sql = "UPDATE maintenance_requests SET status = 'completed' WHERE request_id = '$request_id'";
    mysqli_query($conn, $update_sql);

    // Redirect to FormSubmit after updating
    $formSubmitUrl = "https://formsubmit.co/{$tenant_email}";
    
    echo '<form id="redirectForm" action="'.$formSubmitUrl.'" method="POST">';
    echo '<input type="hidden" name="_next" value="http://localhost/rent-master2/admin/?page=maintenance/index">';
    echo '<input type="hidden" name="_subject" value="Maintenance Request Update">';
    echo '<input type="hidden" name="_captcha" value="false">';
    echo '<input type="hidden" name="Maintenance Status" value="'.htmlspecialchars(ucfirst($status)).'">';
    echo '<input type="hidden" name="Message" value="'.htmlspecialchars($admin_message).'">';
    // echo '<input type="hidden" name="Request ID" value="'.htmlspecialchars($request_id).'">';
    echo '<input type="hidden" name="Landlord Email" value="mikogapasan04@gmail.com">';
    echo '</form>';
    
    echo '<script>document.getElementById("redirectForm").submit();</script>';
    exit();
}

// Fetch maintenance requests with tenant emails
$sql = "SELECT m.request_id, m.tenant_id, m.category, m.description, m.request_date, m.status, u.user_email 
        FROM maintenance_requests m
        JOIN tenants t ON m.tenant_id = t.tenant_id
        JOIN users u ON u.user_id = t.user_id
        ORDER BY m.request_date DESC";
$result = $conn->query($sql);
?>

<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Maintenance Requests</h4>
    </header>
    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead class="table-info">
                <tr>
                    <th>Request ID</th>
                    <th>Tenant ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo 'Req_' . str_pad($row['request_id'], 6, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($row['tenant_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['request_date'])); ?></td>
                        <td class="<?php echo ($row['status'] == 'completed') ? 'text-success' : (($row['status'] == 'pending') ? 'text-danger' : 'text-warning'); ?> fw-medium">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#responseModal<?php echo $row['request_id']; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                                </svg>
                                Respond
                            </button>
                        </td>
                    </tr>

                    <!-- Response Modal -->
                    <div class="modal fade" id="responseModal<?php echo $row['request_id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Respond to Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                        <input type="hidden" name="tenant_email" value="<?php echo htmlspecialchars($row['user_email']); ?>">
                                        <input type="hidden" name="_next" value="http://localhost/rent-master2/admin/">
                                        <input type="hidden" name="_subject" value="Maintenance Request Update">
                                        <input type="hidden" name="_captcha" value="false">
                                        <div class="mb-3">
                                            <label class="form-label">Maintenance Status</label>
                                            <select class="form-select" name="status" required>
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Response Message</label>
                                            <textarea class="form-control" name="admin_message" rows="4" required placeholder="Enter details about the maintenance schedule or reason for rejection"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.5.5 0 0 1-.928.086L7.5 12.5l-4.486 2.904a.5.5 0 0 1-.778-.416l.004-14.59a.5.5 0 0 1 .596-.479l14.5 3.5a.5.5 0 0 1 .028.967z"/>
                                            </svg>
                                            Send Response
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class='text-center text-bg-warning'>No maintenance requests found</div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
