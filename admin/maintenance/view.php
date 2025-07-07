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

if (!isset($_GET['request_id'])) {
    die("Request ID not specified");
}

$request_id = $_GET['request_id'];

// Fetch maintenance request details
$request_sql = "SELECT m.*, u.user_email , u.user_name, u.user_phone_number
                FROM maintenance_requests m
                JOIN tenants t ON m.tenant_id = t.tenant_id
                JOIN users u ON u.user_id = t.user_id
                WHERE m.request_id = '$request_id'";
$request_result = $conn->query($request_sql);
$request_details = $request_result->fetch_assoc();

// Fetch property details only
$tenant_id = $request_details['tenant_id'];
$property_sql = "SELECT p.property_name, p.property_location
                 FROM tenants t
                 JOIN properties p ON p.property_id = t.property_id
                 WHERE t.tenant_id = '$tenant_id'";
$property_result = $conn->query($property_sql);
$property_details = $property_result->fetch_assoc();
?>

<div class="container py-4 px-lg-5">
    <header class="d-flex gap-2 align-items-center mb-3">
        <a href="index.php?page=maintenance/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4>Maintenance Request Details</h4>
    </header>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            
            <div class="mb-2">
                <span class="fw-bold">Status:</span>
                <span class="badge <?php echo ($request_details['status'] == 'completed') ? 'bg-success' : (($request_details['status'] == 'pending') ? 'bg-danger' : 'bg-warning'); ?>">
                    <?php echo htmlspecialchars($request_details['status']); ?>
                </span>
            </div>
            
            <div class="mb-2">
                <span class="fw-bold">Category:</span>
                <?php echo htmlspecialchars($request_details['category']); ?>
            </div>

             <div class="mb-2">
                <span class="fw-bold">Tenant Name:</span>
                <?php echo htmlspecialchars($request_details['user_name']); ?>
            </div>
            
             <div class="mb-2">
                <span class="fw-bold">Tenant Phone Number:</span>
                <?php echo htmlspecialchars($request_details['user_phone_number']); ?>
            </div>

            <div class="mb-2">
                <span class="fw-bold">Tenant Email:</span>
                <?php echo htmlspecialchars($request_details['user_email']); ?>
            </div>
            
            <div class="mb-3">
                <span class="fw-bold">Description:</span>
                <?php echo htmlspecialchars($request_details['description']); ?>
            </div>
            
            <div class="alert alert-light">
                <h6 class="alert-heading">Property Information</h6>
                <hr>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($property_details['property_name']); ?></p>
                <p class="mb-0"><strong>Location:</strong> <?php echo htmlspecialchars($property_details['property_location']); ?></p>
            </div>
        </div>
    </div>
</div>
