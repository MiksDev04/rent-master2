<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}

// Fetch property details if a property_id is provided
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $query = "SELECT * FROM properties WHERE property_id = $property_id";
    $result = mysqli_query($conn, $query);
    $property = mysqli_fetch_assoc($result);
} else {
    echo "<div class='text-center text-bg-warning'>Invalid property ID</div>";
    exit;
}
?>

<div class="container px-lg-5 mb-4 px-lg-5 px-md-4 px-sm-3 px-2">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class="p-2 rounded-circle bg-dark-subtle">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
            </svg>
        </a>
        <h4 class="fw-medium">Property / View Property</h4>
    </header>

    <div class="card mt-3">
        <div class="row g-0">
            <!-- Left Column: Property Details -->
            <div class="col-lg-6">
                <div class="card-body">
                
                    <h5 class="card-title"><strong>Name:</strong> <?php echo htmlspecialchars($property['property_name']); ?></h5>
                    <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($property['property_location']); ?></p>
                    <p class="card-text"><strong>Created On:</strong> <?php echo htmlspecialchars($property['property_date_created']); ?></p>
                    <p class="card-text"><strong>Rental Price:</strong> PHP <?php echo number_format(htmlspecialchars($property['property_rental_price']), 2, '.', ',') ?></p>
                    <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($property['property_description']); ?></p>
                    <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($property['property_status'])); ?></p>
                </div>
            </div>

            <!-- Right Column: Property Image -->
            <div class="col-lg-6">
                <div class="card-img p-3">
                    <img src="<?php echo htmlspecialchars($property['property_image']); ?>" alt="Property Image" class="card-img-top img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
?>
