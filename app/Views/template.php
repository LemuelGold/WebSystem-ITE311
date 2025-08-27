<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Simple Navbar</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .navbar {
      background-color: #333;
      overflow: hidden;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      padding: 14px 20px;
      display: inline-block;
    }

    .navbar a:hover {
      background-color: #575757;
    }

    .nav-links {
      display: flex;
    }

    .logo {
      font-weight: bold;
      font-size: 20px;
      color: white;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="logo">MySite</div>
    <div class="nav-links">
      <a href="#">Home</a>
      <a href="#">About</a>
      <a href="#">Services</a>
      <a href="#">Contact</a>
    </div>
  </div>

</body>
</html>
