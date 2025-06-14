<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
  <style>
    .theme-icon {
      cursor: pointer;
      transition: color 0.3s;
    }

    .theme-icon.active {
      color: orange;
    }

    body {
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>
  <div class="container py-4  px-lg-5">
    <div class="settings-section">

      <header class="mb-4">
        <h4 class="fw-semibold">Settings</h4>
        <hr>
      </header>

      <div class="container">
        <!-- Theme Toggle -->
        <div class="mb-4">
          <label for="themeToggle" class="form-label d-block">Theme Mode</label>
          <div class="d-flex align-items-center justify-content-start gap-3">
            <!-- Sun SVG -->
            <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="theme-icon" viewBox="0 0 16 16">
              <path d="M8 4.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7z" />
              <path d="M8 0a.5.5 0 0 1 .5.5v1.1a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 8 0zm0 13.4a.5.5 0 0 1 .5.5v1.1a.5.5 0 0 1-1 0v-1.1a.5.5 0 0 1 .5-.5zM2.343 2.343a.5.5 0 0 1 .707 0l.778.778a.5.5 0 1 1-.707.707l-.778-.778a.5.5 0 0 1 0-.707zm10.607 10.607a.5.5 0 0 1 .707 0l.778.778a.5.5 0 1 1-.707.707l-.778-.778a.5.5 0 0 1 0-.707zM0 8a.5.5 0 0 1 .5-.5h1.1a.5.5 0 0 1 0 1H.5A.5.5 0 0 1 0 8zm13.4 0a.5.5 0 0 1 .5-.5h1.1a.5.5 0 0 1 0 1h-1.1a.5.5 0 0 1-.5-.5zM2.343 13.657a.5.5 0 0 1 0-.707l.778-.778a.5.5 0 1 1 .707.707l-.778.778a.5.5 0 0 1-.707 0zm10.607-10.607a.5.5 0 0 1 0-.707l.778-.778a.5.5 0 0 1 .707.707l-.778.778a.5.5 0 0 1-.707 0z" />
            </svg>

            <!-- Switch -->
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" id="themeToggle">
            </div>

            <!-- Moon SVG -->
            <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="theme-icon" viewBox="0 0 16 16">
              <path d="M6 0a6 6 0 0 0 0 12A6 6 0 0 0 6 0zm7.293 3.707A6 6 0 0 1 6 12a6 6 0 0 1 7.293-8.293z" />
            </svg>
          </div>
        </div>
        <!-- Font Size -->
        <div class="mb-4">
          <label for="fontSize" class="form-label">Font Size</label>
          <select id="fontSize" class="form-select">
            <option value="small">Small</option>
            <option value="medium" selected>Medium</option>
            <option value="large">Large</option>
          </select>
        </div>
        <!-- Font Family -->
        <div class="mb-4">
          <label for="fontFamily" class="form-label">Font Family</label>
          <select id="fontFamily" class="form-select">
            <option value="system-ui" selected>System UI</option>
            <option value="sans-serif" selected>Sans-serif</option>
            <option value="serif">Serif</option>
            <option value="monospace">Monospace</option>
          </select>
        </div>
      </div>
      <header class="mb-4">
        <h4 class="fw-semibold">Manual Guide</h4>
        <hr>
      </header>

      <div class="container ">
        <p>For more detailed instructions on how to use the admin panel, please refer to the user manual. <a href="?page=settings/user_manual">View Admin Guide</a></p>

      </div>

    </div>
  </div>

  <script defer>
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    const fontSizeSelect = document.getElementById('fontSize');
    const fontFamilySelect = document.getElementById('fontFamily');

    // Apply Theme
    function applyTheme(theme) {
      html.setAttribute('data-bs-theme', theme);
      localStorage.setItem('theme', theme);
      sunIcon.classList.toggle('active', theme === 'light');
      moonIcon.classList.toggle('active', theme === 'dark');
      themeToggle.checked = theme === 'dark';
    }

    // Apply Font Settings to <html>
    function applyFontSettings(size, family) {
      const fontSizes = {
        small: '14px',
        medium: '16px',
        large: '18px'
      };
      html.style.fontSize = fontSizes[size] || '16px';
      // html.style.fontFamily = family || 'sans-serif';
      document.documentElement.style.setProperty('--bs-body-font-family', family);
      localStorage.setItem('fontSize', size);
      localStorage.setItem('fontFamily', family);
    }

    // Load Saved Preferences
    const savedTheme = localStorage.getItem('theme') || 'light';
    const savedFontSize = localStorage.getItem('fontSize') || 'medium';
    const savedFontFamily = localStorage.getItem('fontFamily') || 'sans-serif';

    applyTheme(savedTheme);
    applyFontSettings(savedFontSize, savedFontFamily);

    // Set <select> inputs to saved values
    fontSizeSelect.value = savedFontSize;
    fontFamilySelect.value = savedFontFamily;

    // Event Listeners
    themeToggle.addEventListener('change', () => {
      applyTheme(themeToggle.checked ? 'dark' : 'light');
    });

    fontSizeSelect.addEventListener('change', () => {
      applyFontSettings(fontSizeSelect.value, fontFamilySelect.value);
    });

    fontFamilySelect.addEventListener('change', () => {
      applyFontSettings(fontSizeSelect.value, fontFamilySelect.value);
    });
  </script>

</body>

</html>