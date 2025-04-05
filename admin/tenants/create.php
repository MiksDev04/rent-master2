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

// Fetch properties with 'unavailable' status
$queryProperties = "SELECT property_id, property_name FROM properties WHERE property_status = 'available'";
$propertiesResult = mysqli_query($conn, $queryProperties);
$properties = [];
while ($row = mysqli_fetch_assoc($propertiesResult)) {
    $properties[] = $row;
}

// Function to handle image upload
function uploadImage($file, $existingImage = null)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/tenants/images/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    global $error_message;

    if (!getimagesize($file["tmp_name"])) {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    if ($file["size"] > 5000000) {
        $error_message = "File size exceeds 5MB.";
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
        return "/rent-master2/admin/tenants/images/" . $fileName;
    } else {
        $error_message = "Error uploading file.";
        return null;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['user_name']) && !empty($_POST['user_email']) && !empty($_POST['user_phone']) && !empty($_POST['user_address']) && !empty($_POST['property_id'])) {
        $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
        $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
        $user_phone = mysqli_real_escape_string($conn, $_POST['user_phone']);
        $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);
        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
        $user_description = mysqli_real_escape_string($conn, $_POST['user_description']);

        // Call the uploadImage function to handle the image upload
        $user_image = uploadImage($_FILES['user_image']);

        if ($user_image) {
            // If the image upload is successful, insert the user data
            $queryInsertUser = "INSERT INTO users (user_name, user_email, user_phone_number, user_address, user_description, user_image) 
                                VALUES ('$user_name', '$user_email', '$user_phone', '$user_address', '$user_description', '$user_image')";
            mysqli_query($conn, $queryInsertUser);

            // Get the last inserted user_id
            $user_id = mysqli_insert_id($conn);

            // Insert tenant data
            $queryInsertTenant = "INSERT INTO tenants (user_id, property_id) 
                                  VALUES ('$user_id', '$property_id')";
            mysqli_query($conn, $queryInsertTenant);

            // Update property status to 'unavailable' once rented
            $queryUpdateProperty = "UPDATE properties SET property_status = 'unavailable' WHERE property_id = '$property_id'";
            mysqli_query($conn, $queryUpdateProperty);

            // Redirect to the tenants index page
            echo "<meta http-equiv='refresh' content='0;url=/rent-master2/admin/?page=tenants/index'>";
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



<div class="container px-lg-5 mb-3">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Add Tenant</h4>
    </header>
    <form id="tenant-form" action="tenants/create.php" method="post" enctype="multipart/form-data">
        <div class="mt-2">
            <label for="user-name" class="form-label">Full Name</label>
            <input type="text" id="user-name" name="user_name" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="user-email" class="form-label">Email</label>
            <input type="email" id="user-email" name="user_email" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="user-phone" class="form-label">Phone Number</label>
            <input type="text" id="user-phone" name="user_phone" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="user-address" class="form-label">Address</label>
            <input type="text" id="user-address" name="user_address" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="user-description" class="form-label">Short Bio</label>
            <textarea id="user-description" name="user_description" class="form-control"></textarea>
        </div>
        <div class="mt-2">
            <label for="user-image">Upload Image</label>
            <input type="file" id="user-image" name="user_image" class="form-control" accept="image/*" required>
        </div>
        <div class="mt-2">
            <label for="property-id" class="form-label">Property</label>
            <select id="property-id" name="property_id" class="form-control" required>
                <option value="" disabled selected>Select Property</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?php echo $property['property_id']; ?>" data-property-name="<?php echo $property['property_name']; ?>"><?php echo $property['property_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="button" class="btn btn-success px-4 rounded-5 mt-3" id="submit-btn">Submit</button>
    </form>
    <a href="?page=tenants/index" class="btn btn-secondary mt-3 rounded-5">Back to Tenants List</a>
</div>

<!-- Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tenant Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Full Name:</strong> <span id="modal-user-name"></span></p>
                <p><strong>Email:</strong> <span id="modal-user-email"></span></p>
                <p><strong>Phone Number:</strong> <span id="modal-user-phone"></span></p>
                <p><strong>Address:</strong> <span id="modal-user-address"></span></p>
                <p><strong>Short Bio:</strong> <span id="modal-user-description"></span></p>
                <p><strong>Property:</strong> <span id="modal-property-name"></span></p>
                <p><strong>Image Preview:</strong></p>
                <img id="modal-image-preview" src="" alt="User Image" class="img-fluid d-none">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirmed-btn">Confirmed</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("submit-btn").addEventListener("click", function() {
        let userName = document.getElementById("user-name").value.trim();
        let userEmail = document.getElementById("user-email").value.trim();
        let userPhone = document.getElementById("user-phone").value.trim();
        let userAddress = document.getElementById("user-address").value.trim();
        let userDescription = document.getElementById("user-description").value.trim();
        let propertySelect = document.getElementById("property-id");
        let propertyName = propertySelect.options[propertySelect.selectedIndex].getAttribute("data-property-name");
        let propertyId = propertySelect.value.trim();
        let fileInput = document.getElementById("user-image");

        // Validation
        if (userName === "" || userEmail === "" || userPhone === "" || userAddress === "" || propertyId === "" || fileInput.files.length === 0) {
            alert("All fields are required!");
            return;
        }

        // Set modal content
        document.getElementById("modal-user-name").innerText = userName;
        document.getElementById("modal-user-email").innerText = userEmail;
        document.getElementById("modal-user-phone").innerText = userPhone;
        document.getElementById("modal-user-address").innerText = userAddress;
        document.getElementById("modal-user-description").innerText = userDescription;
        document.getElementById("modal-property-name").innerText = propertyName;

        let imagePreview = document.getElementById("modal-image-preview");
        let fileReader = new FileReader();

        fileReader.onload = function(event) {
            imagePreview.src = event.target.result;
            imagePreview.classList.remove("d-none");
        };
        fileReader.readAsDataURL(fileInput.files[0]);

        // Show the modal
        let modal = new bootstrap.Modal(document.getElementById("tenantModal"));
        modal.show();
    });

    document.getElementById("confirmed-btn").addEventListener("click", function() {
        document.getElementById("tenant-form").submit();
    });
</script>
