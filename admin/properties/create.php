<?php
$server = "127.0.0.1";
$user = "root";
$password = "";
$database = "rentsystem";

// Create a connection
$conn = mysqli_connect($server, $user, $password, $database);
if (!$conn) {
    die("Error: Cannot connect to database " . mysqli_connect_error());
}


// Function to handle image upload
function uploadImage($file, $existingImage = null)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/properties/images/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    global $error_message; // Use global variable

    if (!getimagesize($file["tmp_name"])) {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    if ($file["size"] > 5000000) {
        $error_message = "File size exceeds 2MB.";
        $uploadOk = 0;
    }

    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        $error_message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($existingImage && file_exists($existingImage)) {
        unlink($existingImage);
    }

    if ($uploadOk == 1 && move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "/rent-master2/admin/properties/images/" . $fileName;
    } else {
        $error_message = "Error uploading file.";
        return null;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['property_name']) && !empty($_POST['location']) && !empty($_POST['description']) && !empty($_POST['date_created']) && isset($_FILES['house_image'])) {
        $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date_created = mysqli_real_escape_string($conn, $_POST['date_created']);
        
        // Call the uploadImage function to handle the image upload
        $house_image = uploadImage($_FILES['house_image']);

        if ($house_image) {
            // If the image upload is successful, insert the property data into the database
            $queryInsert = "INSERT INTO property (property_name, property_location, property_date_created, property_description, property_image) 
                            VALUES ('$property_name', '$location', '$date_created', '$description', '$house_image')";
            mysqli_query($conn, $queryInsert);

            // Redirect to the properties index page inside /rent-master2/admin/
            echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=properties/index'>";
            exit();
        } else {
            // Error if the image upload fails
            echo "Error uploading image.";
        }
    } else {
        // Error if fields are missing
        echo "All fields are required.";
    }
}


mysqli_close($conn);
?>



<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Add Property</h4>
    </header>
    <form id="property-form" action="properties/create.php" method="post" enctype="multipart/form-data">
        <div class="mt-2">
            <label for="property-name" class="form-label">Property Name</label>
            <input type="text" id="property-name" name="property_name" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="date-created" class="form-label">Date Created</label>
            <input type="date" id="date-created" name="date_created" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required></textarea>
        </div>
        <div class="mt-2">
            <label for="house-image">Image</label>
            <input type="file" id="house-image" name="house_image" class="form-control" accept="image/*" required>
        </div>
        <button type="button" class="btn btn-success px-4 rounded-5 mt-3" id="submit-btn" >Submit</button>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="propertyModal" tabindex="-1">
    <div class="modal-dialog modal-lg  modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Property Name:</strong> <span id="modal-property-name"></span></p>
                <p><strong>Location:</strong> <span id="modal-location"></span></p>
                <p><strong>Date Created:</strong> <span id="modal-date-created"></span></p>
                <p><strong>Description:</strong> <span id="modal-description"></span></p>
                <p><strong>Image Preview:</strong></p>
                <img id="modal-image-preview" src="" alt="Property Image" class="img-fluid d-none">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmed-btn">Confirmed</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("submit-btn").addEventListener("click", function () {
    // Get values from the form
    let propertyName = document.getElementById("property-name").value.trim();
    let location = document.getElementById("location").value.trim();
    let description = document.getElementById("description").value.trim();
    let dateCreated = document.getElementById("date-created").value;
    let fileInput = document.getElementById("house-image");

    // Validation: Stop execution if any field is empty
    if (propertyName === "" || location === "" || description === "" || dateCreated === "" || fileInput.files.length === 0) {
        alert("All fields are required!");
        return;
    }

    // Set modal content
    document.getElementById("modal-property-name").innerText = propertyName;
    document.getElementById("modal-location").innerText = location;
    document.getElementById("modal-description").innerText = description;
    document.getElementById("modal-date-created").innerText = dateCreated;

    // Handle image preview
    let imagePreview = document.getElementById("modal-image-preview");
    let fileReader = new FileReader();

    fileReader.onload = function(event) {
        imagePreview.src = event.target.result;
        imagePreview.classList.remove("d-none");
    };
    fileReader.readAsDataURL(fileInput.files[0]);

    // Manually trigger the modal only when inputs are valid
    let modal = new bootstrap.Modal(document.getElementById("propertyModal"));
    modal.show();
});
document.getElementById("confirmed-btn").addEventListener("click", function() {
    document.getElementById("property-form").submit();
});
</script>