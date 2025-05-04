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
    if (!empty($_POST['property_name']) && !empty($_POST['location']) && !empty($_POST['description']) && !empty($_POST['date_created']) && !empty($_POST['rental-price'])) {

        // In your create.php file, update the INSERT statement:
        $property_name = mysqli_real_escape_string($conn, $_POST['property_name']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $latitude = isset($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = isset($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $date_created = mysqli_real_escape_string($conn, $_POST['date_created']);
        $rental_price = mysqli_real_escape_string($conn, $_POST['rental-price']);

        $queryInsert = "INSERT INTO properties 
    (property_name, property_location, latitude, longitude, 
     property_date_created, property_description, property_rental_price) 
    VALUES ('$property_name', '$location', $latitude, $longitude, 
            '$date_created', '$description', '$rental_price')";
        mysqli_query($conn, $queryInsert);
        $property_id = mysqli_insert_id($conn);

        if (!empty($_FILES['property_images']['name'][0])) {
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

            $stmt = $conn->prepare("INSERT INTO property_images (property_id, image1, image2, image3, image4, image5, image6, image7, image8, image9, image10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            $stmt->execute();
            $stmt->close();
        }

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

$result = mysqli_query($conn, "SELECT * FROM amenities");
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
mysqli_close($conn);
?>




<head>
    <!-- Add these in the head section or before your form -->
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
    </style>
</head>


<div class="container px-lg-5 mb-4">
    <header class="d-flex align-items-center mt-3 gap-2">
        <a href="?page=properties/index" class=" p-2 rounded-circle bg-dark-subtle" width="2rem" height="2rem">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="grey" viewBox="0 0 448 512">!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
        </a>
        <h4 class="fw-medium ">Property / Add Property</h4>
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
        <!-- Add these new fields for map and coordinates -->
        <div class="mt-2">
            <label class="form-label">Select Location on Map</label>
            <div class="map-container border rounded">
                <div id="map" style="z-index:10;"></div>
            </div>
            <small class="text-muted">Click on the map to set the exact location</small>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text" id="latitude" name="latitude" class="form-control" readonly required>
            </div>
            <div class="col-md-6">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text" id="longitude" name="longitude" class="form-control" readonly required>
            </div>
        </div>
        <div class="mt-2">
            <label for="date-created" class="form-label">Date Created</label>
            <input type="date" id="date-created" name="date_created" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="rental-price" class="form-label">Rental Price (PHP)</label>
            <input type="number" id="rental-price" name="rental-price" class="form-control" required>
        </div>
        <div class="mt-2">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required></textarea>
        </div>
        <div class="mt-2">
            <label for="property_images" class="form-label">Upload Images</label>
            <input type="file" id="property_images" name="property_images[]" class="form-control" accept="image/*" multiple required>
        </div>
        <div class="mt-2">
            <label class="form-label">Amenities</label>
            <div class="d-flex flex-wrap gap-3">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $row['amenity_id'] ?>" id="amenity<?= $row['amenity_id'] ?>">
                        <label class="form-check-label" for="amenity<?= $row['amenity_id'] ?>"><?= $row['amenity_name'] ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <button type="button" class="btn btn-primary px-4 rounded-5 mt-3" id="submit-btn">Submit</button>

    </form>


</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="propertyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Property Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to create this new property?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-5" id="confirmed-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize the map
    document.addEventListener("DOMContentLoaded", function() {
        // Default to Manila coordinates
        const map = L.map('map').setView([14.5995, 120.9842], 13);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add marker that can be dragged
        let marker = null;

        map.on('click', function(e) {
            const {
                lat,
                lng
            } = e.latlng;

            // Update the form fields
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Update the location input with approximate address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('location').value = data.display_name;
                    }
                });

            // Update or create marker
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {
                    draggable: true
                }).addTo(map);
                marker.on('dragend', function() {
                    const position = marker.getLatLng();
                    document.getElementById('latitude').value = position.lat.toFixed(6);
                    document.getElementById('longitude').value = position.lng.toFixed(6);
                });
            }
        });

        // Update validation to include coordinates
        document.getElementById("submit-btn").addEventListener("click", function() {
            let propertyName = document.getElementById("property-name").value.trim();
            let location = document.getElementById("location").value.trim();
            let latitude = document.getElementById("latitude").value;
            let longitude = document.getElementById("longitude").value;
            let description = document.getElementById("description").value.trim();
            let dateCreated = document.getElementById("date-created").value;
            let rentalPrice = document.getElementById("rental-price").value;
            let fileInput = document.getElementById("property_images");

            if (
                propertyName === "" ||
                location === "" ||
                latitude === "" ||
                longitude === "" ||
                description === "" ||
                dateCreated === "" ||
                rentalPrice === "" ||
                fileInput.files.length === 0
            ) {
                alert("All fields are required, including selecting a location on the map!");
                return;
            }

            let modal = new bootstrap.Modal(document.getElementById("propertyModal"));
            modal.show();
        });
    });
    document.getElementById("submit-btn").addEventListener("click", function() {
        // Get values from the form
        let propertyName = document.getElementById("property-name").value.trim();
        let location = document.getElementById("location").value.trim();
        let description = document.getElementById("description").value.trim();
        let dateCreated = document.getElementById("date-created").value;
        let rentalPrice = document.getElementById("rental-price").value;
        let fileInput = document.getElementById("property_images");

        // Simple validation
        if (
            propertyName === "" ||
            location === "" ||
            description === "" ||
            dateCreated === "" ||
            rentalPrice === "" ||
            fileInput.files.length === 0
        ) {
            alert("All fields are required!");
            return;
        }

        // Show confirmation modal
        let modal = new bootstrap.Modal(document.getElementById("propertyModal"));
        modal.show();
    });

    document.getElementById("confirmed-btn").addEventListener("click", function() {
        // Submit the form after confirmation
        document.getElementById("property-form").submit();
    });
</script>