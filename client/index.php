<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentMaster - Login</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="background-blur"></div>
    <header>
        <div class="logo-container">
            <img src="./assets/images/logo.png" alt="RentMaster Logo">
            <div class="logo-text">RentMaster</div>
            <ul class="nav-links">
                <li><a href="HomePage.html">Home</a></li>
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

    <section id="login">
        <div class="login-container">
            <div class="login-box">
                <h2>Login Here</h2>
                <form>
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Enter your email">
                    
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Enter your password">
                    
                    <button type="submit" class="login-btn">Login</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>