<?php

// Database connection with error handling
require_once '../database/config.php';

// Check if user is logged in and is a tenant
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: /rent-master2/client/?page=src/login");
    exit();
}

// Get tenant information
$tenant_sql = "SELECT u.*, t.*, p.* FROM tenants t 
               JOIN properties p ON t.property_id = p.property_id
               JOIN users u ON t.user_id = u.user_id
               WHERE t.user_id = $user_id AND t.tenant_status = 'active'";
$tenant_result = mysqli_query($conn, $tenant_sql);
$tenant_data = mysqli_fetch_assoc($tenant_result);

// Get latest payment information
$payment_sql = "SELECT * FROM payments 
                WHERE tenant_id = {$tenant_data['tenant_id']} 
                ORDER BY payment_date DESC LIMIT 1";
$payment_result = mysqli_query($conn, $payment_sql);
$payment_data = mysqli_fetch_assoc($payment_result);

// Handle testimonial submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Create testimonial table if not exists
    $create_table = "CREATE TABLE IF NOT EXISTS testimonials (
        testimonial_id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        property_id INT NOT NULL,
        rating INT NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id),
        FOREIGN KEY (property_id) REFERENCES properties(property_id)
    )";
    mysqli_query($conn, $create_table);
    
    // Insert testimonial
    $insert_testimonial = "INSERT INTO testimonials (tenant_id, property_id, rating, comment)
                          VALUES ({$tenant_data['tenant_id']}, {$tenant_data['property_id']}, $rating, '$comment')";
    
    if (mysqli_query($conn, $insert_testimonial)) {
        $testimonial_message = "Thank you for your feedback!";
    } else {
        $testimonial_message = "Error submitting testimonial: " . mysqli_error($conn);
    }
    
    // Redirect to home after submission
    header("Location: /rent-master2/client/?page=src/home");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .confirmation-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
        }
        .checkmark {
            width: 100px;
            height: 100px;
            background-color: #2b8a3e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .rating-star:hover {
            transform: scale(1.2);
        }
        .property-img {
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 2rem 0;
        }
        .rating-star {
            font-size: 2rem;
            color: lightgray; /* Default color (gray) */
            cursor: pointer;
            transition: color 0.2s;
        }

        /* When a star is selected, color all previous stars */
        input[name="rating"]:checked ~ .rating-star {
            color: lightgray; /* reset if no radio before label */
        }

        /* Select stars before and including the selected one */
        input[name="rating"]:checked + label,
        input[name="rating"]:checked + label ~ label {
            color: gold;
        }

    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="confirmation-card card p-4 p-md-5 mb-4">
                <div class="text-center">
                    <div class="checkmark">
                        <svg width="48" height="48" viewBox="0 0 16 16" fill="white">
                            <path d="M13.485 1.431a1.473 1.473 0 0 1 2.104 2.062l-7.84 9.801a1.473 1.473 0 0 1-2.12.04L.431 8.138a1.473 1.473 0 0 1 2.084-2.083l4.111 4.112 6.82-8.69a.486.486 0 0 1 .04-.045z"/>
                        </svg>
                    </div>
                    <h2 class="mb-3">Payment Confirmed!</h2>
                    <p class="text-muted mb-4">Thank you for your payment. Here's your receipt.</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Tenant Information</h5>
                                <hr>
                                <p><strong>Name:</strong> <?= htmlspecialchars($tenant_data['user_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($tenant_data['user_email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($tenant_data['user_phone_number']) ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($tenant_data['user_address']) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Property Information</h5>
                                <hr>
                                <p><strong>Property:</strong> <?= htmlspecialchars($tenant_data['property_name']) ?></p>
                                <p><strong>Location:</strong> <?= htmlspecialchars($tenant_data['property_location']) ?></p>
                                <p><strong>Rent Amount:</strong> ₱<?= number_format($tenant_data['property_rental_price'], 2) ?></p>
                                <p><strong>Description:</strong> <?= htmlspecialchars($tenant_data['property_description']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Payment Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Payment ID:</strong> <?= $payment_data['payment_id'] ?></p>
                                <p><strong>Payment Date:</strong> <?= date('M j, Y', strtotime($payment_data['payment_date'])) ?></p>
                                <p><strong>Payment Method:</strong> <?= $payment_data['payment_method'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Period:</strong> <?= date('M j, Y', strtotime($payment_data['payment_start_date'])) ?> to <?= date('M j, Y', strtotime($payment_data['payment_end_date'])) ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-success"><?= $payment_data['payment_status'] ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="text-center">
                    <h4 class="mb-4">How was your experience?</h4>
                    <p class="text-muted mb-4">We'd love to hear your feedback about your rental experience</p>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <div class="d-flex justify-content-center mb-2" id="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" style="display: none;" required>
                                    <label for="star<?= $i ?>" class="rating-star">★</label>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted">Click to rate</small>
                        </div>
                        
                        <div class="mb-4">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Share your experience (optional)"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="submit_testimonial" class="btn btn-primary">Submit Feedback</button>
                            <a href="/rent-master2/client/?page=src/home.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </form>
                    
                    <?php if (isset($testimonial_message)): ?>
                        <div class="alert alert-success mt-3"><?= $testimonial_message ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Star rating functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-star');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('for').replace('star', '');
                
                // Reset all stars
                stars.forEach(s => {
                    s.style.color = '#ffc107';
                    s.style.opacity = '0.5';
                });
                
                // Color stars up to the selected one
                for (let i = 1; i <= rating; i++) {
                    const star = document.querySelector(`label[for="star${i}"]`);
                    star.style.opacity = '1';
                }
                
                // Set the hidden input value
                document.querySelector(`#star${rating}`).checked = true;
            });
        });
    });
</script>
</body>
</html>