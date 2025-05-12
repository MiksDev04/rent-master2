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

// Fetch payment records with property price
$sql = "SELECT p.payment_id, p.tenant_id, t.property_id, pr.property_rental_price, p.payment_start_date, p.payment_end_date, p.payment_status 
        FROM payments p
        INNER JOIN tenants t ON p.tenant_id = t.tenant_id
        INNER JOIN properties pr ON t.property_id = pr.property_id
        ORDER BY p.payment_id DESC";
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
                    <?php while ($row = $result->fetch_assoc()): ?>
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
                                <a href="?page=payments/paid&payment_id=<?php echo htmlspecialchars($row['payment_id']); ?>" class="btn btn-sm btn-secondary" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" fill="currentColor" viewBox="0 0 576 512">
                                        <path d="M572.5 241.4C518.4 135.5 407.5 64 288 64S57.6 135.5 3.5 241.4a48.1 48.1 0 0 0 0 29.2C57.6 376.5 168.5 448 288 448s230.4-71.5 284.5-177.4a48.1 48.1 0 0 0 0-29.2zM288 400c-97 0-189.6-56.1-238.5-144C98.4 168.1 191 112 288 112s189.6 56.1 238.5 144C477.6 343.9 385 400 288 400zm0-240a96 96 0 1 0 96 96 96 96 0 0 0-96-96z" />
                                    </svg>
                                </a>

                                <a href="?page=payments/update&payment_id=<?php echo $row['payment_id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" fill="currentColor" viewBox="0 0 512 512">
                                        <path d="M362.7 19.3c-12.5-12.5-32.8-12.5-45.3 0l-36.7 36.7 90.5 90.5 36.7-36.7c12.5-12.5 12.5-32.8 0-45.3L362.7 19.3zM237.5 138.7L45.3 330.9c-6 6-10.6 13.5-13.1 21.7L.5 478.1c-2.8 9.4-.1 19.5 7.1 26.6s17.2 9.9 26.6 7.1l125.6-31.6c8.2-2.1 15.7-6.7 21.7-13.1l192.2-192.2-90.5-90.5z" />
                                    </svg>
                                </a>

                                <a href="?page=payments/delete&payment_id=<?php echo $row['payment_id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this payment?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" fill="currentColor" viewBox="0 0 448 512">
                                        <path d="M135.2 17.7C140.2 7.1 150.9 0 162.7 0h122.6c11.8 0 22.5 7.1 27.5 17.7L328 32h88c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8l-20.2 363.6c-1.5 26.6-23.5 46.4-50.1 46.4H110.3c-26.6 0-48.6-19.8-50.1-46.4L40 80h-8c-13.3 0-24-10.7-24-24S18.7 32 32 32h88l15.2-14.3zM176 432c13.3 0 24-10.7 24-24V208c0-13.3-10.7-24-24-24s-24 10.7-24 24v200c0 13.3 10.7 24 24 24zm96 0c13.3 0 24-10.7 24-24V208c0-13.3-10.7-24-24-24s-24 10.7-24 24v200c0 13.3 10.7 24 24 24z" />
                                    </svg>
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