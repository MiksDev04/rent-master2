<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem"; // Replace with your actual database name

// Establish the connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);
    $user_phone_number = mysqli_real_escape_string($conn, $_POST['user_phone_number']);
    $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);
    $user_description = mysqli_real_escape_string($conn, $_POST['user_description']);
    
    // Handle image upload
    $user_image = ''; // Default to an empty string if no image is uploaded
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        // Define the target directory
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/rent-master2/admin/assets/tenants/";
        
        // Get the image file extension
        $image_file_type = strtolower(pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION));
        
        // Define a unique file name
        $target_file = $target_dir . uniqid() . '.' . $image_file_type;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file)) {
            $user_image = $target_file; // Store the file path in the database
        } else {
            echo "<div class='alert alert-danger mt-3'>Sorry, there was an error uploading the file.</div>";
        }
    }

    // Default user status
    $user_status = 'visitor'; // Default status

    // Prepare the SQL query
    $sql = "INSERT INTO users (user_name, user_email, user_password, user_phone_number, user_address, user_description, user_image, user_status)
            VALUES ('$user_name', '$user_email', '$user_password', '$user_phone_number', '$user_address', '$user_description', '$user_image', '$user_status')";

    // Execute the query and check for success
    if (mysqli_query($conn, $sql)) {
        echo "<meta http-equiv='refresh' content='0;url=/rent-master2/client/?page=src/home'>";
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Close the connection
mysqli_close($conn);
?>

<div class="container mt-2">
    <h2 class="mb-4">Insert User</h2>
    <form method="post" enctype="multipart/form-data">
        <div class=" mt-2">
            <label class=" form-label" for="user_name">Name:</label>
            <input type="text" class="form-control " id="user_name" name="user_name" required>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_email">Email:</label>
            <input type="email" class="form-control" id="user_email" name="user_email" required>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_password">Password:</label>
            <input type="password" class="form-control" id="user_password" name="user_password" required>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_phone_number">Phone Number:</label>
            <input type="text" class="form-control" id="user_phone_number" name="user_phone_number" required>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_address">Address:</label>
            <input type="text" class="form-control" id="user_address" name="user_address" required>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_description">Description:</label>
            <textarea class="form-control" id="user_description" name="user_description"></textarea>
        </div>

        <div class=" mt-2">
            <label class=" form-label" for="user_image">Profile Image:</label>
            <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary my-2">Submit</button>
    </form>
</div>