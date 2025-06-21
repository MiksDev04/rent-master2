<?php

require_once '../database/config.php';

$success_message = "";
$success_message_status = "alert-success";

// Get property details
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);
    // Get testimonials for this property
    $testimonialsQuery = "SELECT ts.*, u.*
                         FROM testimonials AS ts 
                         JOIN tenants AS te ON te.tenant_id = ts.tenant_id 
                         JOIN properties AS p ON ts.property_id = p.property_id
                         JOIN users AS u ON te.user_id = u.user_id
                         WHERE ts.property_id = '$property_id' LIMIT 5";
    $testimonialsResult = mysqli_query($conn, $testimonialsQuery);
    $testimonials = mysqli_fetch_all($testimonialsResult, MYSQLI_ASSOC);
    // Get property info
    $sqlProperties = "SELECT * FROM properties WHERE property_id = $property_id";
    $propertyResult = $conn->query($sqlProperties);

    // Get landlord info
    $sqlLandlords = "SELECT u.user_name, u.user_image
            FROM users AS u 
            JOIN landlords AS l ON u.user_id = l.user_id 
            JOIN properties AS p ON p.landlord_id = l.landlord_id 
            WHERE p.property_id = $property_id";
    $landlord_result = $conn->query($sqlLandlords);


    if ($propertyResult && $propertyResult->num_rows > 0) {
        $property = $propertyResult->fetch_assoc();
        $landlord = $landlord_result->fetch_assoc();
    } else {
        echo "<p>Property not found.</p>";
        exit;
    }

    // Get property images
    $images_sql = "SELECT * FROM property_images WHERE property_id = $property_id";
    $images_result = $conn->query($images_sql);
    $images = $images_result->fetch_assoc();

    // Get property amenities
    $amenities_sql = "SELECT a.amenity_name 
                      FROM property_amenities pa
                      JOIN amenities a ON pa.amenity_id = a.amenity_id
                      WHERE pa.property_id = $property_id";
    $amenities_result = $conn->query($amenities_sql);
    $amenities = [];
    while ($row = $amenities_result->fetch_assoc()) {
        $amenities[] = $row['amenity_name'];
    }
} else {
    echo "<p>No property selected.</p>";
    exit;
}

// Handle form submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_submit'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /rent-master2/client/?page=src/login");
        exit();
    }

    $user_id = intval($_SESSION['user_id']);
    $landlord_id = intval($property['landlord_id']);
    
    // Landlord check (insert this part)
    $landlord_check = "SELECT user_role FROM users WHERE user_id = $user_id AND user_role = 'landlord'";
    $landlord_result = mysqli_query($conn, $landlord_check);

    if ($landlord_result && mysqli_num_rows($landlord_result) > 0) {
        $success_message_status = "alert-danger";
        $success_message = "Landlords cannot rent properties.";
        // Skip the rest of the rental process
    } else {

        // Check if user is already an active tenant
        $check_sql = "SELECT * FROM tenants WHERE user_id = $user_id AND tenant_status = 'active'";
        $check_result = mysqli_query($conn, $check_sql);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $success_message_status = "alert-warning";
            $success_message = "You already have an active rental. You cannot rent another property.";
        } else {

            $insert_sql = "INSERT INTO tenants (user_id, property_id, landlord_id, tenant_status, tenant_date_created)
                           VALUES ($user_id, $property_id, $landlord_id, 'pending', NOW())";

            if (mysqli_query($conn, $insert_sql)) {
                $success_message = "Rent request sent successfully! Wait for the landlord's approval.";
                $success_message_status = "alert-success";

                // Add notification for property owner
                $message = "New tenant request received for property: {$property['property_name']}. Status: Pending";
                $notification_sql = "INSERT INTO notifications (user_id, type, message, related_id, landlord_id) 
                                    VALUES ($user_id, 'property', '$message', $property_id, $landlord_id)";
                mysqli_query($conn, $notification_sql);
            } else {
                echo "<p>Error inserting tenant: " . mysqli_error($conn) . "</p>";
            }
        }
    }
}

?>


