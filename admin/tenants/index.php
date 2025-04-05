<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'rentsystem');

if (!$conn) {
    die("Error: cannot connect to database. " . mysqli_connect_error());
}

// Query to get tenant info joined with users and properties
$query = "
    SELECT * 
    FROM tenants
    JOIN users ON tenants.user_id = users.user_id
    JOIN properties ON tenants.property_id = properties.property_id;
";

$result = mysqli_query($conn, $query);
?>


<div class="container px-lg-5">
    <header class="d-flex justify-content-between mt-3">
        <h4 class="fw-medium">Your Tenants</h4>
        <a href="?page=tenants/create" class="btn btn-primary fw-bold rounded-5 px-4">
            Add Tenant
        </a>
    </header>

    <div class="container mt-3 mb-5">
        <div class="row row-cols-1 gap-5">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col">
                    <div class="row gy-2">
                        <div class="col col-lg-4 col-md-6 col-12 d-flex align-items-start justify-content-center">
                        <img class="rounded-circle " width="75%" style="aspect-ratio: 1/1;"
                                 src="<?php echo htmlspecialchars($row['user_image']); ?>"
                                 alt="Tenant <?php echo htmlspecialchars($row['tenant_id']); ?>">
                        </div>
                        <div class="col col-lg-8 col-md-6 col-12">
                            <div>
                                <h4><?php echo htmlspecialchars($row['user_name']); ?></h4>
                                <p class="opacity-75"><?php echo htmlspecialchars($row['user_description']); ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium">House ID:</td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['property_id']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Tenant ID:</td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['tenant_id']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Email:</td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium">Phone Number:</td>
                                            <td class="text-right opacity-75"><?php echo htmlspecialchars($row['user_phone_number']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bg-body-secondary d-flex justify-content-center gap-2">
                        <a href="?page=tenants/update&tenant_id=<?php echo htmlspecialchars($row['tenant_id']); ?>" class="hover-btn px-3 py-2 text-decoration-none text-black">Edit</a>
                        <a href="#" class="hover-btn px-3 py-2 text-decoration-none text-black">Remove</a>
                        <a href="#" class="hover-btn px-3 py-2 text-decoration-none text-black">View</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

