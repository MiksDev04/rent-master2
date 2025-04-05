<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}

// Handle the update when the form is submitted
if (isset($_POST['update_status'])) {
    $property_id = $_POST['property_id'];
    $new_status = $_POST['new_status'];  // Get the new status from the form

    $update_query = "UPDATE properties SET property_status = '$new_status' WHERE property_id = $property_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<div class='alert alert-success'>Property status updated to $new_status</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating property status</div>";
    }
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
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">View Property</h4>
    </header>

    <div class="card">
        <div class="row g-0">
            <!-- Left Column: Property Details -->
            <div class="col-lg-6">
                <div class="card-body">
                    <h5 class="card-title"><strong>Name: </strong><?php echo htmlspecialchars($property['property_name']); ?></h5>
                    <p class="card-text"><strong>Location: </strong><?php echo htmlspecialchars($property['property_location']); ?></p>
                    <p class="card-text"><strong>Created On: </strong><?php echo htmlspecialchars($property['property_date_created']); ?></p>
                    <p class="card-text"><strong>Description: </strong><?php echo htmlspecialchars($property['property_description']); ?></p>

                    <!-- Status Update Buttons -->
                    <?php if ($property['property_status'] == 'available') { ?>
                        <p><strong>Status:</strong> Available</p>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['property_id']); ?>">
                            <input type="hidden" name="new_status" value="unavailable">
                            <button type="submit" name="update_status" class="btn btn-danger rounded-5">Mark as Unavailable</button>
                        </form>
                    <?php } else { ?>
                        <p><strong>Status:</strong> Unavailable</p>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['property_id']); ?>">
                            <input type="hidden" name="new_status" value="available">
                            <button type="submit" name="update_status" class="btn btn-success rounded-5">Mark as Available</button>
                        </form>
                    <?php } ?>
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

    <!-- Back Button -->
    <a href="?page=properties/index" class="btn btn-secondary mt-3 rounded-5">Back to Properties List</a>
</div>

<?php
mysqli_close($conn);
?>
