<?php
// Connect to database
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}



// Handle the deletion after form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['property_id'])) {
        $property_id = $_POST['property_id'];
        $property_image = $_POST['property_image'];

        // Check if property image exists, and delete it
        if ($property_image && file_exists($_SERVER['DOCUMENT_ROOT'] . $property_image)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $property_image);  // Delete the image from the server
        }

        // Delete property from the database
        $query = "DELETE FROM properties WHERE property_id = '$property_id'";

        if (mysqli_query($conn, $query)) {
            // Redirect to properties list page after successful deletion
            echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=properties/index'>";
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid request.";
    }
} else {
    // Fetch property details for deletion confirmation
    if (isset($_GET['property_id'])) {
        $property_id = $_GET['property_id'];

        $query = "SELECT * FROM properties WHERE property_id = '$property_id'";
        $result = mysqli_query($conn, $query);
        $property = mysqli_fetch_assoc($result);

        if (!$property) {
            echo "Property not found.";
            exit();
        }
    } else {
        echo "No property ID provided.";
        exit();
    }
}

mysqli_close($conn);
?>

<!-- Property Deletion Confirmation -->
<div class="container px-lg-5 mb-4">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class=" p-2 rounded-circle bg-dark-subtle" width="2rem" height="2rem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
        </a>
        <h4 class="fw-medium ">Property / Delete Property</h4>
    </header>

    <p class="text-warning">Are you sure you want to delete the following property? This action cannot be undone.</p>

    <div class="mt-2">
        <label class="form-label fw-bold">Property Name</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($property['property_name']); ?></div>
    </div>

    <div class="mt-2">
        <label class="form-label fw-bold">Location</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($property['property_location']); ?></div>
    </div>

    <div class="mt-2">
        <label class="form-label fw-bold">Date Created</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($property['property_date_created']); ?></div>
    </div>

    <div class="mt-2">
        <label class="form-label fw-bold">Description</label>
        <div class="form-control-plaintext"><?php echo htmlspecialchars($property['property_description']); ?></div>
    </div>
    <div class="mt-2">
        <label class="form-label fw-bold">Image</label>
        <?php if (!empty($property['property_image'])): ?>
            <div class="mt-2">
                <img src="<?php echo htmlspecialchars($property['property_image']); ?>"
                    alt="Property Image"
                    class="img-fluid"
                    style="max-width: 200px;">
            </div>
        <?php endif; ?>
    </div>

    <form action="properties/delete.php" method="POST">
        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property['property_id']); ?>">
        <input type="hidden" name="property_image" value="<?php echo htmlspecialchars($property['property_image']); ?>">
        <div class="mt-3 d-flex gap-3">
            <button type="submit" class="btn btn-danger rounded-5">Delete Property</button>
            <a href="?page=properties/index" class="btn btn-secondary rounded-5">Cancel</a>
        </div>
    </form>
</div>