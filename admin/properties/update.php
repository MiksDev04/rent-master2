<?php
// Database connection 
require_once '../database/config.php';

function deleteOldImages($conn, $property_id)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/assets/properties/";
    $query = "SELECT * FROM property_images WHERE property_id = '$property_id'";
    $result = mysqli_query($conn, $query);
    $images = mysqli_fetch_assoc($result);

    // Delete old images from the folder
    for ($i = 1; $i <= 10; $i++) {
        $imagePath = $images["image$i"];
        if (!empty($imagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
        }
    }
}

function uploadSingleImage($file)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/assets/properties/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];

    if (!getimagesize($file["tmp_name"])) {
        return null;
    }

    if ($file["size"] > 5000000 || !in_array($imageFileType, $allowedFormats)) {
        return null;
    }

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "/rent-master2/admin/assets/properties/" . $fileName;
    }

    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['property_id']) && !empty($_POST['property_name']) && !empty($_POST['location']) && !empty($_POST['description']) && !empty($_POST['date_created']) && !empty($_POST['property_rental_price'])) {

        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
        $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date_created = mysqli_real_escape_string($conn, $_POST['date_created']);
        $rental_price = mysqli_real_escape_string($conn, $_POST['property_rental_price']);

        // Update main property table
        $queryUpdate = "UPDATE properties 
                        SET property_name = '$property_name', property_location = '$location', 
                            property_date_created = '$date_created', property_description = '$description',
                            property_rental_price = '$rental_price'
                        WHERE property_id = '$property_id'";
        mysqli_query($conn, $queryUpdate);

        // --- Update Images ---
        if (!empty($_FILES['property_images']['name'][0])) {
            // Delete previous images in the folder
            deleteOldImages($conn, $property_id);

            $images = $_FILES['property_images'];
            $imagePaths = [];

            for ($i = 0; $i < min(10, count($images['name'])); $i++) {
                $file = [
                    'name' => $images['name'][$i],
                    'type' => $images['type'][$i],
                    'tmp_name' => $images['tmp_name'][$i],
                    'error' => $images['error'][$i],
                    'size' => $images['size'][$i]
                ];

                $imagePath = uploadSingleImage($file);
                if ($imagePath) {
                    $imagePaths[] = $imagePath;
                }
            }

            while (count($imagePaths) < 10) {
                $imagePaths[] = null;
            }

            // Check if entry exists
            $check = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = '$property_id'");
            if (mysqli_num_rows($check) > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE property_images 
                    SET image1=?, image2=?, image3=?, image4=?, image5=?, image6=?, image7=?, image8=?, image9=?, image10=? 
                    WHERE property_id=?");
                $stmt->bind_param(
                    "ssssssssssi",
                    $imagePaths[0],
                    $imagePaths[1],
                    $imagePaths[2],
                    $imagePaths[3],
                    $imagePaths[4],
                    $imagePaths[5],
                    $imagePaths[6],
                    $imagePaths[7],
                    $imagePaths[8],
                    $imagePaths[9],
                    $property_id
                );
            } else {
                // Insert (if somehow missing)
                $stmt = $conn->prepare("INSERT INTO property_images 
                    (property_id, image1, image2, image3, image4, image5, image6, image7, image8, image9, image10) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "issssssssss",
                    $property_id,
                    $imagePaths[0],
                    $imagePaths[1],
                    $imagePaths[2],
                    $imagePaths[3],
                    $imagePaths[4],
                    $imagePaths[5],
                    $imagePaths[6],
                    $imagePaths[7],
                    $imagePaths[8],
                    $imagePaths[9]
                );
            }
            $stmt->execute();
            $stmt->close();
        }

        // --- Update Amenities ---
        mysqli_query($conn, "DELETE FROM property_amenities WHERE property_id = '$property_id'");
        if (!empty($_POST['amenities'])) {
            foreach ($_POST['amenities'] as $amenity_id) {
                $amenity_id = (int)$amenity_id;
                mysqli_query($conn, "INSERT INTO property_amenities (property_id, amenity_id) VALUES ($property_id, $amenity_id)");
            }
        }

        header("Location: /rent-master2/admin/?page=properties/index");
        exit();
    } else {
        echo "All fields are required";
    }
}

// Fetch existing property and amenities
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $query = "SELECT * FROM properties WHERE property_id = '$property_id'";
    $result = mysqli_query($conn, $query);
    $property = mysqli_fetch_assoc($result);

    $images_result = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = '$property_id'");
    $images = mysqli_fetch_assoc($images_result);

    $amenities_result = mysqli_query($conn, "SELECT amenity_id FROM property_amenities WHERE property_id = '$property_id'");
    $selected_amenities = [];
    while ($row = mysqli_fetch_assoc($amenities_result)) {
        $selected_amenities[] = $row['amenity_id'];
    }

    $all_amenities = mysqli_query($conn, "SELECT * FROM amenities");
}
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
        <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">

        <div class="mt-2">
            <label for="property-name" class="form-label">Property Name</label>
            <input type="text" name="property_name" class="form-control" value="<?= $property['property_name'] ?>" required>
        </div>

        <div class="mt-2">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="<?= $property['property_location'] ?>" required>
        </div>

        <div class="mt-2">
            <label for="date-created" class="form-label">Date Created</label>
            <input type="date" name="date_created" class="form-control" value="<?= $property['property_date_created'] ?>" required>
        </div>

        <div class="mt-2">
            <label for="rental-price" class="form-label">Rental Price (PHP)</label>
            <input type="number" name="property_rental_price" class="form-control" value="<?= $property['property_rental_price'] ?>" required>
        </div>

        <div class="mt-2">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" required><?= $property['property_description'] ?></textarea>
        </div>

        <div class="mt-2">
            <label class="form-label">Upload New Images</label>
            <input type="file" name="property_images[]" class="form-control" accept="image/*" multiple>
        </div>

        <?php if ($images): ?>
            <div class="mt-3">
                <label class="form-label">Current Images:</label><br>
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    $img = $images["image$i"];
                    if (!empty($img)) {
                        echo "<img src='$img' style='max-width: 100px; margin: 5px; border: 1px solid #ccc;'>";
                    }
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="mt-2">
            <label class="form-label">Amenities</label>
            <div class="d-flex flex-wrap gap-3">
                <?php while ($row = mysqli_fetch_assoc($all_amenities)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $row['amenity_id'] ?>"
                            <?= in_array($row['amenity_id'], $selected_amenities) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $row['amenity_name'] ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 rounded-5 mt-3">Update</button>
    </form>

</div>
<!-- Confirmation Modal -->
<div class="modal fade" id="propertyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update this property?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-5">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("submit-btn").addEventListener("click", function() {
        let modal = new bootstrap.Modal(document.getElementById("propertyModal"));
        modal.show();
    });
</script>
