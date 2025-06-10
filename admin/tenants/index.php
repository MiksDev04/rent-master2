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

// Query to get tenant info joined with users and properties, including date created and terminated date
// Query to get tenant info joined with users and properties, including date created and terminated date
$query = "
    SELECT tenants.*, users.*, properties.*, tenants.tenant_date_created, tenants.tenant_terminated_at
    FROM tenants
    JOIN users ON tenants.user_id = users.user_id
    JOIN properties ON tenants.property_id = properties.property_id
    WHERE tenants.tenant_status = 'active' 
";

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];
    $query .= " AND tenant_id = '$tenant_id'";
}


$result = mysqli_query($conn, $query);
?>

<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Your Tenants</h4>
        <a href="?page=tenants/create" class="btn btn-primary fw-bold rounded-5 px-4">
            Add Tenant
        </a>
    </header>
    <?php if (isset($_GET['message'])): ?>
        <div id="addSuccess" class="alert alert-success alert-dismissible fade show slide-in position-fixed top-0 start-50 translate-middle-x mt-3 shadow" role="alert" style="z-index: 1055; min-width: 300px;">
            <?= htmlspecialchars($_GET['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="container mt-3 mb-5">
        <div class="row row-cols-1 gap-5">
            <?php if (mysqli_num_rows($result) == 0) : ?>
                <div class="text-center text-bg-warning">No record found</div>
            <?php endif; ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col">
                    <div class="row gy-2 ">
                        <div class="col col-lg-4 col-md-6 col-12 d-flex align-items-start justify-content-center">
                            <img class="rounded-circle " width="75%" style="aspect-ratio: 1/1;"
                                src="<?php echo htmlspecialchars($row['user_image']); ?>"
                                alt="Tenant <?php echo htmlspecialchars($row['tenant_id']); ?>">
                        </div>
                        <div class="col col-lg-8 col-md-6 col-12">
                            <div>
                                <div class=" d-flex align-items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="currentColor" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c.2 35.5-28.5 64.3-64 64.3l-320.4 0c-35.3 0-64-28.7-64-64l0-160.4-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24zM352 224a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zm-96 96c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16l192 0c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80l-64 0z" />
                                    </svg>
                                    <div class=" fw-medium fs-4"><?php echo htmlspecialchars($row['user_name']); ?></div>
                                </div>
                                <p class="opacity-75"><?php echo htmlspecialchars($row['user_description']); ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 512 512">
                                                    <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z" />
                                                </svg>
                                                Property Renting:
                                            </td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_name']); ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="fw-medium d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" />
                                                </svg>
                                                Email:
                                            </td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" width="20px" fill="currentColor" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M413.6 174.4c6.7 6.7 6.7 17.5 0 24.2c-6.7 6.7-17.5 6.7-24.2 0L256 66.1l-133.4 132.5c-6.7 6.7-17.5 6.7-24.2 0c-6.7-6.7-6.7-17.5 0-24.2L231.8 33.9c6.7-6.7 17.5-6.7 24.2 0L413.6 174.4zM256 128c-35.3 0-64 28.7-64 64c0 8.8 7.2 16 16 16c8.8 0 16-7.2 16-16c0-17.7 14.3-32 32-32c8.8 0 16-7.2 16-16c0-8.8-7.2-16-16-16zm112 160c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16l192 0c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80l-64 0z" />
                                                </svg>
                                                Created On:
                                            </td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['tenant_date_created']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bg-body-secondary d-flex justify-content-center gap-2">
                        <a href="?page=tenants/update&tenant_id=<?php echo htmlspecialchars($row['tenant_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">Edit</a>
                        <a href="?page=tenants/delete&tenant_id=<?php echo htmlspecialchars($row['tenant_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">Remove</a>
                        <a href="?page=tenants/view&tenant_id=<?php echo htmlspecialchars($row['tenant_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none nav-link">View</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php mysqli_close($conn); ?>