<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rent Master</title>
    <!-- <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>"> -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="/rent-master/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>

<body>
    <!-- Sidebar -->
     <?php 
        include('sidebar.php'); // Include the sidebar file
     ?>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="/rent-master2/client/src/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Main Content -->
    <main class="content p-0" >
        <?php include('header.php');?>
        <div class=" main-content">
            <!-- Content Goes Here -->
            <?php
            $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'dashboard';

            $allowed_pages = [
                'dashboard/index',
                'properties/index',
                'properties/create',
                'properties/update',
                'properties/delete',
                'properties/view',
                'tenants/index',
                'tenants/create',
                'tenants/update',
                'tenants/delete',
                'tenants/view',
                'payments/index',
                'reports/index',
                'settings/index',
                'maintenance/index', 
                'payments/paid',
                'payments/update',
                'payments/delete',
                'account/index',
                'notification-link'
            ];
            if (!in_array($page, $allowed_pages)) {
                $page = 'dashboard/index';
            }
            include_once "$page.php";
            ?>
        </div>
    </main>


    <!-- <script src="./js/script.js?v=<?php echo time(); ?>" defer></script> -->
    <script src="./js/script.js"></script>
    <!-- Bootstrap JS -->
    <script src="/rent-master/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
</body>

</html>