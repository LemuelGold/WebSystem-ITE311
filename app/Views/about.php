<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - ITE311 Project</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        nav { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        
        nav a { 
            text-decoration: none; 
            color: #333;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        nav a:hover { 
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        nav a[href*="about"] {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
        }
        
        .content-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        h1 { 
            color: #333;
            font-size: 2.5em;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
        }
        
        .about-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .about-text {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 25px;
            text-align: justify;
        }
        
        .highlight-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-left: 4px solid #667eea;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .highlight-box h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        
        .feature-card {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2em;
            margin-bottom: 15px;
            color: #667eea;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::before {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            bottom: 10%;
            right: 10%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 15px;
            }
            
            .content-section {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2em;
            }

        }
    </style>
</head>
<body>
    <div class="floating-elements"></div>
    
    <div class="container">
        <nav>
            <a href="<?= base_url('/') ?>">Home</a>
            <a href="<?= base_url('/about') ?>">About</a>
            <a href="<?= base_url('/contact') ?>">Contact</a>
            <a href="<?= base_url('/login') ?>">Login</a>
        </nav>
        
        <div class="content-section">
            <h1>About Us</h1>
            
            <div class="about-content">
                <p class="about-text">Welcome to my ITE311 CodeIgniter project!</p>
                
                <p class="about-text">DUDE.</p>
                
                <div class="highlight-box">
                    <h3>Project Purpose</h3>
                    <p>HAHA.</p>
                </div>
                

            </div>
        </div>
    </div>
</body>
</html>