<?php
// Database connection (update with your credentials)
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
  echo "Error: cannot connect to database" . mysqli_connect_error();
}
// Fetch 4 latest available properties
$sql = "SELECT property_id, property_name, property_location, property_image, property_rental_price
        FROM properties 
        WHERE property_status = 'available' 
        ORDER BY property_date_created DESC;";

$result = $conn->query($sql);

?>

<!-- Property Cards -->
<div class="container my-5 px-lg-5 px-md-3">
  <h2 class="fw-mediums">All Properties</h2>
  <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col">
      <a href="?page=src/property-details&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="text-decoration-none text-dark">
          <div class="card p-2 border-0">
            <div class="overflow-hidden rounded-5">
              <img class="card-img img hover-image" src="<?php echo $row['property_image']; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
            </div>
            <div class="mt-2">
              <h5 class="card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
              <p class="card-subtitle"><?php echo htmlspecialchars($row['property_location']); ?></p>
              <p class="fs-6 card-subtitle">
                <span class="fw-medium">PHP <?php echo number_format(htmlspecialchars($row['property_rental_price']), 2, '.', ',') ?></span>
                <span class="opacity-75">Monthly</span>
              </p>
            </div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</div>


<?php $conn->close(); ?>