<div class="container py-5">

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo $_GET['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Success Message -->
    <?php if (!empty($success_message)): ?>
        <div class="alert <?php echo $success_message_status ?> alert-dismissible fade show mb-4" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Main Property Content -->
        <div class="col-lg-8">
            <h1 class="fw-light mb-3"><?php echo htmlspecialchars($property['property_name']); ?></h1>

            <?php
            $validImages = [];
            for ($i = 1; $i <= 10; $i++) {
                if (!empty($images['image' . $i])) {
                    $validImages[] = [
                        'src' => htmlspecialchars($images['image' . $i]),
                        'alt' => "Property image $i",
                        'index' => count($validImages)
                    ];
                }
            }
            ?>

            <?php if (!empty($validImages)): ?>
                <div id="propertyCarousel" class="carousel slide mb-3 rounded-4 overflow-hidden shadow" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($validImages as $idx => $img): ?>
                            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                <img src="<?= $img['src'] ?>" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="<?= $img['alt'] ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <!-- Thumbnails -->
                <div class="d-flex justify-content-start gap-2 flex-wrap mb-4">
                    <?php foreach ($validImages as $img): ?>
                        <img
                            src="<?= $img['src'] ?>"
                            class="img-thumbnail <?= $img['index'] === 0 ? 'active-thumbnail' : '' ?>"
                            style="width: 100px; height: 75px; object-fit: cover; cursor: pointer;"
                            alt="<?= $img['alt'] ?>"
                            data-bs-target="#propertyCarousel"
                            data-bs-slide-to="<?= $img['index'] ?>"
                            id="thumb-<?= $img['index'] ?>">
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="text-center text-muted p-5 border rounded bg-light">
                    <p>No property images available.</p>
                </div>
            <?php endif; ?>


            <!-- Property Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 fw-normal mb-3 text-primary">Property Details</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg> <strong>Location:</strong></p>
                            <p><?php echo htmlspecialchars($property['property_location']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z" />
                                    <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z" />
                                </svg> <strong>Price:</strong></p>
                            <p>₱<?php echo number_format($property['property_rental_price'], 2); ?> / month</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                                </svg> <strong>Date Listed:</strong></p>
                            <p><?php echo date('F j, Y', strtotime($property['property_date_created'])); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg> <strong>Status:</strong></p>
                            <p><span class="badge bg-<?php echo $property['property_status'] === 'available' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($property['property_status']); ?>
                                </span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 640 512">
                                    <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z" />
                                </svg>
                                </svg> <strong>House capacity:</strong></p>
                            <p><?php echo $property['property_capacity']; ?> Persons</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M399 384.2C376.9 345.8 335.4 320 288 320l-64 0c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z" />
                                </svg>
                                </svg> <strong>Landlord:</strong>
                            </p>
                            <div class=" d-flex ">
                                <img src="<?php echo $landlord['user_image'] ?>" alt="Profile" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                <p><?php echo $landlord['user_name']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5 fw-normal mb-3 text-primary">Description</h3>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($property['property_description'] ?? 'No description available.')); ?></p>
                </div>
            </div>

            <!-- Amenities -->
            <?php if (!empty($amenities)): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="h5 fw-normal mb-3 text-primary">Amenities</h3>
                        <div class="row g-2">
                            <?php foreach ($amenities as $amenity): ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="d-flex align-items-center p-2 gap-2 bg-light rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($amenity); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Rent Button -->
            <?php if ($property['property_status'] == 'available'): ?>
                <button type="button" class="btn btn-primary btn-lg px-5 py-3" data-bs-toggle="modal" data-bs-target="#rentModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.5 8h-.5v1.5a.5.5 0 0 1-1 0V8h-.5a.5.5 0 0 1 0-1h.5V5.5a.5.5 0 0 1 1 0V7h.5a.5.5 0 0 1 0 1z" />
                        <path d="M7 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm4 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                        <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zm1.5 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                        <path d="M4.5 6a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                        <path d="M2 5.5A1.5 1.5 0 0 1 3.5 4h9A1.5 1.5 0 0 1 14 5.5v5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 10.5v-5zM3.5 5a.5.5 0 0 0-.5.5v5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-5a.5.5 0 0 0-.5-.5h-9z" />
                    </svg>Rent This Property
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary btn-lg px-5 py-3" disabled data-bs-toggle="modal" data-bs-target="#rentModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.5 8h-.5v1.5a.5.5 0 0 1-1 0V8h-.5a.5.5 0 0 1 0-1h.5V5.5a.5.5 0 0 1 1 0V7h.5a.5.5 0 0 1 0 1z" />
                        <path d="M7 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm4 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                        <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zm1.5 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm4 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                        <path d="M4.5 6a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z" />
                        <path d="M2 5.5A1.5 1.5 0 0 1 3.5 4h9A1.5 1.5 0 0 1 14 5.5v5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 10.5v-5zM3.5 5a.5.5 0 0 0-.5.5v5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-5a.5.5 0 0 0-.5-.5h-9z" />
                    </svg>Rent This Property
                </button>
                <div class=" alert alert-warning mt-3">This property is currently being rented by someone</div>
            <?php endif; ?>
        </div>

        <!-- Rent Confirmation Modal -->
        <div class="modal fade" id="rentModal" tabindex="-1" aria-labelledby="confirmRentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Confirm Rent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to rent this property?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" class="text-center" id="rent-form">
                            <input type="hidden" name="landlord_id" value="<?php echo htmlspecialchars($property['landlord_id']); ?>">
                            <button type="submit" name="rent_submit" id="send-rent-btn" class="btn btn-primary">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Form -->
            <div class="d-grid gap-3">
                <div class="card shadow-sm " style="top: 20px;">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-normal mb-3 text-primary">Contact Owner</h3>
                        <form method="POST" action="client/../includes/send_email.php" id="send-email-form">
                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required>
                            </div>
                            <input type="hidden" name="property_name" value="<?php echo htmlspecialchars($property['property_name']); ?>">
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" name="message" id="message" rows="4" placeholder="Your message..." required></textarea>
                            </div>
                            <input type="hidden" name="submit-property-request-form" value="1">
                            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['property_id']); ?>">
                            <button type="submit" class="btn btn-primary w-100" id="send-email-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11zM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493z" />
                                </svg> Send Message
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm" style="top: 20px;">
                    <div class="card-body p-4">
                        <h3 class="h5 fw-normal mb-4 text-primary">Tenant Reviews</h3>

                        <?php if (!empty($testimonials)): ?>
                            <?php foreach ($testimonials as $review): ?>
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <?php if (!empty($review['user_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($review['user_image']); ?>" alt="User Image" class="rounded-circle" style="width: 40px; height: 40px;">
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <?php echo strtoupper(substr($review['user_name'] ?? 'A', 0, 1)); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></h6>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?php echo $i <= $review['rating'] ? 'text-warning' : 'text-secondary'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-muted mb-0">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .img-thumbnail.active-thumbnail {
        border: 3px solid #0d6efd;
        box-shadow: 0 0 0 2px #fff, 0 0 10px rgba(13, 110, 253, 0.6);
    }

    .carousel-control-prev,
    .carousel-control-next {
        background-color: rgba(0, 0, 0, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
    }

    .carousel-control-prev {
        left: 20px;
    }

    .carousel-control-next {
        right: 20px;
    }

    .card {
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('send-email-form');
        const submitBtn = document.getElementById('send-email-btn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        });
    });
</script>
<?php $conn->close(); ?>