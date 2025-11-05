<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk App - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            background: white;
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 15px;
        }

        .subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 50px;
        }

        .options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .option-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.6);
        }

        .option-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .option-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .option-desc {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            text-align: left;
        }

        .info-box h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .info-box ul {
            list-style: none;
            padding-left: 0;
        }

        .info-box li {
            padding: 8px 0;
            color: #555;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-box li:last-child {
            border-bottom: none;
        }

        .info-box li::before {
            content: '✓ ';
            color: #4ade80;
            font-weight: bold;
            margin-right: 8px;
        }

        .status {
            display: inline-block;
            background: #4ade80;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 Kiosk App</h1>
        <p class="subtitle">Promotional Campaign Photo Capture System</p>

        <div class="options">
            <a href="/" class="option-card" onclick="window.location.href='/'; return false;">
                <div class="option-icon">🎯</div>
                <div class="option-title">Kiosk Mode</div>
                <div class="option-desc">Start the photo capture kiosk interface</div>
            </a>

            <a href="/admin/photos" class="option-card">
                <div class="option-icon">📊</div>
                <div class="option-title">Admin Gallery</div>
                <div class="option-desc">View all captured photos and statistics</div>
            </a>
        </div>

        <div class="info-box">
            <h3>Features Included</h3>
            <ul>
                <li>Live camera preview with photo capture</li>
                <li>16:9 aspect ratio optimized display</li>
                <li>Auto-reset after 5 seconds</li>
                <li>Photo storage with database tracking</li>
                <li>Admin gallery with statistics</li>
                <li>Session tracking and metadata</li>
            </ul>
        </div>

        <div class="status">✅ System Ready</div>
    </div>
</body>
</html>

