<?php
// Database connection (update with your credentials)
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}


// Fetch 4 latest available properties
$sql = "SELECT property_name, property_location, property_image, property_rental_price
        FROM properties 
        WHERE property_status = 'available' 
        ORDER BY property_date_created DESC 
        LIMIT 3";

$result = $conn->query($sql);
?>

<!-- Hero Section -->
<div class="hero">
  <div class="hero-overlay">
    <div>
      <h1>Welcome to <strong>RentMaster!!!</strong></h1>
      <p>RentMaster is an all-in-one property rental management system designed for landlords and property managers. It streamlines rent tracking, tenant management, lease organization, and automated reminders—making rental operations easier, more efficient, and stress-free. Perfect for managing multiple properties.</p>
      <a class="btn btn-pink mt-3" style="background-color: #f36; color: white;">RENT NOW</a>
    </div>
  </div>
</div>

<!-- Property Cards -->
<div class="container my-5 px-lg-5 px-md-3">
    <h2>Our Properties</h2>
    <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card p-2">
                    <img class="card-img " src="<?php echo $row['property_image']; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
                    <div class="mt-2">
                        <h5 class=" card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
                        <p class=" card-subtitle"><?php echo htmlspecialchars($row['property_location']); ?></p>
                        <p class=" fs-6 card-subtitle fw-medium">PHP  <?php echo number_format(htmlspecialchars($row['property_rental_price']), 2, '.', ',')  ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="d-flex align-items-center justify-content-center">
        <button class="btn btn-outline-primary mt-3">View More</button>
    </div>
</div>

<!-- About Us Section -->
<div class="container mb-5 px-lg-5 px-md-3">
  <div class="row justify-content-center">
    <div class="col-md-6 about-section">
      <h4>About Us</h4>
      <p>
        RentMaster is your go-to platform for renting apartments, condos, and homes. We offer a wide variety of listings in various locations to suit your needs.
      </p>
    </div>
  </div>
</div>

<!-- Features Section -->
<div class="container mb-5 px-lg-5 px-md-3">
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <h6><strong>Property Management</strong></h6>
        <p>Streamline property listings, tenant records, and lease agreements.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <h6><strong>Payment Tracking</strong></h6>
        <p>Track payments in real time and send reminders.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <h6><strong>Maintenance Management</strong></h6>
        <p>Allow tenants to submit issues and resolve them easily.</p>
      </div>
    </div>
  </div>
</div>

<!-- Testimonial -->
<div class="container mb-5 px-lg-5 px-md-3">
  <div class="row justify-content-center">
    <div class="col-md-8 testimonial text-center">
      <p>"Finding the perfect apartment has never been easier! The process was smooth..."</p>
      <p><strong>- Happy Tenant Mga Bossing!!!</strong></p>
    </div>
  </div>
</div>

<!-- Contact Section -->
<div class="container mb-5 px-lg-5 px-md-3">
  <div class="row g-4 align-items-center">
    <div class="col-md-4 text-center">
      <img src="./assets/images/image7.png" alt="Agent" class="rounded-circle mb-2" style="width: 100px; height: 100px;">
      <div class="bg-light p-2 rounded">
        <small>Phone: (123) 456-7890</small><br>
        <small>Email: rentmaster@example.com</small><br>
        <small>FB Name: JirehSinsFB</small>
      </div>
    </div>
    <div class="col-md-6 offset-md-1 contact-card">
      <h5>Get in Touch</h5>
      <form>
        <div class="mb-2">
          <label>Name</label>
          <input type="text" class="form-control" placeholder="Enter your name">
        </div>
        <div class="mb-2">
          <label>Email</label>
          <input type="email" class="form-control" placeholder="Enter your email">
        </div>
        <div class="mb-2">
          <label>Comment</label>
          <textarea class="form-control" rows="3" placeholder="Enter your message"></textarea>
        </div>
        <button type="submit" class="btn btn-pink" style="background-color: #f36; color: white;">SEND MESSAGE</button>
      </form>
    </div>
  </div>
</div>


<?php $conn->close(); ?>