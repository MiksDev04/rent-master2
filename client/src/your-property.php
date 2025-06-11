<?php
// Database connection with error handling
require_once '../database/config.php';

$user_id = $_SESSION['user_id'] ?? null;
$is_tenant = false;
$tenant_id = null;


// Check if user is a tenant
if ($user_id) {
    $tenant_check_sql = "SELECT * FROM tenants WHERE user_id = $user_id AND tenant_status = 'active'";
    $tenant_result = mysqli_query($conn, $tenant_check_sql);
    if ($tenant_result && mysqli_num_rows($tenant_result) > 0) {
        $tenant_data = mysqli_fetch_assoc($tenant_result);
        $is_tenant = true;
        $tenant_id = $tenant_data['tenant_id'];
    }
}

// Get payment information for the tenant
$payment_info = [];
if ($tenant_id) {
    // Get the current date
    $current_date = date('Y-m-d');

    // SQL query to fetch the most recent payment record for the tenant
    $payment_sql = "SELECT p.*, pr.*
                    FROM payments AS p
                    JOIN tenants AS t
                    ON p.tenant_id = t.tenant_id
                    JOIN properties AS pr
                    ON t.property_id = pr.property_id
                    WHERE t.tenant_id = $tenant_id  ORDER BY p.payment_date DESC LIMIT 1";
    $payment_result = mysqli_query($conn, $payment_sql);

    // Check if the query returns any results
    if ($payment_result && mysqli_num_rows($payment_result) > 0) {
        // Fetch the payment record
        $payment_info = mysqli_fetch_assoc($payment_result);

        // Get the payment start and end dates from the fetched record
        $payment_start_date = $payment_info['payment_start_date'];
        $payment_end_date = $payment_info['payment_end_date'];
    }
}


// Handle payment submission
$payment_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $method = $_POST['payment_method'] ?? "";
    if (!$method) {
        $payment_message = "Please select a payment method.";
    } else {
        // Update the most recent pending payment for this tenant
        $update_payment = "UPDATE payments 
                            SET payment_status = 'Paid', 
                                payment_date = CURDATE(), 
                                payment_method = '$method'
                            WHERE tenant_id = $tenant_id 
                            AND payment_status IN ('Pending', 'Overdue')
                            ORDER BY payment_date DESC 
                            LIMIT 1;
                            ";

        if (mysqli_query($conn, $update_payment)) {
            if (mysqli_affected_rows($conn) > 0) {
                // Use payment_id from earlier fetched $payment_info
                $payment_id = $payment_info['payment_id'];

                $message = "Payment received for property: {$payment_info['property_name']}. Status: Paid.";

                $notification_sql = "INSERT INTO notifications (user_id, type, message, related_id) 
                             VALUES ($user_id, 'payment', '$message', $payment_id)";
                mysqli_query($conn, $notification_sql);

                $payment_message = "You paid for this month";


                header("Location: /rent-master2/client/?page=src/rating-property");
                exit;
            } else {
                $payment_message = "No pending payments found to update.";
            }
        } else {
            $payment_message = "Error processing payment: " . mysqli_error($conn);
        }
    }
}

