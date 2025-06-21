<!DOCTYPE html>
<html lang="en">

<head>
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>

    header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    h4 {
      margin: 0;
    }

    .section-title {
      font-size: 1.15rem;
      color: #333;
      margin-top: 1.5rem;
    }

    ul {
      padding-left: 1.2rem;
    }

    ul li {
      margin-bottom: 0.4rem;
    }

    a.btn {
      text-decoration: none;
      font-size: 0.875rem;
    }

    .accordion {
      margin-top: 1rem;
    }

    .accordion-item {
      margin-bottom: 0.5rem;
    }

    .accordion-title {
      padding: 0.75rem 1rem;
      cursor: pointer;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-radius: 4px;
    }

    .accordion-content {
      padding: 0.75rem 1.2rem;
      display: none;
      border: 1px solid #e9ecef;
      border-top: none;
      border-radius: 0 0 4px 4px;
    }

    .accordion-title i {
      transition: transform 0.2s;
    }

    .accordion-title.open i {
      transform: rotate(90deg);
    }

    @media (max-width: 600px) {
      body {
        padding: 1rem;
      }

      .container {
        padding: 1rem;
      }
    }
  </style>
</head>

<body>
  <div class="container px-lg-5 py-3">
    <header>
      <a href="?page=settings/index" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
      <h4>Landlord Usage Manual</h4>
    </header>
    <hr>

    <div class="accordion">
      <!-- Section Template -->
      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-users-cog me-2"></i>Managing Tenants</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Access the tenant management panel from the dashboard.</li>
            <li>Click "Add Tenant" to promote a registered visitor to tenant.</li>
            <li>Edit tenants to reassign properties or update info.</li>
            <li>Delete tenants after lease ends or on termination.</li>
            <li>View payment history from the tenant profile.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-building me-2"></i>Managing Properties</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Add, edit, or delete property listings via the dashboard.</li>
            <li>Include details: title, price, images, description, and amenities.</li>
            <li>Toggle property availability as needed.</li>
            <li>Review comments and ratings from users.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-file-signature me-2"></i>Handling Rental Requests</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Requests appear in the Reports tab.</li>
            <li>Approve or reject requests after review.</li>
            <li>Approved tenants are assigned and reflected in system.</li>
            <li>Lease agreement is generated for approved tenants.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-credit-card me-2"></i>Processing Payments</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Generate monthly rent based on property rate.</li>
            <li>Check payment status: Pending, Paid, Overdue.</li>
            <li>System alerts admin 5 days before due dates.</li>
            <li>View history in Admin Payment section.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-tools me-2"></i>Maintenance Requests</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Submitted by tenants from their dashboard.</li>
            <li>Visible in Admin > Maintenance tab.</li>
            <li>Update request status after resolution.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-bell me-2"></i>Notifications</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Get alerts on payments, rental requests, and issues.</li>
            <li>Visible in the notification center or top nav icon.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-cogs me-2"></i>Website & Account Settings</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Switch between dark and light modes.</li>
            <li>Customize fonts and layout options.</li>
            <li>Update profile photo, email, or password.</li>
            <li>Manage from "My Account" or profile page.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-title bg-body-tertiary"><span><i class="fas fa-search me-2"></i>Search Functionality</span><i class="fas fa-chevron-right"></i></div>
        <div class="accordion-content">
          <ul>
            <li>Use search bar in dashboard for tenants, properties, or amenities.</li>
            <li>Search filters include name, location, price, and status.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <script>
    const items = document.querySelectorAll(".accordion-title");
    items.forEach(item => {
      item.addEventListener("click", () => {
        item.classList.toggle("open");
        const content = item.nextElementSibling;
        if (content.style.display === "block") {
          content.style.display = "none";
        } else {
          content.style.display = "block";
        }
      });
    });
  </script>
</body>

</html>
