<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Font Awesome -->
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
    <!-- Landlord/Admin Profile Section - Styled to Match Existing Design -->
    <section class="bg-light py-5 mb-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">System Administrator Profile</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php
                    // Database connection (make sure this is secure in production)
                    require_once '../database/config.php'; // Adjust path as necessary

                    // Query for landlord/admin (user_role = 'landlord')
                    $query = "SELECT * FROM users WHERE user_role = 'landlord' LIMIT 1";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        $landlord = $result->fetch_assoc();
                    ?>
                        <div class="card user-card"> <!-- Added user-card class for hover effect -->
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center mb-4 mb-md-0">
                                        <img src="<?php echo htmlspecialchars($landlord['user_image']); ?>"
                                            class="img-fluid rounded-circle shadow"
                                            alt="<?php echo htmlspecialchars($landlord['user_name']); ?>"
                                            style="width: 200px; height: 200px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="system-feature"> <!-- Using system-feature class for consistent styling -->
                                            <h3 class="text-primary">
                                                <i class="fas fa-user-tie benefit-icon"></i>
                                                <?php echo htmlspecialchars($landlord['user_name']); ?>
                                            </h3>
                                            <p class="text-muted mb-4">Property Manager & System Administrator</p>

                                            <div class=" mb-3">
                                                <h4><i class="fas fa-info-circle benefit-icon"></i>About</h4>
                                                <p><?php echo htmlspecialchars($landlord['user_description']); ?></p>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div>
                                                        <h4><i class="fas fa-envelope benefit-icon"></i>Contact</h4>
                                                        <p>
                                                            <a href="#"><?php echo htmlspecialchars($landlord['user_email']); ?></a>
                                                            <br>
                                                            <?php echo htmlspecialchars($landlord['user_phone_number']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div>
                                                        <h4><i class="fas fa-map-marker-alt benefit-icon"></i>Location</h4>
                                                        <p><?php echo htmlspecialchars($landlord['user_address']); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <a href="?page=src/home#contact" class="btn btn-primary me-2">
                                                    <i class="fas fa-envelope me-1"></i> Contact Admin
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } else {
                        echo '<div class="alert alert-warning text-center">No administrator profile available at this time.</div>';
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </section>
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
</body>

</html>