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
      <p class=" lh-sm">A Place to Call Home</p>
      <h1 class=" lh-sm"><strong>Find Your Ideal Home Away from Home</strong></h1>
      <p class=" mt-2">Explore our wide range of rental properties and find the perfect place that fits your lifestyle and needs. Your dream home is just a click away!</p>
      <a class="btn btn-primary rounded-5 hover-top me-2">Rent Now</a>
      <a class="btn border-1 btn-outline-white rounded-5 hover-top">About Us</a>
    </div>
  </div>
</div>

<!-- About Us Section -->
<div id="about" class=" bg-body-tertiary py-5">
  <div class="container px-lg-5 px-md-3 ">
    <div class="row row-cols-1 row-cols-md-2 justify-content-evenly align-items-center">
      <div class="col">
        <div>
          <span class=" rounded-5 px-2 py-1 bg-info-subtle text-primary ">Our Story</span>
          <h2 class=" fw-medium">About Us</h2>
        </div>
        <p class=" fw-medium">RentMaster: Your Trusted Platform for Apartments and Homes</p>
        <p class=" opacity-75">
          At RentMaster, we specialize in renting apartments and homes across various locations. Our diverse selection is tailored to meet your unique needs, whether you're looking for a cozy apartment or a spacious home. Start your journey today and find the perfect place to call home!
        </p>
      </div>
      <div class=" col">
        <img src="./assets/images/image3.png" alt="About Us" class="img-fluid">
      </div>
    </div>
  </div>
</div>


<!-- Features Section -->
<div class="container mb-5 px-lg-5 px-md-3 py-5">
  <h2 class="fw-medium text-center">Features</h2>
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <i class="fas fa-building fa-2x mb-3"></i>
        <h6><strong>Discover Your Perfect Home</strong></h6>
        <p>Browse a wide range of apartments and homes that match your needs and preferences, making the home-search process simple and seamless.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <i class="fas fa-dollar-sign fa-2x mb-3"></i>
        <h6><strong>Transparent Payment Tracking</strong></h6>
        <p>Stay informed about your rent payments in real-time and receive reminders so you never miss a payment, keeping everything clear and stress-free.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded shadow-sm bg-light">
        <i class="fas fa-tools fa-2x mb-3"></i>
        <h6><strong>Hassle-Free Maintenance Requests</strong></h6>
        <p>Easily submit maintenance issues and track their resolution, ensuring your living space stays in top shape without the stress.</p>
      </div>
    </div>
  </div>
</div>


<!-- Property Cards -->
<div class="container my-5 px-lg-5 px-md-3">
  <h2 class=" fw-medium ">Recent Properties</h2>
  <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col">
        <div class="card p-2 border-0 ">
          <div class=" overflow-hidden rounded-5">
            <img class="card-img img hover-image" src="<?php echo $row['property_image']; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
          </div>
          <div class="mt-2">
            <h5 class=" card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
            <p class=" card-subtitle"><?php echo htmlspecialchars($row['property_location']); ?></p>
            <p class=" fs-6 card-subtitle"><span class=" fw-medium">PHP <?php echo number_format(htmlspecialchars($row['property_rental_price']), 2, '.', ',')  ?></span> <span class=" opacity-75">Monthly</span> </p>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="d-flex align-items-center justify-content-center">
    <a href="?page=src/property" class="btn btn-outline-primary mt-3">View More</a>
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
    <div class="col-md-6 offset-md-1">
      <h5>Get in Touch</h5>
      <form method="POST" action="https://formsubmit.co/mikogapasan04@gmail.com" class="p-4 border rounded shadow-sm bg-light">
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Email address" autocomplete="off" required>
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">Message:</label>
          <textarea class="form-control" name="message" id="message" rows="4" placeholder="Leave your message here" required></textarea>
        </div>

        <!-- Hidden inputs -->
        <input type="hidden" name="_next" value="http://localhost/rent-master2/client/">
        <input type="hidden" name="_subject" value="New email!">
        <input type="hidden" name="_captcha" value="false">

        <button type="submit" class="btn btn-primary w-100">Send Message</button>
      </form>


    </div>
  </div>
</div>


<?php $conn->close(); ?>