// Handle maintenance request
$maintenance_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request']) && $tenant_id) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $insert_maintenance = "INSERT INTO maintenance_requests (tenant_id, category, description, request_date, status)
                           VALUES ($tenant_id, '$category', '$description', NOW(), 'pending')";

    if (mysqli_query($conn, $insert_maintenance)) {
        $request_id = mysqli_insert_id($conn);
        $maintenance_message = "Your maintenance request has been submitted successfully.";
        // Add notification for property owner 

        $property_sql = "SELECT p.property_name, t.tenant_id
                    FROM properties AS p
                    JOIN tenants AS t ON p.property_id = t.property_id
                    WHERE t.tenant_id = $tenant_id";
        $property_result = mysqli_query($conn, $property_sql);


        if ($property_row = mysqli_fetch_assoc($property_result)) {
            $message = "New maintenance request received for property: {$property_row['property_name']}. Status: Pending";

            $notification_sql = "INSERT INTO notifications (user_id, type, message, related_id) 
                         VALUES ($user_id, 'maintenance', '$message', $request_id)";
            mysqli_query($conn, $notification_sql);
        } else {
            // Optional: handle case where property/tenant not found
            echo "Error: Property not found for tenant.";
        }
    } else {
        $maintenance_message = "Error submitting request: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #ffffff;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .btn-custom {
            border-radius: 50px;
            padding: 0.75rem 1.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-success {
            background-color: #2b8a3e;
        }

        .btn-primary {
            background-color: #1971c2;
        }

        .btn-success:hover,
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        h2 {
            color: #212529;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        h2:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: #adb5bd;
            border-radius: 3px;
        }

        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #868e96;
            box-shadow: 0 0 0 3px rgba(134, 142, 150, 0.15);
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-warning {
            background-color: #fff3bf;
            color: #5c4b00;
        }

        .alert-success {
            background-color: #ebfbee;
            color: #2b8a3e;
        }

        .alert-info {
            background-color: #e7f5ff;
            color: #1971c2;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #343a40;
        }

        .row-cols-lg-2>.col {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid #dee2e6;
            position: relative;
        }

        .payment-option:hover {
            background-color: #e9ecef;
        }

        .payment-option.selected {
            border-color: #1971c2;
            background-color: #e7f5ff;
            box-shadow: 0 0 0 1px #1971c2;
        }

        .payment-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .payment-label {
            font-weight: 500;
            color: #343a40;
        }

        .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .payment-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-details p {
            margin-bottom: 0.5rem;
        }

        .payment-details strong {
            color: #212529;
        }

        .highlight-period {
            background-color: #fff3bf;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .highlight-amount {
            background-color: #d3f9d8;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .urgent-payment {
            font-size: 1.25rem;
            font-weight: 600;
            color: #c92a2a;
            background-color: #ffc9c9;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container py-5">

        <?php if (!$is_tenant): ?>
            <div class="alert alert-warning text-center shadow-sm mb-5">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                </svg>
                You need to rent a property first to access this information.
            </div>
            <div class=" d-flex justify-content-center">
                <img src="/rent-master2/client/assets/icons/undraw_home-settings_lw7v.png" style="max-width: 500px;" class=" w-100" alt="Rent a house first">
            </div>
        <?php else: ?>

            <div class="row row-cols-1 row-cols-lg-2 g-4">
                <div class="col">
                    <?php if (!empty($payment_info)): ?>
                        <!-- Payment Section -->
                        <div class="card p-4 h-100">

                            <?php if ($payment_message): ?>
                                <div class="alert alert-success mb-4">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                    </svg>
                                    <?= $payment_message ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($payment_info['payment_status'] != 'Paid'): ?>
                                <h2>Make a Payment</h2>
                                <div class="payment-details mb-4">
                                    <p><strong>Payment Status:</strong> <?= $payment_info['payment_status'] ?></p>
                                    <p><strong>Period:</strong> <span class="highlight-period"><?= date('M j, Y', strtotime($payment_info['payment_start_date'])) ?> to <?= date('M j, Y', strtotime($payment_info['payment_end_date'])) ?></span></p>
                                    <p><strong>Amount Due:</strong> <span class="highlight-amount">PHP <?php echo number_format(htmlspecialchars($payment_info['property_rental_price']), 2, '.', ',') ?></span></p>

                                    <?php
                                    $due_date = new DateTime($payment_info['payment_end_date']);
                                    $today = new DateTime();
                                    $days_remaining = $due_date->diff($today)->days;

                                    if ($days_remaining <= 5 && $payment_info['payment_status'] == 'Pending'): ?>
                                        <div class="urgent-payment">
                                            WARNING: You have <?= $days_remaining ?> day/s left to pay or your contract will be terminated!
                                        </div>
                                    <?php elseif ($payment_info['payment_status'] == 'Overdue'): ?>
                                        <div class="urgent-payment">
                                            WARNING: Your payment is overdue by <?= abs($days_remaining) ?> day/s. Immediate action required to avoid termination!
                                        </div>
                                    <?php endif; ?>

                                </div>
                                <form method="POST" class=" mb-4">
                                    <div class="row g-3 row-cols-lg-2 row-cols-1 mb-4">
                                        <div class="col">
                                            <label class="payment-option d-block">
                                                <div class="d-flex align-items-center">
                                                    <svg class="payment-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill="#00A67E" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z" />
                                                    </svg>
                                                    <span class="payment-label">GCash</span>
                                                </div>
                                                <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="GCash">
                                            </label>
                                        </div>
                                        <div class="col">
                                            <label class="payment-option d-block">
                                                <div class="d-flex align-items-center">
                                                    <svg class="payment-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill="#6F2C91" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                                                    </svg>
                                                    <span class="payment-label">Maya</span>
                                                </div>
                                                <input class="form-check-input" type="radio" name="payment_method" id="maya" value="Maya">
                                            </label>
                                        </div>
                                        <div class="col">
                                            <label class="payment-option d-block">
                                                <div class="d-flex align-items-center">
                                                    <svg class="payment-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill="#1A1F71" d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                                    </svg>
                                                    <span class="payment-label">Debit/Credit Card</span>
                                                </div>
                                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Credit/Debit Card">
                                            </label>
                                        </div>
                                        <div class="col">
                                            <label class="payment-option d-block">
                                                <div class="d-flex align-items-center">
                                                    <svg class="payment-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill="#0072CE" d="M5 14h14v-2H5v2zm0 4h14v-2H5v2zm0-8h14V8H5v2zm-2-4v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2z" />
                                                    </svg>
                                                    <span class="payment-label">Bank Transfer</span>
                                                </div>
                                                <input class="form-check-input" type="radio" name="payment_method" id="bank" value="Bank Transfer">
                                            </label>
                                        </div>
                                        <div class="col">
                                            <label class="payment-option d-block">
                                                <div class="d-flex align-items-center">
                                                    <svg class="payment-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill="#FF9900" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                                                    </svg>
                                                    <span class="payment-label">Coins.ph</span>
                                                </div>
                                                <input class="form-check-input" type="radio" name="payment_method" id="coins" value="Coins.ph">
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_payment" class="btn btn-success btn-custom w-100 mt-2">Submit Payment</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($payment_info['payment_status'] == 'Paid'): ?>
                                <h2>You've already paid for this month</h2>
                            <?php endif; ?>



                            <?php
                            // Get tenant's property information and move-in date
                            $property_sql = "SELECT p.*, t.tenant_date_created
                                FROM properties p
                                JOIN tenants t ON p.property_id = t.property_id
                                WHERE t.tenant_id = $tenant_id";
                            $property_result = mysqli_query($conn, $property_sql);
                            $property_info = mysqli_fetch_assoc($property_result);

                            // Calculate how long tenant has been living there
                            $move_in_date = new DateTime($property_info['tenant_date_created']);
                            $today = new DateTime();
                            $tenancy_duration = $move_in_date->diff($today);
                            ?>

                            <div class="property-info mb-4">
                                <h4>Your Current Residence</h4>
                                <p><strong>Property:</strong> <?= $property_info['property_name'] ?></p>
                                <p><strong>Address:</strong> <?= $property_info['property_location'] ?></p>
                                <p><strong>Move-in Date:</strong> <?= date('M j, Y', strtotime($property_info['tenant_date_created'])) ?></p>
                                <p><strong>Duration:</strong>
                                    <?= $tenancy_duration->y ?> years,
                                    <?= $tenancy_duration->m ?> months,
                                    <?= $tenancy_duration->d ?> days
                                </p>
                            </div>

                            <img src="/rent-master2/client/assets/icons/undraw_online-payments_p97e.png" alt="Payment already made" class="img-fluid">
                        </div>

                    <?php else : ?>
                        <div class="card p-4 h-100">
                            <h2>Tenant created. Awaiting payment processing.</h2>


                            <?php
                            // Get tenant's property information and move-in date
                            $property_sql = "SELECT p.*, t.tenant_date_created
                         FROM properties p
                         JOIN tenants t ON p.property_id = t.property_id
                         WHERE t.tenant_id = $tenant_id";
                            $property_result = mysqli_query($conn, $property_sql);
                            $property_info = mysqli_fetch_assoc($property_result);

                            // Calculate how long tenant has been living there
                            $move_in_date = new DateTime($property_info['tenant_date_created']);
                            $today = new DateTime();
                            $tenancy_duration = $move_in_date->diff($today);
                            ?>

                            <div class="property-info mb-4">
                                <h4>Your Current Residence</h4>
                                <p><strong>Property:</strong> <?= $property_info['property_name'] ?></p>
                                <p><strong>Address:</strong> <?= $property_info['property_location'] ?></p>
                                <p><strong>Move-in Date:</strong> <?= date('M j, Y', strtotime($property_info['tenant_date_created'])) ?></p>
                                <p><strong>Duration:</strong>
                                    <?= $tenancy_duration->y ?> years,
                                    <?= $tenancy_duration->m ?> months,
                                    <?= $tenancy_duration->d ?> days
                                </p>
                            </div>

                            <img src="/rent-master2/client/assets/icons/undraw_online-payments_p97e.png" alt="Payment already made" class="img-fluid">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col">
                    <!-- Maintenance Section -->
                    <div class="card p-4">
                        <h2>Request for Maintenance</h2>

                        <?php if ($maintenance_message): ?>
                            <div class="alert alert-info mb-4">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                                </svg>
                                <?= $maintenance_message ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="maintenance-form">
                            <div class="mb-4">
                                <label for="category" class="form-label">Maintenance Category</label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="">Select category</option>
                                    <option value="Plumbing">Plumbing</option>
                                    <option value="Electrical">Electrical</option>
                                    <option value="Appliance Repair">Appliance Repair</option>
                                    <option value="Structural">Structural</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="form-label">Request Description</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Describe the issue in detail..." required></textarea>
                            </div>
                            <button type="submit" name="submit_request" id="maintenance-btn" class="btn btn-primary btn-custom w-100">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
    <script>
        // Handle payment method selection
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('maintenance-form');
            const submitBtn = document.getElementById('maintenance-btn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            });
            const paymentOptions = document.querySelectorAll('.payment-option');

            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));

                    // Add selected class to clicked option
                    this.classList.add('selected');

                    // Check the corresponding radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;
                });
            });
        });
    </script>
</body>

</html>