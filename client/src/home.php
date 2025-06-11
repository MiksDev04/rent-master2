<?php
// Database connection (update with your credentials)
require_once '../database/config.php';

// Fetch 3 latest available properties with image and amenities
$sql = "SELECT p.property_id, p.property_name, p.property_location, p.property_rental_price, 
            (SELECT image1 FROM property_images WHERE property_images.property_id = p.property_id LIMIT 1) as property_image,
            GROUP_CONCAT(a.amenity_name SEPARATOR ', ') as amenities
        FROM properties p
        LEFT JOIN property_amenities pa ON p.property_id = pa.property_id
        LEFT JOIN amenities a ON pa.amenity_id = a.amenity_id
        GROUP BY p.property_id
        ORDER BY p.property_date_created DESC 
        LIMIT 3";

$result = $conn->query($sql);

// Fetch testimonials with user info and property info
$sql2 =  "SELECT 
        tm.testimonial_id,
        tm.rating,
        tm.comment,
        tm.created_at,
        u.user_name,
        u.user_image,
        p.property_name
    FROM testimonials tm
    JOIN tenants t ON tm.tenant_id = t.tenant_id
    JOIN users u ON t.user_id = u.user_id
    JOIN properties p ON tm.property_id = p.property_id
    WHERE tm.rating >= 4
    ORDER BY tm.created_at DESC LIMIT 3 ";

$result2 = mysqli_query($conn, $sql2);

$testimonials = [];
if ($result2 && mysqli_num_rows($result2) > 0) {
    while ($row = mysqli_fetch_assoc($result2)) {
        $testimonials[] = $row;
    }
}

