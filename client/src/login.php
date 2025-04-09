<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentMaster - Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('./image2.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(5px);
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
        #login {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }
        .login-container {
            background: rgba(204, 204, 204, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 5px 5px 15px rgba(0,0,0,0.3);
            width: 400px;
        }
        .login-box {
            background: #df31a5;
            padding: 20px;
            border-radius: 10px;
        }
        .login-box h2 {
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: bold;
        }
        .login-box label {
            color: rgb(255, 255, 255);
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .login-box input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: none;
            border-radius: 5px;
            background: #fff;
            border: 1px solid red;
            color: black;
        }
        .login-btn {
            background: rgb(97, 52, 221);
            color: rgb(255, 255, 255);
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            display: block;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="background-blur"></div>
    <header>
        <div class="logo-container">
            <img src="./logo.png" alt="RentMaster Logo">
            <div class="logo-text">RentMaster</div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
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
                <h2>Please Fill Login Form</h2>
                <form>
                    <label for="fullname">Enter Full Name</label>
                    <input type="text" id="fullname" placeholder="Enter Full Name">
                    
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Enter your email">
                    
                    <label for="phone">Phone No.</label>
                    <input type="tel" id="phone" placeholder="Enter Phone Number">
                    
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Enter your password">
                    
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" placeholder="Confirm your password">
                    
                    <label for="address">Enter The Detail Address</label>
                    <input type="text" id="address" placeholder="Enter your address">
                    
                    <button type="submit" class="login-btn">Login</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
