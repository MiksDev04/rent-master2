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

// Get tenant details if tenant ID is provided
if (isset($_GET['tenant_id'])) {
    $tenant_id = mysqli_real_escape_string($conn, $_GET['tenant_id']);
    $queryTenant = "SELECT u.user_id, u.user_name, u.user_email, u.user_phone_number, u.user_address, u.user_description, u.user_image, t.property_id
                    FROM tenants t
                    JOIN users u ON t.user_id = u.user_id
                    WHERE t.tenant_id = '$tenant_id'";

    $tenantResult = mysqli_query($conn, $queryTenant);
    $tenant = mysqli_fetch_assoc($tenantResult);
}

function uploadImage($file, $existingImage = null)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/tenants/images/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // If no file was uploaded, return the existing image
    if ($file["error"] === 4 || empty($file["tmp_name"])) {
        return $existingImage;
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

    // Only delete existing image if a new one is being uploaded successfully
    if ($uploadOk == 1 && file_exists($existingImage)) {
        unlink($existingImage);
    }

    if ($uploadOk == 1 && move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "/rent-master2/admin/tenants/images/" . $fileName;
    } else {
        return $existingImage;  // Return existing image if upload failed
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


        // Get the existing image path
        $existingImage = $tenant['user_image'];

        // Call the uploadImage function to handle the image upload
        $user_image = uploadImage($_FILES['user_image'], $existingImage); // Keep existing image if no new one is uploaded

        if ($user_image) {
            // Update user data
            $queryUpdateUser = "UPDATE users SET 
                                    user_name = '$user_name', 
                                    user_email = '$user_email', 
                                    user_phone_number = '$user_phone', 
                                    user_address = '$user_address', 
                                    user_description = '$user_description', 
                                    user_image = '$user_image' 
                                WHERE user_id = '{$tenant['user_id']}'";
            mysqli_query($conn, $queryUpdateUser);

            // Update tenant data
            $queryUpdateTenant = "UPDATE tenants SET 
                                    property_id = '$property_id' 
                                  WHERE tenant_id = '$tenant_id'";
            mysqli_query($conn, $queryUpdateTenant);

            // Update property status to 'unavailable' if needed
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
<header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=tenants/index" class=" p-2 rounded-circle bg-dark-subtle" width="2rem" height="2rem">
            <svg xmlns="http://www.w3.org/2000/svg"  height="24px" width="24px" fill="grey" viewBox="0 0 448 512">!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.<path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
        </a>
        <h4 class="fw-medium ">Tenants / Update Tenant</h4>
    </header>
    <form id="tenant-form" action="" method="post" enctype="multipart/form-data">
        <div class="mt-2">
            <label for="user-name" class="form-label">Full Name</label>
            <input type="text" id="user-name" name="user_name" class="form-control" value="<?php echo $tenant['user_name']; ?>" required>
        </div>
        <div class="mt-2">
            <label for="user-email" class="form-label">Email</label>
            <input type="email" id="user-email" name="user_email" class="form-control" value="<?php echo $tenant['user_email']; ?>" required>
        </div>
        <div class="mt-2">
            <label for="user-phone" class="form-label">Phone Number</label>
            <input type="text" id="user-phone" name="user_phone" class="form-control" value="<?php echo $tenant['user_phone_number']; ?>" required>
        </div>
        <div class="mt-2">
            <label for="user-address" class="form-label">Address</label>
            <input type="text" id="user-address" name="user_address" class="form-control" value="<?php echo $tenant['user_address']; ?>" required>
        </div>
        <div class="mt-2">
            <label for="user-description" class="form-label">Short Bio</label>
            <textarea id="user-description" name="user_description" class="form-control"><?php echo $tenant['user_description']; ?></textarea>
        </div>
        <div class="mt-2">
            <label for="user-image">Upload Image</label>
            <input type="file" id="user-image" name="user_image" class="form-control" accept="image/*">
        </div>
        <div class="mt-2">
            <label for="user-image-preview" class=" w-100">Current Image</label>
            <img src="<?php echo $tenant['user_image']; ?>" id="user-image-preview" class="img-fluid" width="200">
        </div>
        <div class="mt-2">
            <label for="property-id" class="form-label">Property</label>
            <select id="property-id" name="property_id" class="form-control" required>
                <option value="" disabled>Select Property</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?php echo $property['property_id']; ?>"
                        <?php echo ($tenant['property_id'] == $property['property_id']) ? 'selected' : ''; ?>>
                        <?php echo $property['property_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="button" class="btn btn-success px-4 rounded-5 mt-3" id="submit-btn" data-bs-toggle="modal" data-bs-target="#tenantModal">Submit</button>
    </form>
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
                <img id="modal-image-preview" src="" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="submitForm()">Confirm Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("submit-btn").addEventListener("click", function() {
        // Populate modal with form data
        document.getElementById("modal-user-name").innerText = document.getElementById("user-name").value;
        document.getElementById("modal-user-email").innerText = document.getElementById("user-email").value;
        document.getElementById("modal-user-phone").innerText = document.getElementById("user-phone").value;
        document.getElementById("modal-user-address").innerText = document.getElementById("user-address").value;
        document.getElementById("modal-user-description").innerText = document.getElementById("user-description").value;
        document.getElementById("modal-property-name").innerText = document.getElementById("property-id").selectedOptions[0].text;

        const fileInput = document.getElementById("user-image");
        const modalImagePreview = document.getElementById("modal-image-preview");

        if (fileInput.files && fileInput.files[0]) {
            // New image selected; read and display it
            const reader = new FileReader();
            reader.onload = function(e) {
                modalImagePreview.src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            // No new image selected; use existing image
            modalImagePreview.src = document.getElementById("user-image-preview").src;
        }

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('tenantModal'));
        myModal.show();
    });

    function submitForm() {
        document.getElementById("tenant-form").submit();
    }
</script>