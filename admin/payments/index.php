<?php
// Database connection 
require_once '../database/config.php';

// Fetch payment records with property price
$sql = "SELECT p.payment_id, p.tenant_id, t.property_id, pr.property_rental_price, p.payment_start_date, p.payment_end_date, p.payment_status 
        FROM payments p
        INNER JOIN tenants t ON p.tenant_id = t.tenant_id
        INNER JOIN properties pr ON t.property_id = pr.property_id
        ORDER BY p.payment_id ASC";
$result = $conn->query($sql);
?>

<div class="container px-lg-5">
    <header class=" d-flex justify-content-between mt-3">
        <h4 class=" fw-medium">Your Payments</h4>
    </header>
    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead class=" table-info ">
                <tr>
                    <th>Payment No.</th>
                    <th>House ID</th>
                    <th>Tenant ID</th>
                    <th colspan="2">Payment Period</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo 'Pay_' . str_pad($row['payment_id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['tenant_id']); ?></td>
                            <td colspan="2">
                                <?php 
                                    echo date('M d, Y', strtotime($row['payment_start_date'])) . " - " . date('M d, Y', strtotime($row['payment_end_date'])); 
                                ?>
                            </td>
                            <td>PHP <?php echo number_format($row['property_rental_price'], 2); ?></td>
                            <td class="<?php echo ($row['payment_status'] == 'Paid') ? 'text-success' : 'text-danger'; ?> fw-medium">
                                <?php echo htmlspecialchars($row['payment_status']); ?>
                            </td>
                            <td>
                                <a href="?page=payments/paid&payment_id=<?php echo htmlspecialchars($row['payment_id']); ?>" class="d-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 13c-3.866 0-7-4.03-7-5s3.134-5 7-5 7 4.03 7 5-3.134 5-7 5z"/>
                                      <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM8 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                    </svg>
                                    View Payment
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class='text-center text-bg-warning'>No record found</div>
    <?php endif; ?>
</div>

<?php $conn->close(); ?>
