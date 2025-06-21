<?php
require_once '../database/config.php';
// Start session
$searchTerm = $_GET['searchInput'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$location = $_GET['location'] ?? '';
$searchType = $_GET['search_type'] ?? 'list';



$sql = "SELECT p.*, pi.image1 
        FROM properties p 
        LEFT JOIN property_images pi ON p.property_id = pi.property_id 
        WHERE (p.property_status = 'available' OR p.property_status = 'unavailable') AND p.landlord_id = $landlordId ";

$params = [];
$types = '';

if (isset($_GET['submitted'])) {
    if (!empty($searchTerm)) {
        $sql .= " AND (p.property_name LIKE ? OR p.property_description LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $types .= 'ss';
    }
    if (!empty($minPrice)) {
        $sql .= " AND p.property_rental_price >= ?";
        $params[] = $minPrice;
        $types .= 'd';
    }
    if (!empty($maxPrice)) {
        $sql .= " AND p.property_rental_price <= ?";
        $params[] = $maxPrice;
        $types .= 'd';
    }
    if (!empty($location)) {
        $sql .= " AND p.property_location LIKE ?";
        $params[] = "%$location%";
        $types .= 's';
    }
}

$sql .= " ORDER BY p.property_date_created DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

$propertyTotal = 0;
$tenantTotal = 0;
$paymentTotal = 0;

$propertySql = "SELECT COUNT(*) AS total FROM properties WHERE property_status = 'available' AND landlord_id = $landlordId";
$tenantSql = "SELECT COUNT(*) AS total FROM tenants WHERE tenant_status = 'active' AND landlord_id = $landlordId";
$paymentSql = "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'paid' AND landlord_id = $landlordId";

$propertyTotal = getTotal($conn, $propertySql);
$tenantTotal = getTotal($conn, $tenantSql);
$paymentTotal = getTotal($conn, $paymentSql);

function getTotal($conn, $query)
{
    $sql = $query;
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    return 0;
}


// Get monthly income data
$monthlyIncomeQuery = " SELECT 
                MONTH(p.payment_date) AS month,
                SUM(pr.property_rental_price) AS total_income
                FROM payments p
                JOIN tenants t ON p.tenant_id = t.tenant_id
                JOIN properties pr ON t.property_id = pr.property_id
                WHERE p.payment_status = 'Paid' AND p.payment_date IS NOT NULL AND p.landlord_id = $landlordId
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
$yearlyIncomeQuery = " SELECT SUM(pr.property_rental_price) AS yearly_total
                        FROM payments p
                        JOIN tenants t ON p.tenant_id = t.tenant_id
                        JOIN properties pr ON t.property_id = pr.property_id
                        WHERE p.payment_status = 'Paid' AND p.payment_date IS NOT NULL
                        AND YEAR(p.payment_date) = YEAR(CURRENT_DATE()) AND pr.landlord_id = $landlordId
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


$stmt->close();
$result->free();
?>

<header>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</header>
<div class="container">
    <h4 class="text-center fw-medium mt-3">Welcome Admin</h4>
    <?php if (isset($_GET['message'])): ?>
        <div id="addSuccess" class="alert alert-success alert-dismissible fade show slide-in position-fixed top-0 start-50 translate-middle-x mt-3 shadow" role="alert" style="z-index: 1055; min-width: 300px;">
            <?= htmlspecialchars($_GET['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="container container">
        <h5 class=" mt-4">Overview this Month</h5>
        <hr>
        <div class="px-lg-5 row row-cols-1 row-cols-md-2 row-cols-lg-3 gx-lg-5 gy-3 overviews">
            <div class="col">
                <a href="?page=properties/index" class=" text-decoration-none card h-100  shadow text-white rounded-4 shortcuts" id="houses" onclick="ShowProperties()">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Neighborhood.png" alt="">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1"><?= htmlspecialchars($propertyTotal) ?></h1>
                                <span class="fw-bold">House/s</span>
                                <span>Available</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly rounded-4 bg-danger">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="?page=tenants/index" class=" text-decoration-none card h-100 shadow  text-white rounded-4 shortcuts" id="tenants" onclick="ShowTenants()">
                    <div class="card-body ">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Tenant.png" alt="">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1"><?= htmlspecialchars($tenantTotal) ?></h1>
                                <span class="fw-bold">Tenant/s</span>
                                <span>Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly rounded-4 bg-primary ">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="?page=payments/index" class=" text-decoration-none card  h-100 shadow text-white  rounded-4 shortcuts" id="payments" onclick="ShowPayments()">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Online Payment.png" alt="Payment icon">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1"><?= htmlspecialchars($paymentTotal) ?></h1>
                                <span class="fw-bold">Paid</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly bg-success rounded-4">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>


    </div>
    <div class="container py-4">
        <h5 class=" mt-4">Income Report</h5>
        <hr>
        <div class="container">
            <div class="row">
                <div class="col col-12">
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

                <div class="col col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd" class="bi bi-graph-up me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07Z" />
                            </svg>
                            <h5 class="mb-0">Monthly Income</h5>
                        </div>
                        <div class="card-body position-relative">
                            <button class="btn btn-sm btn-outline-primary position-absolute z-3" style="top: 10px; right: 10px" id="download-chart">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
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
    </div>
    <div class="container">
        <h5 class=" mt-4">Properties Map</h5>
        <hr>
        <section class="search-filter py-2 bg-body-tertiary shadow-sm border-5">
            <div class="container" id="map">
                <form method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>#propertyMap">
                    <input type="hidden" name="map" value="1">
                    <input type="hidden" name="submitted" value="1">
                    <div class="row g-3 align-items-end">
                        <!-- Search Field -->
                        <div class="col-md-3">
                            <label for="searchInput" class="form-label small text-muted mb-1">Search Properties</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" name="searchInput"
                                    placeholder="Name or description..." value="<?= htmlspecialchars($searchTerm) ?>">
                            </div>
                        </div>

                        <!-- Location Field -->
                        <div class="col-md-3">
                            <label for="locationInput" class="form-label small text-muted mb-1">Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" id="locationInput" name="location"
                                    placeholder="City or address" value="<?= htmlspecialchars($location) ?>">
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Price Range</label>
                            <div class="row g-2">
                                <div class="col">
                                    <select class="form-select" name="min_price">
                                        <option value="">Min Price</option>
                                        <?php
                                        $prices = [5000, 10000, 15000, 20000, 25000, 30000, 50000, 100000];
                                        foreach ($prices as $price) {
                                            $selected = ($minPrice == $price) ? 'selected' : '';
                                            echo "<option value=\"$price\" $selected>₱" . number_format($price) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" name="max_price">
                                        <option value="">Max Price</option>
                                        <?php
                                        foreach ($prices as $price) {
                                            $selected = ($maxPrice == $price) ? 'selected' : '';
                                            echo "<option value=\"$price\" $selected>₱" . number_format($price) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="map" value="#propertyMap">

                        <!-- Submit Button -->
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- Map Section -->
        <section class="property-map py-4">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body p-0" style="height: 500px;">
                        <div id="propertyMap" class=" z-2" style="height: 100%; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>


</div>
<?php if (isset($_GET['map']) && $_GET['map'] == 1): ?>
    <script>
        window.addEventListener('load', () => {
            const target = document.getElementById("propertyMap");
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    </script>
<?php endif; ?>
<script>
    // Run initChart when the page loads
    document.addEventListener('DOMContentLoaded', initChart);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<!-- JavaScript for Map -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Initialize the map
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate average coordinates from properties
        <?php
        $avgLat = 14.5995; // Default Manila latitude
        $avgLng = 120.9842; // Default Manila longitude
        $count = 0;

        if (count($properties) > 0) {
            $sumLat = 0;
            $sumLng = 0;
            $count = 0;

            foreach ($properties as $property) {
                if (!empty($property['latitude']) && !empty($property['longitude'])) {
                    $sumLat += $property['latitude'];
                    $sumLng += $property['longitude'];
                    $count++;
                }
            }

            if ($count > 0) {
                $avgLat = $sumLat / $count;
                $avgLng = $sumLng / $count;
            }
        }
        ?>

        const map = L.map('propertyMap').setView([<?= $avgLat ?>, <?= $avgLng ?>], <?= $count > 0 ? '8' : '7' ?>);

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Custom icons based on availability
        const availableIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const unavailableIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add markers for each property
        <?php foreach ($properties as $property): ?>
            <?php
            $lat = !empty($property['latitude']) ? $property['latitude'] : (14.5995 + (rand(-50, 50) / 1000));
            $lng = !empty($property['longitude']) ? $property['longitude'] : (120.9842 + (rand(-50, 50) / 1000));
            $isAvailable = $property['property_status'] === 'available';
            ?>

            L.marker([<?= $lat ?>, <?= $lng ?>], {
                    icon: <?= $isAvailable ? 'availableIcon' : 'unavailableIcon' ?>
                }).addTo(map)
                .bindPopup(`
                    <div style="width: 220px;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0 fw-bold"><?= addslashes($property['property_name']) ?></h6>
                            <span class="badge bg-<?= $isAvailable ? 'success' : 'secondary' ?>">
                                <?= $isAvailable ? 'Available' : 'Unavailable' ?>
                            </span>
                        </div>
                        <img src="<?= $property['image1'] ?>" style="width: 100%; height: 120px; object-fit: cover;" class="mb-2 rounded">
                        <div class="property-info">
                            <p class="mb-1"><strong>Location:</strong> <?= addslashes($property['property_location']) ?></p>
                            <p class="mb-1"><strong>Price:</strong> ₱<?= number_format($property['property_rental_price'], 2) ?>/mo</p>
                            <p class="mb-2 text-muted small"><?= substr(addslashes($property['property_description']), 0, 60) ?>...</p>
                        </div>
                        <a href="?page=properties/index&property_id=<?= $property['property_id'] ?>&property_status=<?= $property['property_status'] ?>"
                            class="btn btn-sm btn-outline-primary w-100 mb-2 rounded-5">
                            View Details
                        </a>
                    </div>
                `);
        <?php endforeach; ?>

        // Add legend
        const legend = L.control({
            position: 'bottomright'
        });
        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend bg-body-tertiary p-2 rounded shadow-sm');
            div.innerHTML = `
                <h6 class="mb-2 fw-bold">Property Status</h6>
                <div class="d-flex align-items-center mb-1">
                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png" width="20" class="me-2">
                    <span>Available</span>
                </div>
                <div class="d-flex align-items-center">
                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png" width="20" class="me-2">
                    <span>Unavailable</span>
                </div>
            `;
            return div;
        };
        legend.addTo(map);
    });
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


<?php
$conn->close();
?>