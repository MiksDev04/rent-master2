<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}


function uploadImage($file, $existingImage = null)
{
    // Use a relative path for deployment
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/assets/properties/";

    // Check if the directory exists and create it if not
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Ensure the directory exists
    }

    $fileName = basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    if (!getimagesize($file["tmp_name"])) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 2MB)
    if ($file["size"] > 5000000) {
        echo "File size exceeds 2MB.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    // If there's an existing image, delete it
    if ($existingImage && file_exists($_SERVER['DOCUMENT_ROOT'] . $existingImage)) {
        unlink($_SERVER['DOCUMENT_ROOT'] . $existingImage); // Delete the existing image
    }

    // Attempt to upload the file
    if ($uploadOk == 1 && move_uploaded_file($file["tmp_name"], $targetFile)) {
        // Return the full path of the image relative to the root of the web server
        return "/rent-master2/admin/assets/properties/" . $fileName;
    } else {
        echo "Error uploading file.";
        return null;
    }
}


// Update Property
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['property_name']) && !empty($_POST['location']) && !empty($_POST['description']) && !empty($_POST['date_created'])) {
        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
        $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
        $property_location = mysqli_real_escape_string($conn, $_POST['location']);
        $property_description = mysqli_real_escape_string($conn, $_POST['description']);
        $property_date_created = mysqli_real_escape_string($conn, $_POST['date_created']);
        $existing_image = mysqli_real_escape_string($conn, $_POST['existing_image']); // Existing image path
        $property_rental_price = mysqli_real_escape_string($conn, $_POST['property_rental_price']);

        // Check if a new image is uploaded
        if ($_FILES['house_image']['name']) {
            // Upload the new image and get the new image path
            $property_image = uploadImage($_FILES['house_image'], $existing_image);
        } else {
            // If no new image is uploaded, retain the existing image
            $property_image = $existing_image;
        }

        // Update property record
        $queryUpdate = "UPDATE properties
                        SET property_name = '$property_name', property_location = '$property_location', 
                            property_date_created = '$property_date_created', 
                            property_description = '$property_description', 
                            property_image = '$property_image',
                            property_rental_price = '$property_rental_price' 
                        WHERE property_id = '$property_id'";

        if (mysqli_query($conn, $queryUpdate)) {
            // Redirect after successful update
            echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=properties/index'>";
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        echo "All fields are required.";
    }
} else {
    // Fetch property details for editing
    if (isset($_GET['property_id'])) {
        $property_id = $_GET['property_id'];

        // Fetch the property data from the database
        $query = "SELECT * FROM properties WHERE property_id = '$property_id'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $property = mysqli_fetch_assoc($result);
            $property_name = $property['property_name'];
            $location = $property['property_location'];
            $description = $property['property_description'];
            $date_created = $property['property_date_created'];
            $existing_image = $property['property_image'];
            $rental_price = $property['property_rental_price'];
        } else {
            echo "Property not found.";
            exit;
        }
    } else {
        echo "No property ID provided.";
        exit;
    }
}

mysqli_close($conn);
?>


<!-- Property Update Form -->
<div class="container px-lg-5 mb-4">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class=" p-2 rounded-circle bg-dark-subtle" width="2rem" height="2rem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
        </a>
        <h4 class="fw-medium ">Property / Update Property</h4>
    </header>

    <form id="property-form" action="properties/update.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
        <div class="mt-2">
            <label for="property-name" class="form-label">Property Name</label>
            <input type="text" id="property-name" name="property_name" class="form-control" value="<?php echo $property_name; ?>" required>
        </div>
        <div class="mt-2">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" value="<?php echo $location; ?>" required>
        </div>
        <div class="mt-2">
            <label for="date-created" class="form-label">Date Created</label>
            <input type="date" id="date-created" name="date_created" class="form-control" value="<?php echo $date_created; ?>" required>
        </div>
        <div class="mt-2">
            <label for="rental-price" class="form-label">Rental Price (PHP)</label>
            <input type="number" id="rental-price" name="property_rental_price" class="form-control" value="<?php echo $rental_price; ?>" required>
        </div>
        <div class="mt-2">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required><?php echo $description; ?></textarea>
        </div>
        <div class="mt-2">
            <label for="house-image">Upload Image</label>
            <input type="file" id="house-image" name="house_image" class="form-control" accept="image/*">
            <input type="hidden" name="existing_image" value="<?php echo $existing_image; ?>">
            <?php if (!empty($existing_image)) : ?>
                <div class="mt-2">
                    <label for="current-image-preview" class=" w-100">Current Image</label>
                    <img id="current-image-preview"
                        src="<?php echo $existing_image; ?>"
                        alt="Current Image"
                        class="img-fluid"
                        style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>
        <button type="button" id="submit-btn" class="btn btn-primary px-4 rounded-5 mt-3">Update</button>
    </form>
</div>

<!-- Modal (Optional Preview) -->
<div class="modal fade" id="propertyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Property Name:</strong> <span id="modal-property-name"></span></p>
                <p><strong>Location:</strong> <span id="modal-location"></span></p>
                <p><strong>Date Created:</strong> <span id="modal-date-created"></span></p>
                <p><strong>Rental Price:</strong> PHP <span id="modal-rental-price"></span></p>
                <p><strong>Description:</strong> <span id="modal-description"></span></p>
                <p><strong>Image Preview:</strong></p>
                <img id="modal-image-preview" src="" alt="Property Image" class="img-fluid d-none">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmed-btn">Confirmed</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle the form data and image preview
    // Handle the form data and image preview
    document.getElementById("submit-btn").addEventListener("click", function() {
        let propertyName = document.getElementById("property-name").value.trim();
        let location = document.getElementById("location").value.trim();
        let description = document.getElementById("description").value.trim();
        let dateCreated = document.getElementById("date-created").value;
        let rentalPrice = document.getElementById("rental-price").value.trim(); // Get the rental price
        let fileInput = document.getElementById("house-image");
        let existingImage = document.querySelector("input[name='existing_image']").value;

        if (propertyName === "" || location === "" || description === "" || dateCreated === "" || rentalPrice === "") {
            alert("All fields are required!");
            return;
        }

        // Set modal content
        document.getElementById("modal-property-name").innerText = propertyName;
        document.getElementById("modal-location").innerText = location;
        document.getElementById("modal-description").innerText = description;
        document.getElementById("modal-date-created").innerText = dateCreated;
        document.getElementById("modal-rental-price").innerText = rentalPrice; // Set rental price in modal

        let imagePreview = document.getElementById("modal-image-preview");

        if (fileInput.files.length > 0) {
            let fileReader = new FileReader();
            fileReader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreview.classList.remove("d-none");
            };
            fileReader.readAsDataURL(fileInput.files[0]);
        } else {
            imagePreview.src = existingImage;
            imagePreview.classList.remove("d-none");
        }

        let modal = new bootstrap.Modal(document.getElementById("propertyModal"));
        modal.show();
    });

    // Confirm and submit the form
    document.getElementById("confirmed-btn").addEventListener("click", function() {
        document.getElementById("property-form").submit();
    });
</script>