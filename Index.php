<?php
session_start();
if(isset($_SESSION['owner_id'])) {
    header("Location: Owner_Dashboard.php");
    exit();
}

if(isset($_SESSION['officer_id'])) {
    header("Location: Officer_Dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehicle Document</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background: url('assets/backgroundimage.avif') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background: rgba(143, 186, 238, 0.966);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 60px;
      max-width: 600px;
      width: 100%;
      text-align: center;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #003366;
    }

    .login-box h3 {
      text-align: center;
      margin-bottom: 30px;
      color: #d1172a;
      font-size: 22px;
    }

    .links {
      text-align: center;
    }

    .links a {
      display: inline-block;
      font-size: 1em;
      text-decoration: none;
      color: #003366;
      padding: 10px 20px;
      margin: 10px 0;
      border: 2px solid #003366;
      border-radius: 8px;
      background-color: white;
      transition: all 0.3s ease;
    }

    .links a:hover {
      background-color: #003366;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="login-box">
      <h2>VEHICLE DOCUMENT</h2>
      <h3>LOGIN</h3>

      <div class="links">
        <a href="owner/Owner_Login_Page.html">Vehicle Owner Login</a><br><br>
        <a href="officer/Officer_Login_Page.html">Police Officer Login</a>
      </div>
    </div>
  </div>

  <script src="assets/js/script.js"></script>
</body>
</html>
