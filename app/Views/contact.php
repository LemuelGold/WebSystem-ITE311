<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - ITE311 Project</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        nav { background: #f4f4f4; padding: 10px; margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: #333; }
        nav a:hover { color: #007bff; }
        h1 { color: #333; }
    </style>
</head>
<body>
   <nav>
        <a href="<?= base_url('/') ?>">Home</a>
        <a href="<?= base_url('/about') ?>">About</a>
        <a href="<?= base_url('/contact') ?>">Contact</a>
        <a href="<?= base_url('/login') ?>">Login</a>
    </nav>
    
    <h1>Contact Us</h1>
    <p>This is the contact page of my CodeIgniter project.</p>
    <p>You can reach us through this page for any inquiries.</p>
</body>
</html>