?>
<?php if (isset($_GET['message'])): ?>
    <div id="addSuccess" class="alert alert-success alert-dismissible fade show slide-in position-fixed top-0 start-50 translate-middle-x mt-3 shadow" role="alert" style="z-index: 9999; min-width: 300px;">
        <?= htmlspecialchars($_GET['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <p class="lead mb-3">Welcome to RentMaster</p>
        <h1>We Offer You The Best Properties In The World</h1>
        <p class="lead">Find your dream home from our carefully curated selection of premium properties worldwide.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="#properties" class="btn btn-primary">Browse Properties</a>
            <a href="#how-to-rent" class="btn btn-outline-light">How to Rent</a>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section id="properties" class="section">
    <div class="container c">
        <div class="text-center mb-5">
            <h2 class="section-title">Featured Properties</h2>
            <p class="section-subtitle">Discover our latest additions</p>
        </div>
        <?php if ($result->num_rows === 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-home text-muted fa-3x mb-3"></i>
                <h4 class="text-muted">No properties currently available</h4>
                <p class="text-muted">Please check back later</p>
            </div>
        <?php endif; ?>
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="property-card h-100">
                        <div class="overflow-hidden">
                            <img src="<?php echo $row['property_image']; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>" class="property-img w-100">
                        </div>
                        <div class="p-4">
                            <h5><?php echo htmlspecialchars($row['property_name']); ?></h5>
                            <p class="text-muted mb-3">
                                <svg class="svg-icon" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                <?php echo htmlspecialchars(mb_strimwidth($row['property_location'], 0, 30, '...')); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="property-price">PHP <?php echo number_format(htmlspecialchars($row['property_rental_price']), 2, '.', ',') ?></span>
                                <span class="text-muted">per month</span>
                            </div>
                            <a href="?page=src/properties-details&property_id=<?php echo htmlspecialchars($row['property_id']); ?>" class="btn btn-outline-primary w-100 mt-3">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>


        <div class="text-center mt-5">
            <a href="?page=src/properties" class="btn btn-primary px-4">View All Properties</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Choose RentMaster</h2>
            <p class="section-subtitle">We make renting simple and comfortable</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <svg class="feature-icon" viewBox="0 0 24 24" fill="#4a6bff">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                    <h4>Comfortable Living</h4>
                    <p class="text-muted">We ensure all our properties meet high standards of comfort and livability so you can feel at home from day one.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <svg class="feature-icon" viewBox="0 0 24 24" fill="#4a6bff">
                        <path d="M12 2L4 7v10l8 5 8-5V7L12 2zm-1 13.5l-4-2.3v-4.6l4 2.3v4.6zm.5-5.62L8.04 7.4 12 5.14l3.96 2.26-4.46 2.48zm1 5.62v-4.6l4-2.3v4.6l-4 2.3z" />
                    </svg>
                    <h4>Prompt Maintenance</h4>
                    <p class="text-muted">24/7 maintenance support to quickly resolve any issues in your home, ensuring your comfort is never compromised.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <svg class="feature-icon" viewBox="0 0 24 24" fill="#4a6bff">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                    </svg>
                    <h4>Secure Process</h4>
                    <p class="text-muted">Verified properties and secure rental agreements protect both tenants and landlords for a worry-free experience.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How to Rent Section -->
<section id="how-to-rent" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">How to Rent with Us</h2>
            <p class="section-subtitle">Simple steps to your new home</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4>Online Process</h4>
                    <ol class="mt-4 ps-3">
                        <li class="mb-3">Browse our properties collection</li>
                        <li class="mb-3">Select your ideal property</li>
                        <li class="mb-3">Click the rent button</li>
                        <li>Wait for landlord approval</li>
                    </ol>
                </div>
            </div>

            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4>Direct Contact</h4>
                    <ol class="mt-4 ps-3">
                        <li class="mb-3">Message the landlord/admin via email</li>
                        <li class="mb-3">Express your interest in a property</li>
                        <li>The admin will guide you through the rental process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="#contact" class="btn btn-primary px-4">Contact Us Now</a>
        </div>
    </div>
</section>
<!-- Testimonials Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">What Our Tenants Say</h2>
            <p class="section-subtitle">Hear from people who've made RentMaster their home</p>
        </div>

        <div class="row">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card card h-100 border-0 shadow-sm overflow-hidden">
                        <!-- Decorative ribbon for premium testimonials -->
                        <?php if (($testimonial['rating'] ?? 0) >= 4): ?>
                            <div class="position-absolute end-0 top-0 bg-primary text-white px-3 py-1 small" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 10px 50%);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                                    <path d="M12 1L15.09 7.26L22 8.27L17 13.14L18.18 20.02L12 16.77L5.82 20.02L7 13.14L2 8.27L8.91 7.26L12 1z" />
                                </svg>
                                Highly Suggested
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column align-items-center text-center p-4">
                            <!-- User avatar with subtle shadow -->
                            <div class="position-relative mb-3">
                                <img src="<?php echo htmlspecialchars($testimonial['user_image'] ?? 'path/to/default-image.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($testimonial['user_name'] ?? 'Anonymous'); ?>"
                                    class="rounded-circle shadow-sm"
                                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                                <!-- Verified badge for trusted users -->
                                <?php if (($testimonial['is_verified'] ?? false)): ?>
                                    <div class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1" style="width: 24px; height: 24px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white" class="d-block">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Star rating with animated hover effect -->
                            <div class="stars mb-3" style="letter-spacing: 2px;">
                                <?php
                                $rating = $testimonial['rating'] ?? 5;
                                for ($i = 1; $i <= 5; $i++):
                                ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                        fill="<?= $i <= $rating ? '#ffc107' : '#e9ecef'; ?>"
                                        class="star-icon"
                                        style="transition: transform 0.2s, fill 0.2s;">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                    </svg>
                                <?php endfor; ?>
                                <span class="ms-2 small text-muted"><?= $rating ?>.0</span>
                            </div>

                            <!-- Testimonial text with elegant quote marks -->
                            <div class="position-relative mb-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#e9ecef" class="position-absolute top-0 start-0" style="transform: translate(-5px, -5px);">
                                    <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z" />
                                </svg>
                                <p class="testimonial-text mb-0 px-3" style="font-style: italic; line-height: 1.6;">
                                    "<?php echo htmlspecialchars($testimonial['comment'] ?? 'No comment provided.'); ?>"
                                </p>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#e9ecef" class="position-absolute bottom-0 end-0" style="transform: translate(5px, 5px);">
                                    <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z" />
                                </svg>
                            </div>

                            <!-- User info with subtle divider -->
                            <div class="w-100 mt-auto pt-3 border-top">
                                <p class="testimonial-author mb-1 fw-semibold">
                                    <?php echo htmlspecialchars($testimonial['user_name'] ?? 'Anonymous'); ?>
                                </p>
                                <small class="d-block text-muted">
                                    <?php echo htmlspecialchars($testimonial['property_name'] ?? 'Unknown Property'); ?>
                                </small>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars(date('M d, Y ', strtotime($testimonial['created_at'] ?? 'now'))); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- Contact Section -->
<section id="contact" class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <h2 class="section-title">Contact Us</h2>
                <p class="section-subtitle">Have questions? Get in touch with our team.</p>

                <div class="contact-info-card">
                    <div class="d-flex align-items-start mb-4">
                        <svg class="contact-icon" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        <div>
                            <h5 class="mb-1">Email</h5>
                            <p class="mb-0 text-muted">rentmaster@gmail.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <svg class="contact-icon" viewBox="0 0 24 24">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                        <div>
                            <h5 class="mb-1">Phone</h5>
                            <p class="mb-0 text-muted">(123) 456-7890</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <svg class="contact-icon" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z" />
                        </svg>
                        <div>
                            <h5 class="mb-1">Facebook</h5>
                            <p class="mb-0 text-muted">Rent Master</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 offset-lg-1">
                <div class="contact-form p-4">
                    <h4 class="mb-4">Send us a message</h4>
                    <form method="POST" action="client/../includes/send_email.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Your message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="How can we help you?" required></textarea>
                        </div>
                        <input type="hidden" name="submit-normal-form" value="1">
                        <button type="submit" class="btn btn-primary w-100 py-2">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Worker Registration for Offline Functionality -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        });
    });
    // Add subtle animation to stars on hover
    document.querySelectorAll('.star-icon').forEach(star => {
        star.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.3)';
        });

        star.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>

<?php $conn->close(); ?>