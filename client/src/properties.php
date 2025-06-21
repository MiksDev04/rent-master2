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
        LEFT JOIN landlords AS l ON p.landlord_id = l.landlord_id
        WHERE (p.property_status = 'available' OR p.property_status = 'unavailable') AND l.landlord_status = 'active' ";

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

<!-- Property Listing Section -->
<section class="property-listing py-3 bg-light">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-3">
            <h2 class="display-5 fw-light text-dark mb-3">Discover Your Perfect Home</h2>
            <p class="lead text-muted">Explore our curated collection of premium rental properties</p>
            <div class="divider mx-auto bg-primary"></div>
        </div>
        <!-- Unified Search & Filter Section -->
        <section class="search-filter py-2 bg-white">
            <div class="container">
                <form method="GET" id="propertySearchForm">
                    <input type="hidden" name="page" value="src/properties">
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
        <section class="property-map py-4 bg-light">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body p-0" style="height: 500px;">
                        <div id="propertyMap" style="height: 100%; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Property Grid -->
        <div class="row g-4">
            <?php if (count($properties) > 0): ?>
                <?php foreach ($properties as $row): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="property-card card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow">
                            <div class="property-image-container overflow-hidden position-relative">
                                <img src="<?php echo htmlspecialchars($row['image1']); ?>"
                                    alt="<?php echo htmlspecialchars($row['property_name']); ?>"
                                    class="img-fluid property-image transition-all">
                                <div class="property-price-badge bg-primary text-white px-3 py-2 rounded-pill position-absolute">
                                    ₱<?php echo number_format($row['property_rental_price'], 2); ?>/mo
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-normal text-dark mb-1">
                                    <?php echo htmlspecialchars($row['property_name']); ?>
                                </h5>
                                <p class="text-muted mb-2">
                                    <svg class="svg-icon" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <?php echo substr(htmlspecialchars($row['property_location']), 0, 70); ?>
                                </p>
                                <p class="card-text text-secondary small">
                                    <?php echo substr(htmlspecialchars($row['property_description']), 0, 100); ?>...
                                </p>
                            </div>
                            <div class="card-footer bg-transparent d-flex justify-content-between align-items-center border-0 pt-0">
                                <a href="?page=src/properties-details&property_id=<?php echo htmlspecialchars($row['property_id']); ?>"
                                    class="btn btn-outline-primary rounded-pill px-4 stretched-link">
                                    View Details
                                </a>
                                <span class="badge bg-<?php echo $row['property_status'] === 'available' ? 'success' : 'secondary'; ?>">
                                    <?= htmlspecialchars($row['property_status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-home text-muted fa-3x mb-3"></i>
                    <h4 class="text-muted">No properties found matching your criteria</h4>
                    <p class="text-muted">Try adjusting your search filters</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

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
                        <a href="?page=src/properties-details&property_id=<?= $property['property_id'] ?>" 
                           class="btn btn-sm btn-outline-primary w-100 mb-2">
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