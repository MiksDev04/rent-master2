<?php
// Database connection with error handling
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');
if (!$conn) {
    die('<div class="database-error">Database connection failed: ' . mysqli_connect_error() . '</div>');
}

// Fetch available properties with prepared statement for security
$sql = "SELECT p.property_id, p.property_name, p.property_location, 
               p.property_rental_price, p.property_description, pi.image1 
        FROM properties p 
        LEFT JOIN property_images pi ON p.property_id = pi.property_id 
        WHERE p.property_status = 'available' 
        ORDER BY p.property_date_created DESC";

$result = $conn->query($sql);
?>

<!-- Property Listing Section -->
<section class="property-listing py-5 bg-light">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <h2 class="display-5 fw-light text-dark mb-3">Discover Your Perfect Home</h2>
            <p class="lead text-muted">Explore our curated collection of premium rental properties</p>
            <div class="divider mx-auto bg-primary"></div>
        </div>

        <!-- Property Grid -->
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): ?>
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
                                <?php echo htmlspecialchars($row['property_location']); ?>
                            </p>
                            <p class="card-text text-secondary small">
                                <?php echo substr(htmlspecialchars($row['property_description']), 0, 100); ?>...
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <a href="?page=src/property-details&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" 
                               class="btn btn-outline-primary rounded-pill px-4 stretched-link">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($result->num_rows === 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-home text-muted fa-3x mb-3"></i>
                <h4 class="text-muted">No properties currently available</h4>
                <p class="text-muted">Please check back later</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php 
$conn->close();
?>

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
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>