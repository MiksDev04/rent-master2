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

// If tenant_id is passed in the query, show detailed tenant view
if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];
    $query = "
        SELECT * 
        FROM tenants
        JOIN users ON tenants.user_id = users.user_id
        JOIN properties ON tenants.property_id = properties.property_id
        WHERE tenants.tenant_id = $tenant_id;";
    $result = mysqli_query($conn, $query);

?>

   <?php if ($tenant = mysqli_fetch_assoc($result)) { ?>
<!-- Tenant Detailed View -->
<div class="container px-lg-5 mb-4 px-lg-5 px-md-4 px-sm-3 px-2">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Tenant / View Tenant</h4>
    </header>

    <div class="card mt-3">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="card-body">
                    <h5 class="card-title"><strong>Name:</strong> <?php echo htmlspecialchars($tenant['user_name']); ?></h5>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($tenant['user_description']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($tenant['user_email']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($tenant['user_phone_number']); ?></p>
                    <p><strong>Property:</strong> <?php echo htmlspecialchars($tenant['property_name']); ?> (ID: <?php echo $tenant['property_id']; ?>)</p>
                    <p><strong>Tenant ID:</strong> <?php echo $tenant['tenant_id']; ?></p>
                    <p><strong>Status:</strong> 
                        <?php 
                            // Display status with better readability
                            switch ($tenant['tenant_status']) {
                                case 'pending':
                                    echo "<span class='badge bg-warning'>Pending</span>";
                                    break;
                                case 'active':
                                    echo "<span class='badge bg-success'>Active</span>";
                                    break;
                                case 'terminated':
                                    echo "<span class='badge bg-danger'>Terminated</span>";
                                    break;
                                default:
                                    echo "<span class='badge bg-secondary'>Unknown</span>";
                            }
                        ?>
                    </p>
                    <?php if ($tenant['tenant_status'] == 'terminated'): ?>
                        <p><strong>Terminated At:</strong> <?php echo htmlspecialchars($tenant['tenant_terminated_at']); ?></p>
                    <?php endif; ?>
                    <p><strong>Date Created:</strong> <?php echo htmlspecialchars($tenant['tenant_date_created']); ?></p>
                </div>
            </div>
            <div class="col-lg-6 p-3 d-flex align-items-center justify-content-center">
                <img src="<?php echo htmlspecialchars($tenant['user_image']); ?>" alt="Tenant Image" class="card-img-top rounded-circle img-fluid" style="aspect-ratio:1/1; width: 75%;">
            </div>
        </div>
    </div>
</div>
<?php
    } else {
        echo "<div class='text-center text-bg-warning'>Tenant not found</div>";
        exit;
    }
    mysqli_close($conn);
}
?>
