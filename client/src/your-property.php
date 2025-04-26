<?php
session_start();

// Database connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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

// Handle payment submission
$payment_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $method = $_POST['payment_method'] ?? "";
    if (!$method) {
        $payment_message = "Please select a payment method.";
    } else {
        $payment_message = "Thank you! Your payment via {$method} has been received. Please proceed as instructed.";
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
        $maintenance_message = "Your maintenance request has been submitted successfully.";
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #495057;
        }
        .container {
            max-width: 1200px;
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
        .btn-success:hover, .btn-primary:hover {
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
        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
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
        .row-cols-lg-2 > .col {
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
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="container py-5">

    <?php if (!$is_tenant): ?>
        <div class="alert alert-warning text-center shadow-sm mb-5">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>
            You need to rent a property first to access this information.
        </div>
        <div class=" d-flex justify-content-center"> 
            <img src="/rent-master2/client/assets/icons/undraw_home-settings_lw7v.png" style="max-width: 500px;" class=" w-100" alt="Rent a house first" >
        </div>
    <?php else: ?>

        <div class="row row-cols-1 row-cols-lg-2 g-4">
            <div class="col">
                <!-- Payment Section -->
                <div class="card p-4 h-100">
                    <h2>Make a Payment</h2>
        
                    <?php if ($payment_message): ?>
                        <div class="alert alert-success mb-4">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                            <?= $payment_message ?>
                        </div>
                    <?php endif; ?>
        
                    <form method="POST">
                        <div class="row g-3 row-cols-lg-2 row-cols-1 mb-4">
                            <div class="col">
                                <label class="payment-option d-block">
                                    <div class="d-flex align-items-center">
                                        <svg class="payment-icon" viewBox="0 0 24 24">
                                            <path fill="#00A67E" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
                                        </svg>
                                        <span class="payment-label">GCash</span>
                                    </div>
                                    <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="GCash">
                                </label>
                            </div>
                            <div class="col">
                                <label class="payment-option d-block">
                                    <div class="d-flex align-items-center">
                                        <svg class="payment-icon" viewBox="0 0 24 24">
                                            <path fill="#6F2C91" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                        </svg>
                                        <span class="payment-label">Maya</span>
                                    </div>
                                    <input class="form-check-input" type="radio" name="payment_method" id="maya" value="Maya">
                                </label>
                            </div>
                            <div class="col">
                                <label class="payment-option d-block">
                                    <div class="d-flex align-items-center">
                                        <svg class="payment-icon" viewBox="0 0 24 24">
                                            <path fill="#1A1F71" d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                        </svg>
                                        <span class="payment-label">Debit/Credit Card</span>
                                    </div>
                                    <input class="form-check-input" type="radio" name="payment_method" id="card" value="Debit/Credit Card">
                                </label>
                            </div>
                            <div class="col">
                                <label class="payment-option d-block">
                                    <div class="d-flex align-items-center">
                                        <svg class="payment-icon" viewBox="0 0 24 24">
                                            <path fill="#0072CE" d="M5 14h14v-2H5v2zm0 4h14v-2H5v2zm0-8h14V8H5v2zm-2-4v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2z"/>
                                        </svg>
                                        <span class="payment-label">Bank Transfer</span>
                                    </div>
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank" value="Bank Transfer">
                                </label>
                            </div>
                            <div class="col">
                                <label class="payment-option d-block">
                                    <div class="d-flex align-items-center">
                                        <svg class="payment-icon" viewBox="0 0 24 24">
                                            <path fill="#28A745" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                                        </svg>
                                        <span class="payment-label">Cash</span>
                                    </div>
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="Cash">
                                </label>
                            </div>
                        </div>
                        <button type="submit" name="submit_payment" class="btn btn-success btn-custom w-100 mt-2">Submit Payment</button>
                    </form>
                </div>
            </div>
            
            <div class="col">
                <!-- Maintenance Section -->
                <div class="card p-4 h-100">
                    <h2>Request for Maintenance</h2>
        
                    <?php if ($maintenance_message): ?>
                        <div class="alert alert-info mb-4">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                            <?= $maintenance_message ?>
                        </div>
                    <?php endif; ?>
        
                    <form method="POST">
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
                        <button type="submit" name="submit_request" class="btn btn-primary btn-custom w-100">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<script>
    // Handle payment method selection
    document.addEventListener('DOMContentLoaded', function() {
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