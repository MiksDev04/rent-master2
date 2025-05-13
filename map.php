<?php
// Database connection using mysqli
$host = '127.0.0.1';
$dbname = 'rentsystem';
$username = 'root'; // Change as needed
$password = ''; // Change as needed

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get monthly income data
$monthlyIncomeQuery = "
    SELECT 
        MONTH(p.payment_date) AS month,
        SUM(pr.property_rental_price) AS total_income
    FROM payments p
    JOIN tenants t ON p.tenant_id = t.tenant_id
    JOIN properties pr ON t.property_id = pr.property_id
    WHERE p.payment_status = 'Paid' AND p.payment_date IS NOT NULL
    GROUP BY MONTH(p.payment_date)
    ORDER BY MONTH(p.payment_date)
";

$monthlyIncomeResult = mysqli_query($conn, $monthlyIncomeQuery);
$monthlyIncomeData = [];
if ($monthlyIncomeResult) {
    while ($row = mysqli_fetch_assoc($monthlyIncomeResult)) {
        $monthlyIncomeData[] = $row;
    }
}

// Get yearly total income
$yearlyIncomeQuery = "
    SELECT SUM(pr.property_rental_price) AS yearly_total
    FROM payments p
    JOIN tenants t ON p.tenant_id = t.tenant_id
    JOIN properties pr ON t.property_id = pr.property_id
    WHERE p.payment_status = 'Paid' AND p.payment_date IS NOT NULL
    AND YEAR(p.payment_date) = YEAR(CURRENT_DATE())
";

$yearlyIncomeResult = mysqli_query($conn, $yearlyIncomeQuery);
$yearlyIncomeRow = mysqli_fetch_assoc($yearlyIncomeResult);
$yearlyIncome = $yearlyIncomeRow['yearly_total'] ?? 0;

// Prepare data for chart
$months = [
    1 => 'Jan',
    2 => 'Feb',
    3 => 'Mar',
    4 => 'Apr',
    5 => 'May',
    6 => 'Jun',
    7 => 'Jul',
    8 => 'Aug',
    9 => 'Sep',
    10 => 'Oct',
    11 => 'Nov',
    12 => 'Dec'
];

$incomeByMonth = array_fill(1, 12, 0);
foreach ($monthlyIncomeData as $data) {
    $incomeByMonth[$data['month']] = (float)$data['total_income'];
}

// Find max value for chart scaling
$maxIncome = max($incomeByMonth) ?: 1; // Avoid division by zero
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

</head>

<body>
    <div class="container py-4">
        <h2 class="mb-4 accent-blue">Income Report</h2>

        <div class="row">
            <div class="col col-lg-3 col-12">
                <div class="card mb-4" style="border-left: 4px solid #0d6efd;">
                    <div class="card-header d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd" class="bi bi-cash-stack me-2" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z" />
                        </svg>
                        <h5 class="mb-0">Yearly Total Income</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title accent-blue" id="yearly-total">₱0.00</h3>
                        <p class="card-text text-muted">Total rental income for <span id="current-year"></span></p>
                    </div>
                </div>
            </div>
            <!-- Yearly Total Income Card -->

            <div class="col col-lg-9 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd" class="bi bi-graph-up me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z"/>
                        </svg>
                        <h5 class="mb-0">Monthly Income</h5>
                    </div>
                    <div class="card-body position-relative">
                        <button class="btn btn-sm btn-outline-primary position-absolute z-3" style="top: 10px; right: 10px" id="download-chart">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                            </svg>
                            <span class="d-none d-lg-inline">
                                Download Chart  
                            </span>
                        </button>
                        <div class="chart-container position-relative" style="height: 300px;">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Monthly Income Chart -->
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PHP data would be passed here in the actual implementation
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            // This would be populated from PHP in the actual implementation
            // Sample data for demonstration
            const incomeData = [
                <?php echo implode(',', array_values($incomeByMonth)); ?>
            ];
            
            const yearlyTotal = <?php echo $yearlyIncome ?: 0; ?>;
            const currentYear = new Date().getFullYear();
            
            // Update the yearly total and year
            document.getElementById('yearly-total').textContent = `₱${formatNumber(yearlyTotal)}`;
            document.getElementById('current-year').textContent = currentYear;
            
            // Create the chart
            const ctx = document.getElementById('incomeChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Income',
                        data: incomeData,
                        fill: false,
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        tension: 0.4,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: `Monthly Rental Income (${currentYear})`,
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `₱${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return `₱${value/1000}k`;
                                }
                            },
                            title: {
                                display: true,
                                text: 'Income (₱)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
            
            // Handle download button click
            document.getElementById('download-chart').addEventListener('click', function() {
                // Create a temporary link
                const link = document.createElement('a');
                link.download = `income-report-${currentYear}.png`;
                link.href = document.getElementById('incomeChart').toDataURL('image/png');
                link.click();
            });
            
            // Number formatting helper
            function formatNumber(num) {
                return new Intl.NumberFormat('en-PH', { 
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2 
                }).format(num);
            }
        });
    </script>
</body>

</html>