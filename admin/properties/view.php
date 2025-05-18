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

if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $query = "SELECT * FROM properties WHERE property_id = $property_id";
    $result = mysqli_query($conn, $query);
    $property = mysqli_fetch_assoc($result);

    $images_result = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = $property_id");
    $images = mysqli_fetch_assoc($images_result);

    $amenities_result = mysqli_query($conn, "
        SELECT a.amenity_name FROM amenities a
        JOIN property_amenities pa ON a.amenity_id = pa.amenity_id
        WHERE pa.property_id = $property_id
    ");
    $amenities = [];
    while ($row = mysqli_fetch_assoc($amenities_result)) {
        $amenities[] = $row['amenity_name'];
    }
} else {
    echo "<div class='text-center text-bg-warning'>Invalid property ID</div>";
    exit;
}
?>

<div class="container px-lg-5 mb-4 px-md-4 px-sm-3 px-2">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>
        <h4 class="fw-medium">Property / View Property</h4>
    </header>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><strong>Name:</strong> <?= htmlspecialchars($property['property_name']) ?></h5>
            <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($property['property_location']) ?></p>
            <p class="card-text"><strong>Created On:</strong> <?= htmlspecialchars($property['property_date_created']) ?></p>
            <p class="card-text"><strong>Rental Price:</strong> PHP <?= number_format($property['property_rental_price'], 2, '.', ',') ?></p>
            <p class="card-text"><strong>Description:</strong> <?= htmlspecialchars($property['property_description']) ?></p>
            <p class="card-text"><strong>Status:</strong><span class=" badge bg-success ms-1"> <?= ucfirst(htmlspecialchars($property['property_status'])) ?></span></p>
        </div>
    </div>

    <!-- Amenities -->
    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><strong>Amenities:</strong></h5>
            <?php if (!empty($amenities)): ?>
                <div class=" d-flex gap-2 flex-wrap">
                    <?php foreach ($amenities as $amenity): ?>
                        <div class="px-3 py-2 bg-body-tertiary rounded-5"><?= htmlspecialchars($amenity) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No amenities listed.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Images -->
    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><strong>Images:</strong></h5>
            <div class="row g-2">
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    $img = $images["image$i"] ?? '';
                    if (!empty($img)) {
                        echo "<div class='col-6 col-md-4 col-lg-3'><img src='" . htmlspecialchars($img) . "' class='img-fluid rounded border' style='height: 150px; object-fit: cover; width: 100%;'></div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php mysqli_close($conn); ?>
