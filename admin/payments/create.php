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

// Initialize variables
$errors = [];
$tenant_id = '';

// Fetch active tenants with their properties
$tenants_sql = "SELECT t.tenant_id, t.user_id, t.property_id, p.property_name, p.property_rental_price, u.user_name
                FROM tenants t
                INNER JOIN properties p ON t.property_id = p.property_id
                INNER JOIN users u ON t.user_id = u.user_id
                WHERE t.tenant_status = 'active'";
$tenants_result = $conn->query($tenants_sql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tenant_id'])) {
    $tenant_id = intval($_POST['tenant_id']);

    // Get the latest paid payment for the tenant
    $query = "SELECT payment_end_date FROM payments 
              WHERE tenant_id = $tenant_id AND payment_status = 'Paid' 
              ORDER BY payment_end_date DESC LIMIT 1";
    $payment_result = mysqli_query($conn, $query);

    if ($payment_result && mysqli_num_rows($payment_result) > 0) {
        $payment_row = mysqli_fetch_assoc($payment_result);
        $payment_end_date = $payment_row['payment_end_date'];

        $next_start_date = date('Y-m-d', strtotime($payment_end_date . ' +1 day'));
        $next_end_date = date('Y-m-d', strtotime($next_start_date . ' +1 month -1 day'));

        // Insert the next month's payment record with payment_date as NULL
        $insert_query = "INSERT INTO payments (tenant_id, payment_start_date, payment_end_date, payment_status, payment_date, payment_method)
                         VALUES ($tenant_id, '$next_start_date', '$next_end_date', 'Pending', NULL, NULL)";

        if (mysqli_query($conn, $insert_query)) {
            header("Location: /rent-master2/admin/?page=payments/index&message=Payment record created successfully! The tenant now has a pending payment.");

            exit;
        } else {
            $errors[] = "Error creating payment record: " . mysqli_error($conn);
        }
    } else {
        // For tenants with no previous payments, create first payment starting today
        $next_start_date = date('Y-m-d');
        $next_end_date = date('Y-m-d', strtotime($next_start_date . ' +1 month -1 day'));

        $insert_query = "INSERT INTO payments (tenant_id, payment_start_date, payment_end_date, payment_status, payment_date, payment_method)
                         VALUES ($tenant_id, '$next_start_date', '$next_end_date', 'Pending', NULL, NULL)";

        if (mysqli_query($conn, $insert_query)) {


            header("Location: /rent-master2/admin/?page=payments/index&message=Payment record created successfully! The tenant now has a pending payment.");
            exit;
        } else {
            $errors[] = "Error creating first payment record: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container px-lg-5">
    <header class="d-flex gap-2 align-items-center mt-3">
         <a href="?page=payments/index" class=" btn btn-sm btn-outline-secondary" width="2rem" height="2rem">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Create Payment Record</h4>
    </header>
    <p class="text-muted card bg-info-subtle p-3 mt-2">This creates the next payment period for the tenant to pay</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>



    <form method="post" class="needs-validation" action="payments/create.php" novalidate>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tenant_id" class="form-label">Select Tenant</label>
                <select class="form-select" id="tenant_id" name="tenant_id" required>
                    <option value="">Select Tenant</option>
                    <?php while ($tenant = $tenants_result->fetch_assoc()): ?>
                        <option value="<?php echo $tenant['tenant_id']; ?>"
                            <?php echo ($tenant_id == $tenant['tenant_id']) ? 'selected' : ''; ?>>
                            Tenant: <?php echo $tenant['user_name']; ?> -
                            Property: <?php echo htmlspecialchars($tenant['property_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">
                    Please select a tenant.
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary rounded-5">Create Payment Record</button>
            <a href="?page=payments/index" class="btn btn-secondary rounded-5">Cancel</a>
        </div>
    </form>
</div>

<script>
    // Client-side validation
    (function() {
        'use strict'

        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<?php $conn->close(); ?>