<?php
require '../database/config.php';

$user_id = $_SESSION['user_id'] ?? null;
$message = null;
$user = null;
$application_status = null;

// Separate: FORM SUBMISSION HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $stmt = $conn->prepare("SELECT user_role FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    // Check existing application
    $check = $conn->prepare("SELECT landlord_status FROM landlords WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result()->fetch_assoc();
    $application_status = $result['landlord_status'] ?? null;

    if ($role === 'tenant') {
        header("Location: /rent-master2/client/?page=src/about&message");
        exit();
    } elseif ($application_status) {
        header("Location: /rent-master2/client/?page=src/about&message");
        exit();
    } else {
        $insert = $conn->prepare("INSERT INTO landlords (user_id) VALUES (?)");
        $insert->bind_param("i", $user_id);
        if ($insert->execute()) {
            $application_status = 'pending';
            header("Location: /rent-master2/client/?page=src/about&message");
            exit();
        } else {
            header("Location: /rent-master2/client/?page=src/about&message");
            exit();
        }
    }
}

// Separate: DISPLAY USER INFO + STATUS
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$application_status) {
        $check = $conn->prepare("SELECT landlord_status FROM landlords WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $res = $check->get_result()->fetch_assoc();
        $application_status = $res['landlord_status'] ?? null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .system-hero {
            background-color: #f0f8ff;
            padding: 60px 0;
            /* margin-bottom: 40px; */
            border-bottom: 1px solid #dee2e6;
        }

        .system-feature {
            border-left: 4px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .system-feature:hover {
            transform: translateX(5px);
        }

        .user-card {
            transition: all 0.3s ease;
            height: 100%;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .benefit-icon {
            color: #0d6efd;
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <!-- System Hero Section -->
    <section class="system-hero">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">The Rent Master System</h1>
            <p class="lead">A comprehensive solution for modern rental property management</p>
        </div>
    </section>

    <div class="container py-5">
        <h2 class="mb-4">Apply to Become a Landlord</h2>

        <?php if (!$user_id): ?>
            <div class="alert alert-info">
                <h5>Please <a href="?page=src/login">log in</a> to apply.</h5>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Note: Tenant cannot apply as a landlord</div>
            <form method="POST" action="" class="mb-3" id="landlordForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['user_name']) ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['user_email']) ?>" disabled>
                    </div>
                </div>
                <?php if (!$application_status && $user['user_role'] !== 'tenant'): ?>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#termsModal" class="btn btn-primary px-4">Submit Application</button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary px-4" disabled>Submit Application</button>
                <?php endif; ?>
            </form>


            <?php if ($application_status): ?>
                <?php if ($application_status === 'approved'): ?>
                    <div class="alert alert-success">
                        You are already a verified <strong>landlord</strong>. Welcome aboard.
                    </div>
                <?php elseif ($application_status === 'pending'): ?>
                    <div class="alert alert-warning">
                        Your landlord application is still <strong>pending</strong>. Please wait for admin approval.
                    </div>
                <?php endif; ?>
            <?php endif; ?>


        <?php endif; ?>
    </div>
    <!-- Team Members -->
    <section class="container mb-5">
        <h2 class="text-center fw-bold mb-4"><i class="fas fa-users me-2"></i>BSIT 2A Team</h2>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-search me-2 text-primary"></i>Anareta, Yzabel - Tester</li>
                    <li class="list-group-item"><i class="fas fa-pen-fancy me-2 text-primary"></i>Austria, Karl Matthew - Technical Writer</li>
                    <li class="list-group-item"><i class="fas fa-code me-2 text-primary"></i>Caricot, Jericho - Front-end Developer</li>
                    <li class="list-group-item"><i class="fas fa-crown me-2 text-primary"></i>Dela Rosa, Jan Patrick - Leader</li>
                    <li class="list-group-item"><i class="fas fa-server me-2 text-primary"></i>Gapasan, Miko - Back-end Developer</li>
                    <li class="list-group-item"><i class="fas fa-pen-fancy me-2 text-primary"></i>Miranda, Jasfer Ryle - Technical Writer</li>
                    <li class="list-group-item"><i class="fas fa-pen-fancy me-2 text-primary"></i>Ogerio, Margarette - Technical Writer</li>
                    <li class="list-group-item"><i class="fas fa-user-shield me-2 text-primary"></i>Pasigan, Chinee - Co-Leader</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true" style="z-index: 111111;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Landlord Responsibilities</h6>
                    <p>As a landlord on Rent Master, you agree to:</p>
                    <ul>
                        <li>Provide accurate information about your properties</li>
                        <li>Respond to tenant inquiries in a timely manner</li>
                        <li>Maintain your properties in good condition</li>
                        <li>Abide by all local rental laws and regulations</li>
                    </ul>

                    <h6 class="mt-4">Fees and Payments</h6>
                    <p>Rent Master charges a 5% service fee on all successful rentals.</p>

                    <h6 class="mt-4">Termination</h6>
                    <p>We reserve the right to remove landlords who violate our terms of service.</p>
                </div>
                <div class="modal-footer">
                    <button id="submitLandlordRequest" type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>
    <!-- System Overview -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">System Overview</h2>
                <p>Rent Master is a specialized property rental management system designed to streamline operations for landlords and property managers while enhancing the rental experience for tenants.</p>
                <p>The system provides a centralized platform to manage all aspects of rental properties, from tenant screening and lease management to payment processing and maintenance requests.</p>
                <p>Built with security and efficiency in mind, Rent Master helps property owners maintain compliance while reducing administrative overhead.</p>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/about-house.jpg" alt="System Overview" class="img-fluid rounded shadow">
            </div>
        </div>
    </section>

    <!-- Core System Components -->
    <section class="bg-light py-5 mb-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Core System Components</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="system-feature">
                        <h4><i class="fas fa-users-cog benefit-icon"></i>Landlord Portal</h4>
                        <p>Complete dashboard for property owners to manage their portfolio, view financials, and handle tenant communications.</p>
                    </div>
                    <div class="system-feature">
                        <h4><i class="fas fa-user-tie benefit-icon"></i>Tenant Portal</h4>
                        <p>Dedicated space for tenants to pay rent, submit maintenance requests, and communicate with landlords.</p>
                    </div>
                    <div class="system-feature">
                        <h4><i class="fas fa-file-contract benefit-icon"></i>Lease Management</h4>
                        <p>Digital lease creation, signing, storage, and renewal tracking with automated reminders.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="system-feature">
                        <h4><i class="fas fa-money-bill-wave benefit-icon"></i>Payment Processing</h4>
                        <p>Secure online payment system with automatic tracking and reconciliation.</p>
                    </div>
                    <div class="system-feature">
                        <h4><i class="fas fa-tools benefit-icon"></i>Maintenance Center</h4>
                        <p>Streamlined workflow for submitting, tracking, and resolving property maintenance issues.</p>
                    </div>
                    <div class="system-feature">
                        <h4><i class="fas fa-chart-line benefit-icon"></i>Reporting Suite</h4>
                        <p>Comprehensive financial and operational reports for better decision making.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Benefits -->
    <section class="container mb-5">
        <h2 class="text-center fw-bold mb-5">Key System Benefits</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-clock me-2"></i>Time Savings</h4>
                        <p class="card-text">Automate repetitive tasks like rent collection, reminders, and document generation to save hours each week.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-eye me-2"></i>Visibility</h4>
                        <p class="card-text">Real-time dashboard gives complete visibility into property performance, vacancies, and financials.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-lock me-2"></i>Security</h4>
                        <p class="card-text">Bank-level encryption protects sensitive tenant and financial data with role-based access controls.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-mobile-alt me-2"></i>Accessibility</h4>
                        <p class="card-text">Cloud-based system accessible 24/7 from any device with responsive design for mobile use.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-handshake me-2"></i>Tenant Relations</h4>
                        <p class="card-text">Improve tenant satisfaction with transparent communication and quick issue resolution.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 user-card">
                    <div class="card-body">
                        <h4 class="card-title text-primary"><i class="fas fa-chart-pie me-2"></i>Data-Driven</h4>
                        <p class="card-text">Make informed decisions with comprehensive analytics and historical performance data.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Users -->
    <section class="bg-primary text-white py-5 mb-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Who Uses This System?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-tie fa-3x mb-3"></i>
                            <h4>Individual Landlords</h4>
                            <p>Owners of single or multiple rental properties looking for professional management tools.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-building fa-3x mb-3"></i>
                            <h4>Property Management Companies</h4>
                            <p>Professional firms managing portfolios for multiple property owners.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h4>Real Estate Investors</h4>
                            <p>Investors with rental portfolios who need centralized management.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Requirements -->
    <section class="container mb-5">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">System Requirements</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">For Users</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Modern web browser (Chrome, Firefox, Safari, Edge)</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Internet connection</li>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i>Email account for notifications</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">System Architecture</h2>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Technical Foundation</h5>
                        <ul class="list-group list-group-flush">
                            <!-- <li class="list-group-item"><i class="fas fa-server text-info me-2"></i>Cloud-based SaaS platform</li> -->
                            <li class="list-group-item"><i class="fas fa-database text-info me-2"></i>Secure relational database</li>
                            <li class="list-group-item"><i class="fas fa-shield-alt text-info me-2"></i>Enterprise-grade security</li>
                            <li class="list-group-item"><i class="fas fa-sync-alt text-info me-2"></i>Automated daily backups</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('submitLandlordRequest').addEventListener('click', () => {
            document.getElementById('landlordForm').submit()
        })
    </script>
</body>

</html>