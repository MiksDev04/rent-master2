<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
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
        <a href="?page=tenants/index" class="p-2 rounded-circle bg-dark-subtle">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
            </svg>
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