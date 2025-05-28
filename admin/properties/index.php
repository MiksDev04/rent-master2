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

// Check if a filter is applied via the form submission
$status_filter = 'available'; // Default filter
$property_id =  null; // Get property_id from GET request if available
$query = "SELECT * FROM properties WHERE ";

if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $status_filter = $_GET['property_status'] ?? 'available'; // Default to 'available' if not set
    $query .= " property_id = '$property_id' AND";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['filter'] == 'changeStatus') {
    $status_filter = $_POST['status'];
    $query = "SELECT * FROM properties WHERE ";
}
// Adjust the query based on the filter
$query .= " property_status = '$status_filter' ORDER BY property_date_created DESC"; // Order by date created
$result = mysqli_query($conn, $query);


?>

<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Your Properties</h4>
        <a href="?page=properties/create" class="btn btn-primary fw-bold rounded-5 px-4">Add Property</a>
    </header>
    <?php if (isset($_GET['message'])): ?>
        <div id="addSuccess" class="alert alert-success alert-dismissible fade show slide-in position-fixed top-0 start-50 translate-middle-x mt-3 shadow" role="alert" style="z-index: 1055; min-width: 300px;">
            <?= htmlspecialchars($_GET['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="mt-2">
        <form method="POST">
            <input type="hidden" name="filter" value="changeStatus">
            <div class="d-flex gap-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="available" <?php echo ($status_filter == 'available') ? 'selected' : ''; ?>>Available</option>
                    <option value="unavailable" <?php echo ($status_filter == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>
        </form>
    </div>

    <div class="container mt-3 mb-5">
        <div class="row row-cols-1 gap-5">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <div class="col">
                        <div class="row gy-2">
                            <div class="col col-lg-4 col-md-6 col-12">
                                <?php
                                $id = $row['property_id'];
                                $image_query = mysqli_query($conn, "SELECT image1 FROM property_images WHERE property_id = '$id' LIMIT 1;");
                                $image_row = mysqli_fetch_assoc($image_query);
                                $image_path = $image_row ? $image_row['image1'] : 'default.jpg'; // fallback if no image
                                ?>
                                <img class="w-100" src="<?php echo htmlspecialchars($image_path); ?>" alt="Property Image">
                            </div>
                            <div class="col col-lg-8 col-md-6 col-12">
                                <div>
                                    <div class=" d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                            <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                                        </svg>
                                        <div class=" fs-4 fw-medium">
                                            <?php echo htmlspecialchars($row['property_name']); ?>
                                        </div>
                                    </div>
                                    <p class="opacity-75"><?php echo htmlspecialchars($row['property_description']); ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z" />
                                                    </svg>
                                                    House ID:
                                                </td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_id']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                                                    </svg>
                                                    Location:
                                                </td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars(mb_strimwidth($row['property_location'], 0, 60, '...')); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M96 32l0 32L48 64C21.5 64 0 85.5 0 112l0 48 448 0 0-48c0-26.5-21.5-48-48-48l-48 0 0-32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 32L160 64l0-32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192L0 192 0 464c0 26.5 21.5 48 48 48l352 0c26.5 0 48-21.5 48-48l0-272z" />
                                                    </svg>
                                                    Date Created:
                                                </td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_date_created']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                        <path d="M64 32C46.3 32 32 46.3 32 64l0 64c-17.7 0-32 14.3-32 32s14.3 32 32 32l0 32c-17.7 0-32 14.3-32 32s14.3 32 32 32l0 64 0 96c0 17.7 14.3 32 32 32s32-14.3 32-32l0-64 80 0c68.4 0 127.7-39 156.8-96l19.2 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-.7 0c.5-5.3 .7-10.6 .7-16s-.2-10.7-.7-16l.7 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-19.2 0C303.7 71 244.4 32 176 32L64 32zm190.4 96L96 128l0-32 80 0c30.5 0 58.2 12.2 78.4 32zM96 192l190.9 0c.7 5.2 1.1 10.6 1.1 16s-.4 10.8-1.1 16L96 224l0-32zm158.4 96c-20.2 19.8-47.9 32-78.4 32l-80 0 0-32 158.4 0z" />
                                                    </svg>
                                                    Rental Price:
                                                </td>
                                                <td class="text-right opacity-75"> PHP <?php echo number_format(htmlspecialchars($row['property_rental_price']), 2, '.', ','); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="bg-body-secondary d-flex justify-content-center gap-2">
                            <a href="?page=properties/update&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">Edit</a>
                            <a href="?page=properties/delete&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">Remove</a>
                            <a href="?page=properties/view&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">View</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='text-center text-bg-warning'>No record found</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
?>