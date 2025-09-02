<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register Page</h1>
    
    <form method="post" action="<?= base_url('register') ?>">
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        
        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>
        
        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirm" required>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p><a href="<?= base_url('login') ?>">Login</a></p>
    <p><a href="<?= base_url('dashboard') ?>">Dashboard</a></p>
</body>
</html>