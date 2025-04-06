<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    echo "Error: cannot connect to database" . mysqli_connect_error();
}

// Check if a filter is applied via the form submission
$status_filter = isset($_POST['status']) ? $_POST['status'] : 'available';

// Adjust the query based on the filter
$query = "SELECT * FROM properties WHERE property_status = '$status_filter' ORDER BY property_date_created DESC";
$result = mysqli_query($conn, $query);


?>

<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Your Properties</h4>
        <a href="?page=properties/create" class="btn btn-primary fw-bold rounded-5 px-4">Add Property</a>
    </header>

    <div class="mt-2">
        <form method="POST">
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
                                <img class="w-100" src="<?php echo htmlspecialchars($row['property_image']); ?>" alt="Property Image">
                            </div>
                            <div class="col col-lg-8 col-md-6 col-12">
                                <div>
                                    <h4><?php echo htmlspecialchars($row['property_name']); ?></h4>
                                    <p class="opacity-75"><?php echo htmlspecialchars($row['property_description']); ?></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium">House ID:</td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_id']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Owner Name:</td>
                                                <td class="text-right opacity-75">Jerico Caricot</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Location:</td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_location']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Date Created:</td>
                                                <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_date_created']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="bg-body-secondary d-flex justify-content-center gap-2">
                            <a href="?page=properties/update&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none text-black">Edit</a>
                            <a href="?page=properties/delete&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none text-black">Remove</a>
                            <a href="?page=properties/view&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none text-black">View</a>
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
