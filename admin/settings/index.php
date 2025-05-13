<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <title>Settings Theme Mode</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .theme-toggle .bi {
      font-size: 1.5rem;
      cursor: pointer;
    }
    .theme-toggle .bi.active {
      color: orange;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <header class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-medium">Settings</h4>
    <div class="form-check form-switch d-flex align-items-center gap-2 theme-toggle">
      <i class="bi bi-sun" id="sunIcon"></i>
      <input class="form-check-input" type="checkbox" role="switch" id="themeToggle">
      <i class="bi bi-moon" id="moonIcon"></i>
    </div>
  </header>

  <hr>

  <div class="container">
    <h4>Account Information</h4>
    <form class="d-flex">
      <div class="row row-cols-1 row-cols-md-2 g-3">
        <div class="col">
          <label class="form-label" for="name">Owner Name</label>
          <input type="text" class="form-control" id="name" value="Juan Dela Cruz" />
        </div>
        <div class="col">
          <label class="form-label" for="email">Email</label>
          <input type="email" class="form-control" id="email" value="juandelacruz@gmail.com" />
        </div>
        <div class="col">
          <label class="form-label" for="phone-number">Phone Number</label>
          <input type="text" class="form-control" id="phone-number" value="0945-476-9876" />
        </div>
        <div class="col">
          <label class="form-label" for="password">Password</label>
          <input type="password" class="form-control" id="password" value="1234567890" />
        </div>
        <div class="col-12">
          <button class="btn btn-secondary fw-bold rounded-5 px-4">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- JavaScript to Handle Theme Toggle -->
<script>
  const themeToggle = document.getElementById('themeToggle');
  const html = document.documentElement;
  const sunIcon = document.getElementById('sunIcon');
  const moonIcon = document.getElementById('moonIcon');

  function applyTheme(theme) {
    html.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    sunIcon.classList.toggle('active', theme === 'light');
    moonIcon.classList.toggle('active', theme === 'dark');
    themeToggle.checked = theme === 'dark';
  }

  // Load saved theme or default to light
  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  themeToggle.addEventListener('change', () => {
    const newTheme = themeToggle.checked ? 'dark' : 'light';
    applyTheme(newTheme);
  });
</script>

</body>
</html>
