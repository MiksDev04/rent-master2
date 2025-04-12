<?php
session_start();

// Database connection
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$success_message = "";

// Get property details
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);
    $sql = "SELECT * FROM properties WHERE property_id = $property_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $property = $result->fetch_assoc();
    } else {
        echo "<p>Property not found.</p>";
        exit;
    }
} else {
    echo "<p>No property selected.</p>";
    exit;
}

// Handle form submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_submit'])) {
    if (!isset($_SESSION['user_id'])) {
        // Not logged in, redirect to login
        header("Location: /rent-master2/client/?page=src/login");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Insert rent request into tenants table
    $insert_sql = "INSERT INTO tenants (user_id, property_id, tenant_status, tenant_date_created)
                   VALUES ($user_id, $property_id, 'pending', NOW())";

    if (mysqli_query($conn, $insert_sql)) {
        $success_message = "Rent request sent successfully! Your status is now pending.";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container p-4">

    <div class="row row-cols-lg-2 row-cols-1">
        <div class="col col-lg-8 mb-5">
            <!-- ✅ Success Modal -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <h2 class="mb-4"><?php echo htmlspecialchars($property['property_name']); ?></h2>
            <img src="<?php echo $property['property_image']; ?>" class="img-fluid rounded mb-3" alt="Property Image" style="max-height: 400px; object-fit: cover;">

            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['property_location']); ?></p>
            <p><strong>Price:</strong> PHP <?php echo number_format($property['property_rental_price'], 2); ?> / Month</p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($property['property_description'] ?? 'No description available.')); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($property['property_status']); ?></p>

            <!-- Rent Form -->
            <?php if ($property['property_status'] === 'available'): ?>
                <form method="POST">
                    <button type="submit" name="rent_submit" class="btn btn-success mt-3">Rent This Property</button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary mt-3" disabled>Already Rented</button>
            <?php endif; ?>
        </div>

        <div class="col col-lg-4">
            <!-- Contact Form for Email -->
            <form method="POST" action="https://formsubmit.co/mikogapasan04@gmail.com" class="p-4 border rounded shadow-sm bg-light">
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email address" autocomplete="off" required>
                </div>

                <input type="hidden" name="property_name" value="<?php echo htmlspecialchars($property['property_name']); ?>">
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