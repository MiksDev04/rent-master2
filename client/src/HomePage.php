<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentMaster - Home</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        html{
            scroll-behavior: smooth;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            width: 100vw;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('image.jpg') no-repeat center center fixed;
            background-size: cover;
            z-index: -1;
        }
        header {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 10%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }
        .logo-container img {
            width: 70px; 
            filter: brightness(1.2) contrast(1.2);
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
        }
        .nav-links, nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin-left: 30px;
            padding: 0;
        }
        .nav-links li, nav ul li {
            list-style: none;
        }
        .nav-links li a, .auth a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
        }
        section {
            padding: 80px 10%;
            margin-bottom: 50px;
            color: #fff;
        }
        .properties, .about, .contact {
            margin-bottom: 50px;
        }
        .section-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .section-content {
            font-size: 18px;
        }
        .property-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .property-card {
            background: rgba(204, 204, 204, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 30%;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
        }
        .property-card img {
            width: 100%;
            border-radius: 10px;
        }
        .welcome-container {
            margin-left: 10%;
            margin-top: 100px;
            top: 20%;
            left: 5%;
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            color: #fff;
            width: 80%;
            max-width: 400px;
            z-index: 1;
        }
        .welcome-container h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        .welcome-container .rent-now-btn {
            background: #df31a5;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            margin-top: 20px;
        }
        .service-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .service-container {
            background: rgba(204, 204, 204, 0.8);
            padding: 30px;
            border-radius: 10px;
            width: 30%;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
        }
        .service-container p {
            font-size: 18px;
            color: #333;
        }

        .about-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            width: 30%; 
            max-width: 400px; 
            margin: 0 auto; 
            text-align: center;
            margin-top: 50px;
        }
        .about-container h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        .about-container p {
            font-size: 18px;
        }

        /* Adjusted margin for the next sections */
        .properties {
            margin-top: 100px; /* Move the properties section down */
        }
        .about {
            margin-top: 50px; /* Add a slight margin to separate About Us */
        }
        .service-section, .contact {
            margin-top: 80px; /* Move services and contact slightly down */
        }

        /* Testimonial Section */
        .testimonial-section {
            background: rgba(0, 0, 0, 0.8);
            padding: 40px 10%;
            margin-top: 50px;
            margin-bottom: 80px;
            color: #fff;
            text-align: center;
        }
        .testimonial-container {
            background: rgba(204, 204, 204, 0.8);
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto;
            font-size: 18px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
        }
        .testimonial-container p {
            font-size: 18px;
            color: #333;
        }
        .testimonial-container .testimonial-author {
            font-weight: bold;
            font-size: 20px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <header>
        <div class="logo-container">
            <img src="logo.png" alt="RentMaster Logo">
            <div class="logo-text">RentMaster</div>
            <ul class="nav-links">
                <li><a href="#home.html">Home</a></li>
                <li><a href="#properties">Property</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
        <nav>
            <ul>
                <li class="auth"><a href="register.html">Register</a></li>
                <li class="auth"><a href="login.html">Login</a></li>
            </ul>
        </nav>
    </header>

    <div class="welcome-container" id="home">
        <h1>Welcome to RentMaster!!!</h1>
        <button class="rent-now-btn">Rent Now</button>
    </div>

    <!-- Properties Section -->
    <section id="properties" class="properties">
        <div class="section-title">Featured Properties</div>
        <div class="section-content">Browse some of our amazing rental properties below.</div>
        <div class="property-list">
            <div class="property-card">
                <img src="image4.png" alt="Property 1">
                <div class="property-title">Cozy Apartment</div>
                <p>2 Beds | 1 Bath | $1,200/month</p>
            </div>
            <div class="property-card">
                <img src="image5.png" alt="Property 2">
                <div class="property-title">Luxury Condo</div>
                <p>3 Beds | 2 Baths | $2,500/month</p>
            </div>
            <div class="property-card">
                <img src="image6.png" alt="Property 3">
                <div class="property-title">Modern Studio</div>
                <p>1 Bed | 1 Bath | $950/month</p>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="about">
        <div class="about-container">
            <h2>About Us</h2>
            <p>RentMaster is your go-to platform for renting apartments, condos, and homes. We offer a wide variety of listings in various locations to suit your needs. Whether you're looking for a cozy apartment or a luxury condo, we have the perfect rental for you!</p>
        </div>
    </section>

    <!-- Services Section -->
    <section id="service" class="service">
        <div class="section-title">Our Services</div>
        <div class="service-section">
            <div class="service-container">
                <h3>Property Management</h3>
                <p>Streamline property listings, tenant records, and lease agreements in one organized platform.</p>
            </div>
            <div class="service-container">
                <h3>Payment Tracking</h3>
                <p>Track payments in real time, send automated reminders, and reduce late payments.</p>
            </div>
            <div class="service-container">
                <h3>Maintenance Management</h3>
                <p>Allow tenants to submit issues easily while landlords manage and resolve maintenance tasks efficiently.</p>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial-section">
        <div class="testimonial-container">
            <p>"Finding the perfect apartment has never been easier! The process was smooth, and the team was incredibly helpful in guiding me every step of the way. My new home is exactly what I was looking for—comfortable, affordable, and in a great location. I highly recommend their service to anyone looking for a hassle-free rental experience!"</p>
            <div class="testimonial-author">- Happy Tenant Mga Bossing!!!</div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="contact">
        <div class="section-title">Contact Us</div>
        <div class="section-content">
            Have questions? Feel free to reach out to us at <strong>support@rentmaster.com</strong> or call us at <strong>(123) 456-7890</strong>.
        </div>
    </section>
</body>
</html>
