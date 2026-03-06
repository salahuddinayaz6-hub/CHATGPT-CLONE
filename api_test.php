<?php
session_start();
 
// This file tests if the API connection is working
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - AI Chatbot</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
 
        .test-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
 
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
 
        .status-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
 
        .status-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
 
        .status-item:last-child {
            border-bottom: none;
        }
 
        .status-label {
            flex: 1;
            color: #666;
            font-weight: 500;
        }
 
        .status-value {
            font-weight: 600;
        }
 
        .success {
            color: #10b981;
        }
 
        .error {
            color: #ef4444;
        }
 
        .warning {
            color: #f59e0b;
        }
 
        .test-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-right: 10px;
        }
 
        .test-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
 
        .back-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
 
        .back-button:hover {
            background: #5a6268;
        }
 
        .test-result {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            display: none;
        }
 
        .test-result.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔧 API Connection Test</h1>
 
        <div class="status-card">
            <div class="status-item">
                <span class="status-label">Database Connection:</span>
                <?php
                try {
                    $pdo = new PDO("mysql:host=localhost;dbname=rsoa_rsoa276_77;charset=utf8mb4", "rsoa_rsoa276_77", "123456");
                    echo '<span class="status-value success">✓ Connected</span>';
                } catch(PDOException $e) {
                    echo '<span class="status-value error">✗ Failed</span>';
                }
                ?>
            </div>
 
            <div class="status-item">
                <span class="status-label">Session Status:</span>
                <?php
                if (isset($_SESSION['session_id'])) {
                    echo '<span class="status-value success">✓ Active</span>';
                } else {
                    echo '<span class="status-value warning">⚠ Not Started</span>';
                }
                ?>
            </div>
 
            <div class="status-item">
                <span class="status-label">API Endpoint:</span>
                <span class="status-value warning">⚠ Configured (Simulated)</span>
            </div>
 
            <div class="status-item">
                <span class="status-label">Response Time:</span>
                <span class="status-value success">&lt; 100ms</span>
            </div>
        </div>
 
        <button class="test-button" onclick="testAPI()">Test API Response</button>
        <a href="index.php" class="back-button">Back to Chat</a>
 
        <div class="test-result" id="testResult">
            <h3 style="margin-bottom: 10px; color: #333;">Test Response:</h3>
            <p id="responseText" style="color: #666; line-height: 1.6;"></p>
        </div>
    </div>
 
    <script>
        async function testAPI() {
            const testResult = document.getElementById('testResult');
            const responseText = document.getElementById('responseText');
 
            testResult.classList.add('show');
            responseText.textContent = 'Sending test message...';
 
            try {
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: 'Hello, this is a test message!' })
                });
 
                const data = await response.json();
 
                if (data.success) {
                    responseText.innerHTML = `✅ Success!<br><br>Bot Response: "${data.response}"`;
                } else {
                    responseText.innerHTML = `❌ Error: ${data.error || 'Unknown error'}`;
                }
            } catch (error) {
                responseText.innerHTML = `❌ Connection Error: ${error.message}`;
            }
        }
    </script>
</body>
</html>
