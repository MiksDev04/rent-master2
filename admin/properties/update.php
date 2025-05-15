<?php
// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function deleteOldImages($conn, $property_id)
{
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/assets/properties/";
    $query = "SELECT * FROM property_images WHERE property_id = '$property_id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $images = mysqli_fetch_assoc($result);
        
        // Delete old images from the folder
        for ($i = 1; $i <= 10; $i++) {
            $imagePath = $images["image$i"];
            if (!empty($imagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
            }
        }
        
        // Clear all image references from database
        $clearQuery = "UPDATE property_images SET ";
        $updates = [];
        for ($i = 1; $i <= 10; $i++) {
            $updates[] = "image$i = NULL";
        }
        $clearQuery .= implode(", ", $updates) . " WHERE property_id = '$property_id'";
        mysqli_query($conn, $clearQuery);
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
    if (!empty($_POST['property_id']) && !empty($_POST['property_name']) && !empty($_POST['location']) && 
        !empty($_POST['description']) && !empty($_POST['date_created']) && !empty($_POST['property_rental_price'])) {

        $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
        $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        
        // Proper NULL handling for coordinates
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : 'NULL';
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : 'NULL';
        
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date_created = mysqli_real_escape_string($conn, $_POST['date_created']);
        $rental_price = mysqli_real_escape_string($conn, $_POST['property_rental_price']);

        // UPDATE statement
        $queryUpdate = "UPDATE properties 
            SET property_name = '$property_name', 
                property_location = '$location', 
                latitude = $latitude,
                longitude = $longitude,
                property_date_created = '$date_created', 
                property_description = '$description',
                property_rental_price = '$rental_price'
            WHERE property_id = '$property_id'";

        // Execute the UPDATE query with error checking
        if (!mysqli_query($conn, $queryUpdate)) {
            die("Error updating property: " . mysqli_error($conn));
        }

        // Handle image uploads if new images were provided
        if (!empty($_FILES['property_images']['name'][0])) {
            // Delete all old images and clear database references
            deleteOldImages($conn, $property_id);
            
            // Prepare image paths for database
            $imagePaths = array_fill(1, 10, NULL);
            
            // Upload new images (up to 10)
            $uploadCount = min(count($_FILES['property_images']['name']), 10);
            for ($i = 0; $i < $uploadCount; $i++) {
                $file = [
                    'name' => $_FILES['property_images']['name'][$i],
                    'type' => $_FILES['property_images']['type'][$i],
                    'tmp_name' => $_FILES['property_images']['tmp_name'][$i],
                    'error' => $_FILES['property_images']['error'][$i],
                    'size' => $_FILES['property_images']['size'][$i]
                ];
                
                $imagePath = uploadSingleImage($file);
                if ($imagePath) {
                    $imagePaths[$i+1] = $imagePath;
                }
            }
            
            // Update image paths in database
            $query = "SELECT * FROM property_images WHERE property_id = '$property_id'";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                // Update existing record with new images
                $updateQuery = "UPDATE property_images SET ";
                $updates = [];
                for ($i = 1; $i <= 10; $i++) {
                    $updates[] = "image$i = " . ($imagePaths[$i] !== NULL ? "'" . mysqli_real_escape_string($conn, $imagePaths[$i]) . "'" : "NULL");
                }
                $updateQuery .= implode(", ", $updates) . " WHERE property_id = '$property_id'";
                mysqli_query($conn, $updateQuery);
            } else {
                // Insert new record with images
                $insertQuery = "INSERT INTO property_images (property_id, image1, image2, image3, image4, image5, image6, image7, image8, image9, image10) 
                                VALUES ('$property_id', ";
                $values = [];
                for ($i = 1; $i <= 10; $i++) {
                    $values[] = $imagePaths[$i] !== NULL ? "'" . mysqli_real_escape_string($conn, $imagePaths[$i]) . "'" : "NULL";
                }
                $insertQuery .= implode(", ", $values) . ")";
                mysqli_query($conn, $insertQuery);
            }
        }

        // Handle amenities
        if (isset($_POST['amenities'])) {
            // First delete existing amenities for this property
            mysqli_query($conn, "DELETE FROM property_amenities WHERE property_id = '$property_id'");
            
            // Insert the new selected amenities
            foreach ($_POST['amenities'] as $amenity_id) {
                $amenity_id = mysqli_real_escape_string($conn, $amenity_id);
                mysqli_query($conn, "INSERT INTO property_amenities (property_id, amenity_id) VALUES ('$property_id', '$amenity_id')");
            }
        } else {
            // If no amenities selected, remove all for this property
            mysqli_query($conn, "DELETE FROM property_amenities WHERE property_id = '$property_id'");
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


<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
        }

        .leaflet-top {
            z-index: 999 !important;
        }

        .map-container {
            margin-bottom: 1rem;
        }

        .current-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .current-images img {
            max-width: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<!-- Property Update Form -->


<div class="container px-lg-5 mb-4">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class="p-2 rounded-circle bg-dark-subtle">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
        </a>
        <h4 class="fw-medium">Property / <?= isset($property) ? 'Update' : 'Add' ?> Property</h4>
    </header>

    <form id="property-form" action="properties/update.php" method="post" enctype="multipart/form-data">
        <!-- <?php if (isset($property)): ?> -->
            <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
        <!-- <?php endif; ?> -->

        <div class="mt-2">
            <label for="property-name" class="form-label">Property Name</label>
            <input type="text" id="property-name" name="property_name" class="form-control"
                value="<?= isset($property) ? htmlspecialchars($property['property_name']) : '' ?>" required>
        </div>

        <div class="mt-2">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control"
                value="<?= isset($property) ? htmlspecialchars($property['property_location']) : '' ?>" required>
        </div>

        <!-- Map Section -->
        <div class="mt-2">
            <label class="form-label">Select Location on Map</label>
            <div class="map-container border rounded">
                <div id="map" style="z-index: 11;"></div>
            </div>
            <small class="text-muted">Click on the map to set the exact location</small>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text" id="latitude" name="latitude" class="form-control"
                    value="<?= isset($property) ? htmlspecialchars($property['latitude']) : '' ?>" readonly required>
            </div>
            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text" id="longitude" name="longitude" class="form-control"
                    value="<?= isset($property) ? htmlspecialchars($property['longitude']) : '' ?>" readonly required>
            </div>
        </div>

        <div class="mt-2">
            <label for="date-created" class="form-label">Date Created</label>
            <input type="date" id="date-created" name="date_created" class="form-control"
                value="<?= isset($property) ? htmlspecialchars($property['property_date_created']) : '' ?>" required>
        </div>

        <div class="mt-2">
            <label for="rental-price" class="form-label">Rental Price (PHP)</label>
            <input type="number" id="rental-price" name="<?= isset($property) ? 'property_rental_price' : 'rental-price' ?>"
                class="form-control" value="<?= isset($property) ? htmlspecialchars($property['property_rental_price']) : '' ?>" required>
        </div>

        <div class="mt-2">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required><?= isset($property) ? htmlspecialchars($property['property_description']) : '' ?></textarea>
        </div>

        <div class="mt-2">
            <label for="property_images" class="form-label">Upload Images</label>
            <input type="file" id="property_images" name="property_images[]" class="form-control" accept="image/*"
                <?= !isset($property) ? 'required' : '' ?> multiple>
            <?php if (isset($images)): ?>
                <div class="current-images mt-2">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <?php if (!empty($images["image$i"])): ?>
                            <img src="<?= htmlspecialchars($images["image$i"]) ?>" alt="Property image <?= $i ?>">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <small class="text-muted">New images will replace existing ones</small>
            <?php endif; ?>
        </div>

        <div class="mt-2">
            <label class="form-label">Amenities</label>
            <div class="d-flex flex-wrap gap-3">
                <?php while ($row = mysqli_fetch_assoc($all_amenities)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $row['amenity_id'] ?>"
                            <?= (isset($selected_amenities) && in_array($row['amenity_id'], $selected_amenities)) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($row['amenity_name']) ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <button type="button" class="btn btn-primary px-4 rounded-5 mt-3" id="submit-btn">
            <?= isset($property) ? 'Update' : 'Submit' ?>
        </button>
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="propertyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Property <?= isset($property) ? 'Update' : 'Creation' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <?= isset($property) ? 'update' : 'create' ?> this property?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-5" id="confirmed-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize the map
        const defaultLat = <?= isset($property) && !empty($property['latitude']) ? $property['latitude'] : '14.5995' ?>;
        const defaultLng = <?= isset($property) && !empty($property['longitude']) ? $property['longitude'] : '120.9842' ?>;

        const map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = null;

        // If editing and has coordinates, add marker
        if (<?= isset($property) && !empty($property['latitude']) ? 'true' : 'false' ?>) {
            marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);
            marker.on('dragend', updateCoordinates);
        }

        map.on('click', function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Get approximate address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('location').value = data.display_name;
                    }
                })
                .catch(error => console.error('Geocoding error:', error));

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {
                    draggable: true
                }).addTo(map);
                marker.on('dragend', updateCoordinates);
            }
        });

        function updateCoordinates() {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(6);
            document.getElementById('longitude').value = position.lng.toFixed(6);
        }

        // Form validation
        document.getElementById("submit-btn").addEventListener("click", function() {
            const requiredFields = [
                'property-name', 'location', 'latitude', 'longitude',
                'date-created', 'rental-price', 'description'
            ];

            let isValid = true;
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            <?php if (!isset($property)): ?>
                if (document.getElementById('property_images').files.length === 0) {
                    isValid = false;
                    document.getElementById('property_images').classList.add('is-invalid');
                } else {
                    document.getElementById('property_images').classList.remove('is-invalid');
                }
            <?php endif; ?>

            if (!isValid) {
                alert("Please fill all required fields and select a location on the map!");
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById("propertyModal"));
            modal.show();
        });

        document.getElementById("confirmed-btn").addEventListener("click", function() {
            document.getElementById("property-form").submit();
        });
    });
</script>