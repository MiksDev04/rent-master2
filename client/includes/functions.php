<?php
function reserveNextMonthPayment($conn, $tenant_id) {
    // Get the latest paid payment for the tenant
    $payment_end_date = null; // or an appropriate default value
    $query = "SELECT payment_end_date FROM payments WHERE tenant_id = $tenant_id AND payment_status = 'Paid' ORDER BY payment_end_date DESC LIMIT 1";
    $payment_result = mysqli_query($conn, $query);

    if ($payment_result && mysqli_num_rows($payment_result) > 0) {
        $payment_row = mysqli_fetch_assoc($payment_result);
        $payment_end_date = $payment_row['payment_end_date'];
        
        $next_start_date = date('Y-m-d', strtotime($payment_end_date . ' +1 day'));
        $next_end_date = date('Y-m-d', strtotime($next_start_date . ' +1 month -1 day'));

        // Close the result set
        mysqli_free_result($payment_result);

        // Insert the next month's payment record with payment_date as NULL
        $insert_query = "INSERT INTO payments (tenant_id, payment_start_date, payment_end_date, payment_status, payment_date, payment_method)
                         VALUES ($tenant_id, '$next_start_date', '$next_end_date', 'Pending', NULL, NULL)";
        
        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            // Successfully inserted the payment record
            return true;
        } else {
            // Handle error in inserting record
            return false;
        }
    } else {
        // Handle case where no paid payment record is found for the tenant
        return false;
    }
}
?>
