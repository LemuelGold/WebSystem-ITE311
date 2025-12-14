<?php
// Simple web-based database fixer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_db'])) {
    try {
        $conn = new mysqli('localhost', 'root', '', 'lms_restaurodb');
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $results = [];
        
        // Check and add period column
        $result = $conn->query("SHOW COLUMNS FROM materials LIKE 'period'");
        if ($result->num_rows == 0) {
            if ($conn->query("ALTER TABLE materials ADD COLUMN period ENUM('Prelim', 'Midterm', 'Final') NULL AFTER file_path")) {
                $results[] = "✅ Added 'period' column successfully";
            } else {
                $results[] = "❌ Error adding 'period' column: " . $conn->error;
            }
        } else {
            $results[] = "✅ 'period' column already exists";
        }
        
        // Check and add material_title column
        $result = $conn->query("SHOW COLUMNS FROM materials LIKE 'material_title'");
        if ($result->num_rows == 0) {
            if ($conn->query("ALTER TABLE materials ADD COLUMN material_title VARCHAR(100) NULL AFTER period")) {
                $results[] = "✅ Added 'material_title' column successfully";
            } else {
                $results[] = "❌ Error adding 'material_title' column: " . $conn->error;
            }
        } else {
            $results[] = "✅ 'material_title' column already exists";
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        $results[] = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Materials Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .result { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #28a745; }
        .warning { background: #fff3cd; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #ffc107; }
        h1 { color: #333; text-align: center; }
        .status { font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Materials Database</h1>
        
        <div class="warning">
            <strong>⚠️ What this will do:</strong><br>
            • Add 'period' column to materials table<br>
            • Add 'material_title' column to materials table<br>
            • Enable proper period-based material organization
        </div>
        
        <?php if (isset($results)): ?>
            <div class="result">
                <h3>🔄 Database Update Results:</h3>
                <?php foreach ($results as $result): ?>
                    <div class="status"><?= htmlspecialchars($result) ?></div>
                <?php endforeach; ?>
                
                <hr>
                <strong>✅ Database update completed!</strong><br>
                <small>You can now go back to your materials page and test the period system.</small>
            </div>
        <?php else: ?>
            <form method="POST">
                <p>Click the button below to add the missing database columns for the materials period system:</p>
                <button type="submit" name="fix_db" class="btn">🚀 Fix Database Now</button>
            </form>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="../teacher/course/12/upload" style="color: #007bff;">← Back to Materials</a>
        </div>
    </div>
</body>
</html>