<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login Page</h1>
    
    <form method="post" action="/ITE311-FUNDAR/public/login">
        <div>
            <label>Username:</label>
            <input type="text" name="login" required>
        </div>
        
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <p><a href="<?= base_url('register') ?>">Register</a></p>
    <p><a href="<?= base_url('dashboard') ?>">Dashboard</a></p>
</body>
</html>