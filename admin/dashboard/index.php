<?php
require_once '../database/config.php';

$searchTerm = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$location = $_GET['location'] ?? '';
$searchType = $_GET['search_type'] ?? 'list';

$sql = "SELECT p.*, pi.image1 
        FROM properties p 
        LEFT JOIN property_images pi ON p.property_id = pi.property_id 
        WHERE (p.property_status = 'available' OR p.property_status = 'unavailable')";

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

$stmt->close();
$result->free();
?>
<div class="container">
    <h4 class="text-center fw-medium mt-3">Welcome Admin</h4>


    <div class="container ">
        <h5 class="text-black-50 mt-4">Overview this Month</h5>
        <hr>
        <div class="px-lg-5 row row-cols-1 row-cols-md-2 row-cols-lg-3 gx-lg-5 gy-3 overviews">
            <div class="col">
                <a href="?page=properties/index" class=" text-decoration-none card h-100  shadow text-white rounded-4 shortcuts" id="houses" onclick="ShowProperties()">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Neighborhood.png" alt="">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1">20</h1>
                                <span class="fw-bold">Houses</span>
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
                                <h1 class="fw-bolder fs-1">18</h1>
                                <span class="fw-bold">Tenants</span>
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
                                <h1 class="fw-bolder fs-1">14</h1>
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
    <div class="container">
        <h5 class="text-black-50 mt-4">Properties Map</h5>
        <hr>
        <section class="search-filter py-2 bg-white">
            <div class="container">
                <form method="GET" id="propertySearchForm">
                    <input type="hidden" name="page" value="src/property">
                    <input type="hidden" name="submitted" value="1">

                    <div class="row g-3 align-items-end">
                        <!-- Search Field -->
                        <div class="col-md-3">
                            <label for="searchInput" class="form-label small text-muted mb-1">Search Properties</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" name="search"
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
                                        $prices = [0, 5000, 10000, 15000, 20000, 25000, 30000];
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
        <section class="property-map py-4 bg-light ">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body p-0" style="height: 500px;">
                        <div id="propertyMap" class=" z-2" style="height: 100%; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="container">
        <h5 class="text-black-50 mt-4">Monthly History</h5>
        <hr>
        <div class=" container mb-3" style="width: 100%; height: 300px; margin: auto;">
            <canvas id="myChart" class=" w-100"></canvas>
        </div>
    </div>

</div>
<script>
    function initChart() {
        const ctx = document.getElementById('myChart');
        if (!ctx) return;

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['October', 'November', 'December', 'January', 'February', 'March'],
                datasets: [{
                    label: '# of Tenants',
                    data: [15, 19, 18, 15, 14, 19],
                    backgroundColor: ['rgba(255, 99, 133, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

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
            const div = L.DomUtil.create('div', 'info legend bg-white p-2 rounded shadow-sm');
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
</script>

<!-- CSS Styling -->
<style>
    .property-listing {
        background-color: #f8f9fa;
    }

    .divider {
        width: 80px;
        height: 3px;
        opacity: 0.7;
    }

    .property-card {
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .property-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .property-image-container {
        height: 220px;
        background: #f0f0f0;
    }

    .property-image {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .property-card:hover .property-image {
        transform: scale(1.05);
    }

    .property-price-badge {
        bottom: 15px;
        left: 15px;
        font-weight: 500;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .svg-icon {
        width: 16px;
        height: 16px;
        fill: currentColor;
        vertical-align: middle;
        margin-right: 5px;
    }

    /* Map styling */
    .property-map {
        background-color: #f8f9fa;
    }

    /* Search filter styling */
    .search-filter {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .input-group-text {
        border-right: none;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }

    /* Map popup styling */
    .leaflet-popup-content {
        margin: 8px 12px;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }

    .property-info p {
        margin-bottom: 0.3rem;
        line-height: 1.3;
    }

    /* Legend styling */
    .info.legend {
        padding: 8px 12px;
        font-size: 14px;
        line-height: 1.4;
    }

    .info.legend h6 {
        font-size: 14px;
        margin-bottom: 8px;
        color: #333;
    }
</style>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<?php
$conn->close();
?>