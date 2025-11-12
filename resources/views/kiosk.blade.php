<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Promotional Campaign Kiosk</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js" integrity="sha384-lGUMmzEWVOliWfynhZoL3yYsKM9kbLj4yEJITWic9Y2Q2Dw/dCTeor7YNLlhlQgR" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #000;
            overflow: hidden;
            -webkit-user-select: none;
            user-select: none;
        }

        #kiosk-container {
            width: 100vw;
            height: 100vh;
            aspect-ratio: 16/9;
            max-height: 100vh;
            max-width: 177.78vh; /* 16:9 ratio */
            margin: 0 auto;
            position: relative;
            background: url('/01/00_BG.jpg') center center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Step 1: Welcome Screen */
        #welcome-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px;
            color: white;
            background: rgba(0, 0, 0, 0.3);
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
        }

        #welcome-screen.hidden,
        #camera-screen.hidden,
        #success-screen.hidden {
            display: none;
        }

        .logo {
            width: 39vh;
            height: auto;
            margin-bottom: 60px;
        }

        .btn {
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
            outline: none;
        }

        .btn:focus,
        .btn:active,
        .capture-btn:focus,
        .capture-btn:active,
        .home-icon:focus,
        .home-icon:active,
        button:focus,
        button:active {
            outline: none;
            box-shadow: none;
        }

        .btn-primary {
            background: transparent;
        }

        .btn-primary img {
            width: auto;
            height: auto;
            max-width: 600px;
            display: block;
            transition: all 0.3s ease;
        }

        .btn-primary:hover img {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .btn-primary:active img {
            transform: scale(0.98);
        }

        /* Step 2: Camera Screen */
        #camera-screen {
            width: 100%;
            height: 100%;
            position: relative;
            background: url('/02/00_BG.jpg') center center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .camera-wrapper {
            position: relative;
            height: 90%;
            max-height: 90vh;
            aspect-ratio: 9/16;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .camera-wrapper::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            background: linear-gradient(135deg, rgba(241, 113, 6, 0.8), rgba(241, 113, 6, 0.3));
            border-radius: 34px;
            z-index: -1;
        }

        .camera-wrapper::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            right: 2px;
            bottom: 2px;
            background: #000;
            border-radius: 30px;
            z-index: -1;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        #video {
            width: 100%;
            height: 62vh;
            object-fit: cover;
            display: block;
            margin-top: 245px;
            border-radius: 70px;
        }

        #canvas {
            display: none;
        }

        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, transparent 15%, transparent 85%, rgba(0,0,0,0.3) 100%);
        }

        .camera-logo {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            pointer-events: none;
        }

        .camera-logo img {
            height: 60px;
            width: auto;
            filter: drop-shadow(2px 2px 10px rgba(0,0,0,0.8));
        }

        .camera-preloader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99;
            color: white;
            text-align: center;
            gap: 20px;
            backdrop-filter: blur(10px);
        }

        .camera-preloader.hidden {
            display: none;
        }

        .camera-preloader .preloader-spinner {
            width: 70px;
            height: 70px;
            border: 6px solid rgba(255, 255, 255, 0.2);
            border-top-color: #ff7300;
            border-radius: 50%;
            animation: spin 1s linear infinite, glow 1.5s ease-in-out infinite alternate;
        }

        .camera-preloader p {
            font-size: 1.4rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        @keyframes glow {
            from {
                filter: drop-shadow(0 0 10px rgba(255, 115, 0, 0.6));
            }
            to {
                filter: drop-shadow(0 0 25px rgba(255, 255, 255, 0.9));
            }
        }

        .countdown-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        .countdown-overlay.active {
            display: flex;
        }

        .countdown-number {
            font-size: 15rem;
            font-weight: 900;
            color: #f17106;
            text-shadow: 0 0 35px rgba(241, 113, 6, 0.85),
                         0 0 70px rgba(241, 113, 6, 0.6),
                         0 0 110px rgba(241, 113, 6, 0.4);
            animation: countdownPulse 1s ease-in-out;
        }

        @keyframes countdownPulse {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0.9;
            }
        }

        .countdown-smile {
            font-size: 10rem;
            font-weight: 700;
            color: #f17106;
            text-shadow: 0 0 35px rgba(241, 113, 6, 0.85),
                         0 0 70px rgba(241, 113, 6, 0.6);
            animation: countdownSmile 0.8s ease-in-out;
        }

        @keyframes countdownSmile {
            0% {
                transform: scale(0.5) rotate(-10deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.1) rotate(5deg);
                opacity: 1;
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        /* Camera Flash Effect */
        .flash-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            opacity: 0;
            z-index: 40;
            pointer-events: none;
        }

        .flash-overlay.flash {
            animation: cameraFlash 0.5s ease-out;
        }

        @keyframes cameraFlash {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Camera Loading Overlay */
        .camera-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            backdrop-filter: blur(10px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 100;
            text-align: center;
            padding: 40px;
        }

        .camera-loading.active {
            display: flex;
        }

        .camera-loading .loading-ring {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 8px solid rgba(255, 255, 255, 0.1);
            border-top-color: #f17106;
            border-right-color: #f17106;
            border-bottom-color: rgba(241, 113, 6, 0.5);
            border-left-color: rgba(241, 113, 6, 0.3);
            animation: spinRing 1s linear infinite;
            margin-bottom: 30px;
            box-shadow: 0 0 30px rgba(241, 113, 6, 0.6),
                        0 0 60px rgba(241, 113, 6, 0.4);
            position: relative;
        }

        .camera-loading .loading-ring::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(241, 113, 6, 0.4), transparent);
            animation: pulse 1.5s ease-in-out infinite;
        }

        .camera-loading h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            letter-spacing: 1px;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        .camera-loading p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }

        @keyframes spinRing {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
                transform: translate(-50%, -50%) scale(1);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.2);
            }
        }

        .camera-controls {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            align-items: center;
            z-index: 20;
        }

        .capture-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .capture-btn img {
            width: auto;
            height: auto;
            max-width: 35vh;
            display: block;
            transition: all 0.3s ease;
        }

        .capture-btn:hover {
            transform: scale(1.05);
        }

        .capture-btn:hover img {
            filter: brightness(1.1);
        }

        .capture-btn:active {
            transform: scale(0.98);
        }

        .preview-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/02/00_BG.jpg') center center / cover no-repeat;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .preview-container.active {
            display: flex;
        }

        .preview-wrapper {
            position: relative;
            height: 90%;
            max-height: 90vh;
            aspect-ratio: 9/16;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .preview-wrapper::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            background: conic-gradient(
                from 0deg,
                #ff0000,
                #ff7300,
                #fffb00,
                #48ff00,
                #00ffd5,
                #002bff,
                #7a00ff,
                #ff00c8,
                #ff0000
            );
            border-radius: 34px;
            z-index: -1;
            animation: rotate 4s linear infinite;
            filter: blur(1px);
        }

        .preview-wrapper::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            right: 2px;
            bottom: 2px;
            background: #000;
            border-radius: 30px;
            z-index: -1;
        }

        #preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .preview-controls {
            position: absolute;
            bottom: -90px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            z-index: 20;
        }

        .btn-retake,
        .btn-save {
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-retake {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-save {
            background: #667eea;
            color: white;
        }

        .btn-retake:hover,
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* Success Screen */
        #success-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 60px;
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(8,8,8,0.92) 0%, rgba(0,0,0,0.98) 100%);
        }

        #success-screen h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        }

        #success-screen p {
            font-size: 1.5rem;
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .photo-frame-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .photo-frame {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            z-index: 3;
        }

        .captured-photo-display {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            z-index: 2;
        }

        .saving-indicator {
            margin-top: 20px;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            animation: scaleIn 0.5s ease;
        }

        .success-icon::before {
            content: '✓';
            font-size: 70px;
            color: white;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .countdown {
            font-size: 1.2rem;
            margin-top: 20px;
            opacity: 0.8;
        }

        /* Home Icon */
        .home-icon {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .home-icon:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
        }

        .home-icon:active {
            transform: scale(0.95);
        }

        .home-icon svg {
            width: 30px;
            height: 30px;
            fill: white;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        /* Loading spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .hidden {
            display: none !important;
        }

        /* Camera permission message */
        .camera-error {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            color: #333;
            max-width: 500px;
        }

        .camera-error h2 {
            color: #e74c3c;
            margin-bottom: 15px;
        }

        .qr-container {
            position: absolute;
            bottom: 0px;
            right: 0px;
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 18px 26px;
            /* background: rgba(0, 0, 0, 0.55); */
            /* border-radius: 20px; */
            /* border: 1px solid rgba(255, 255, 255, 0.08); */
            /* backdrop-filter: blur(8px); */
            z-index: 20;
        }

        .qr-container img {
            width: 220px;
            height: 220px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
            padding: 10px;
            background-color: white;
        }

        .qr-title {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.6px;
        }

        .qr-link {
            font-size: 0.95rem;
            color: #f17106;
            text-decoration: none;
            word-break: break-all;
            max-width: 320px;
        }

        .qr-link:hover {
            text-decoration: underline;
        }

        /* Preloader Screen */
        #preloader-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            color: white;
        }

        #preloader-screen.hidden {
            display: none;
        }

        .preloader-spinner {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 8px solid rgba(255, 255, 255, 0.1);
            border-top-color: #f17106;
            border-right-color: #f17106;
            border-bottom-color: rgba(241, 113, 6, 0.5);
            border-left-color: rgba(241, 113, 6, 0.3);
            animation: spinRing 1s linear infinite;
            margin-bottom: 30px;
            box-shadow: 0 0 30px rgba(241, 113, 6, 0.6),
                        0 0 60px rgba(241, 113, 6, 0.4);
        }

        .preloader-text {
            font-size: 1.5rem;
            font-weight: 300;
            opacity: 0.9;
            letter-spacing: 1px;
        }

        .preloader-progress {
            margin-top: 20px;
            font-size: 1rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Preloader Screen -->
    <div id="preloader-screen">
        <div class="preloader-spinner"></div>
        <div class="preloader-text">Loading...</div>
        <div class="preloader-progress" id="preloader-progress">0%</div>
    </div>

    <div id="kiosk-container" class="hidden">
        <!-- Step 1: Welcome Screen -->
        <div id="welcome-screen">
            <img src="/01/01_Logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
            <button class="btn btn-primary" onclick="startCamera()">
                <img src="/01/01_Button.png" alt="Continue" style="max-width: 35vh;margin-top: 54vh;">
            </button>
        </div>

        <!-- Step 2: Camera Screen -->
        <div id="camera-screen" class="hidden">
            <div class="camera-loading" id="camera-loading">
                <div class="loading-ring"></div>
                <h2>Initializing Camera</h2>
                <p>Give us a second while we prepare the view…</p>
            </div>
            <div class="camera-wrapper">
                <video id="video" autoplay playsinline></video>
                <div class="camera-overlay"></div>
                <div class="camera-logo">
                    <img src="/02/02_Logo.png" alt="Logo" style="max-width: 49vh;margin-top: 2vh;width: 29vh;height: auto;">
                </div>
                <canvas id="canvas"></canvas>

                <!-- Camera Preloader -->
                <div class="camera-preloader hidden" id="camera-preloader">
                    <div class="preloader-spinner"></div>
                    <p>Starting camera...</p>
                </div>

                <!-- Countdown Overlay -->
                <div class="countdown-overlay" id="countdown-overlay">
                    <div class="countdown-number" id="countdown-display">3</div>
                </div>

                <!-- Flash Overlay -->
                <div class="flash-overlay" id="flash-overlay"></div>

                <div class="camera-controls">
                    <button class="capture-btn" id="capture-button" onclick="startCountdownCapture()" title="Take Photo">
                        <img src="/02/02_Button.png" alt="Capture" style="max-width: 35vh;">
                    </button>
                </div>
            </div>

            <!-- Preview Container -->
            <div class="preview-container" id="preview-container">
                <div class="preview-wrapper">
                    <img id="preview-image" alt="Preview">
                    <div class="preview-controls">
                        <button class="btn-retake" onclick="retakePhoto()">Retake</button>
                        <button class="btn-save" onclick="savePhoto()">Save Photo</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Success Screen -->
        <div id="success-screen" class="hidden">
            <!-- Home Icon -->
            <div class="home-icon" onclick="manualRestart()" title="Back to Home">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </div>

            <div class="photo-frame-container">
                <img id="captured-photo-display" src="" alt="Captured Photo" class="captured-photo-display">
                <img src="/03/Frame_4K_1.png" alt="Photo Frame" class="photo-frame">
            </div>
            <h1 id="success-message">Saving Photo...</h1>
            <div class="saving-indicator" id="saving-indicator">
                <div class="spinner"></div>
                <p>Please wait while we save your photo...</p>
            </div>
            <div class="qr-container hidden" id="qr-container">
                <img id="qr-code" alt="Download QR Code">
                <a id="qr-link" class="qr-link" target="_blank" rel="noopener"></a>
            </div>
            <div class="countdown hidden" id="countdown-container">Restarting in <span id="countdown">60</span> seconds...</div>
        </div>
    </div>

    <script>
        let stream = null;
        let capturedImage = null;
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const previewImage = document.getElementById('preview-image');
        const previewContainer = document.getElementById('preview-container');
        const cameraPreloader = document.getElementById('camera-preloader');
        const cameraLoading = document.getElementById('camera-loading');
        const qrContainer = document.getElementById('qr-container');
        const qrImage = document.getElementById('qr-code');
        const qrLink = document.getElementById('qr-link');
        const preloaderScreen = document.getElementById('preloader-screen');
        const preloaderProgress = document.getElementById('preloader-progress');
        const kioskContainer = document.getElementById('kiosk-container');

        function loadImage(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        }

        // Preload all images
        async function preloadAssets() {
            const images = [
                '/01/00_BG.jpg',
                '/01/01_Logo.png',
                '/01/01_Button.png',
                '/02/00_BG.jpg',
                '/02/02_Logo.png',
                '/02/02_Button.png',
                '/03/Frame_4K_1.png'
            ];

            let loaded = 0;
            const total = images.length;

            const updateProgress = () => {
                if (total > 0 && preloaderProgress) {
                    const progress = Math.round((loaded / total) * 100);
                    preloaderProgress.textContent = `${Math.min(progress, 100)}%`;
                }
            };

            if (total === 0) {
                if (preloaderScreen) {
                    preloaderScreen.classList.add('hidden');
                }
                if (kioskContainer) {
                    kioskContainer.classList.remove('hidden');
                }
                return;
            }

            const loadPromises = images.map((src) => {
                return loadImage(src)
                    .catch((error) => {
                        console.warn(`Failed to load image: ${src}`, error);
                    })
                    .finally(() => {
                        loaded++;
                        updateProgress();
                    });
            });

            await Promise.all(loadPromises);

            // Hide preloader and show kiosk
            if (preloaderScreen) {
                preloaderScreen.classList.add('hidden');
            }
            if (kioskContainer) {
                kioskContainer.classList.remove('hidden');
            }
        }

        // Start preloading when page loads
        window.addEventListener('load', () => {
            preloadAssets();
        });

        // CSRF Token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function startCamera() {
            try {
                // Hide welcome, show camera
                document.getElementById('welcome-screen').classList.add('hidden');
                document.getElementById('camera-screen').classList.remove('hidden');

                // Show preloader immediately
                showCameraLoading('Initializing Camera...');

                // Small delay to ensure preloader is visible
                await new Promise(resolve => setTimeout(resolve, 100));

                // Request camera access
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1080 },
                        height: { ideal: 1920 }
                    }
                });

                video.srcObject = stream;

                // Wait for video to be ready
                const handleLoaded = () => {
                    hideCameraLoading();
                    video.removeEventListener('loadeddata', handleLoaded);
                    video.removeEventListener('canplay', handleLoaded);
                    video.removeEventListener('playing', handleLoaded);
                };

                video.addEventListener('loadeddata', handleLoaded);
                video.addEventListener('canplay', handleLoaded);
                video.addEventListener('playing', handleLoaded);

                // Fallback: hide after video starts playing
                video.onplaying = () => {
                    setTimeout(() => {
                        hideCameraLoading();
                    }, 300);
                };
            } catch (error) {
                console.error('Error accessing camera:', error);
                hideCameraLoading();
                showCameraError();
            }
        }

        function startCountdownCapture() {
            // Disable button during countdown
            const captureBtn = document.getElementById('capture-button');
            captureBtn.disabled = true;

            // Show countdown overlay
            const countdownOverlay = document.getElementById('countdown-overlay');
            const countdownDisplay = document.getElementById('countdown-display');
            countdownOverlay.classList.add('active');

            let count = 3;
            countdownDisplay.textContent = count;
            countdownDisplay.className = 'countdown-number';

            const countdownInterval = setInterval(() => {
                count--;

                if (count > 0) {
                    // Update number with animation
                    countdownDisplay.textContent = count;
                    // Re-trigger animation by removing and adding class
                    countdownDisplay.style.animation = 'none';
                    setTimeout(() => {
                        countdownDisplay.style.animation = '';
                    }, 10);
                } else if (count === 0) {
                    // Show "Smile!"
                    countdownDisplay.textContent = 'Smile!';
                    countdownDisplay.className = 'countdown-smile';
                } else {
                    // Take the photo
                    clearInterval(countdownInterval);
                    countdownOverlay.classList.remove('active');
                    countdownDisplay.className = 'countdown-number';
                    countdownDisplay.style.animation = '';
                    countdownDisplay.textContent = '3';
                    captureBtn.disabled = false;
                    capturePhoto().catch(error => console.error('capturePhoto failed', error));
                }
            }, 1000);
        }

        async function capturePhoto() {
            // Flash effect
            const flashOverlay = document.getElementById('flash-overlay');
            flashOverlay.classList.add('flash');
            setTimeout(() => {
                flashOverlay.classList.remove('flash');
            }, 500);

            // Set canvas dimensions to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw the current video frame to canvas
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Overlay frame image
            try {
                const frameImage = await loadImage('/03/Frame_4K_1.png');
                ctx.drawImage(frameImage, 0, 0, canvas.width, canvas.height);
            } catch (frameError) {
                console.error('Failed to load frame image:', frameError);
            }

            // Convert to base64
            capturedImage = canvas.toDataURL('image/png');

            // Small delay for flash effect to complete
            setTimeout(async () => {
                // Stop camera
                stopCamera();

                // Display photo in frame on success screen
                document.getElementById('captured-photo-display').src = capturedImage;

                // Hide camera screen, show success screen
                document.getElementById('camera-screen').classList.add('hidden');
                document.getElementById('success-screen').classList.remove('hidden');

                // Update message
                document.getElementById('success-message').textContent = 'Saving Photo...';
                document.getElementById('saving-indicator').classList.remove('hidden');
                document.getElementById('countdown-container').classList.add('hidden');

                // Automatically save photo
                try {
                    await savePhoto();
                } catch (saveError) {
                    console.error('Failed to save photo:', saveError);
                }
            }, 300);
        }

        async function savePhoto() {
            if (!capturedImage) {
                console.error('No photo captured');
                const successMessageEl = document.getElementById('success-message');
                if (successMessageEl) {
                    successMessageEl.textContent = 'Error: No photo captured!';
                }
                const savingIndicatorEl = document.getElementById('saving-indicator');
                if (savingIndicatorEl) {
                    savingIndicatorEl.classList.add('hidden');
                }
                // Auto-redirect disabled - user must click home button to restart
                return;
            }

            if (qrContainer) {
                qrContainer.classList.add('hidden');
            }
            if (qrImage) {
                qrImage.src = '';
            }
            if (qrLink) {
                qrLink.textContent = '';
                qrLink.removeAttribute('href');
            }

            try {
                // Send photo to server
                const response = await fetch('/api/photos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        photo: capturedImage,
                        session_id: generateSessionId()
                    })
                });

                if (!response.ok) {
                    console.warn('Photo save request failed', response);
                }

                const data = await response.json();

                if (data.success) {
                    console.log('Photo saved response', data);
                    // Update success message
                    document.getElementById('success-message').textContent = 'Photo Saved Successfully!';
                    document.getElementById('saving-indicator').classList.add('hidden');
                    const countdownContainerEl = document.getElementById('countdown-container');
                    if (countdownContainerEl) {
                        countdownContainerEl.classList.add('hidden');
                    }

                    // Generate QR code download link
                    if (qrContainer && qrImage && qrLink && data.photo_url) {
                        const downloadUrl = data.photo_url.startsWith('http')
                            ? data.photo_url
                            : `${window.location.origin}${data.photo_url}`;
                        console.log('Generated QR download URL:', downloadUrl);

                        qrLink.textContent = downloadUrl;
                        qrLink.href = downloadUrl;

                        if (window.QRCode && typeof QRCode.toDataURL === 'function') {
                            try {
                                const qrDataUrl = await QRCode.toDataURL(downloadUrl, {
                                    width: 320,
                                    margin: 1,
                                });
                                qrImage.src = qrDataUrl;
                                qrContainer.classList.remove('hidden');
                            } catch (qrError) {
                                console.error('Failed to generate QR code:', qrError);
                                qrContainer.classList.add('hidden');
                            }
                        } else {
                            console.warn('QRCode library not available');
                            qrContainer.classList.add('hidden');
                        }
                    } else {
                        console.warn('QR data missing', {
                            qrContainer,
                            qrImage,
                            qrLink,
                            photo_url: data.photo_url,
                            data,
                        });
                    }

                    // Countdown auto-redirect is disabled - user must click home button to restart
                } else {
                    throw new Error('Failed to save photo');
                }
            } catch (error) {
                console.error('Error saving photo:', error);
                document.getElementById('success-message').textContent = 'Error Saving Photo';
                document.getElementById('saving-indicator').innerHTML = '<p style="color: #ff6b6b;">Failed to save. Photo will still be available.</p>';

                setTimeout(() => {
                    document.getElementById('saving-indicator').classList.add('hidden');
                    if (qrContainer) {
                        qrContainer.classList.add('hidden');
                    }
                }, 2000);
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            hideCameraLoading();
        }

        let resetCountdownInterval = null;

        function startCountdown() {
            let seconds = 60;
            const countdownElement = document.getElementById('countdown');
            countdownElement.textContent = seconds;

            if (resetCountdownInterval) {
                clearInterval(resetCountdownInterval);
            }

            resetCountdownInterval = setInterval(() => {
                seconds--;
                countdownElement.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(resetCountdownInterval);
                    resetCountdownInterval = null;
                    resetKiosk();
                }
            }, 1000);
        }

        function manualRestart() {
            // Clear countdown if running
            if (resetCountdownInterval) {
                clearInterval(resetCountdownInterval);
                resetCountdownInterval = null;
            }
            resetKiosk();
        }

        function resetKiosk() {
            // Reset all states
            capturedImage = null;
            if (previewContainer) {
                previewContainer.classList.remove('active');
            }

            // Clear captured photo display
            const capturedDisplay = document.getElementById('captured-photo-display');
            if (capturedDisplay) {
                capturedDisplay.src = '';
            }

            // Clear fireworks

            hideCameraLoading();

            // Reset success screen messages
            const successMessageEl = document.getElementById('success-message');
            if (successMessageEl) {
                successMessageEl.textContent = 'Saving Photo...';
            }
            const savingIndicatorEl = document.getElementById('saving-indicator');
            if (savingIndicatorEl) {
                savingIndicatorEl.classList.remove('hidden');
            }
            const countdownContainerEl = document.getElementById('countdown-container');
            if (countdownContainerEl) {
                countdownContainerEl.classList.add('hidden');
            }
            if (qrContainer) {
                qrContainer.classList.add('hidden');
            }
            if (qrImage) {
                qrImage.src = '';
            }
            if (qrLink) {
                qrLink.textContent = '';
                qrLink.removeAttribute('href');
            }

            // Show welcome screen
            const successScreenEl = document.getElementById('success-screen');
            if (successScreenEl) {
                successScreenEl.classList.add('hidden');
            }
            const welcomeScreenEl = document.getElementById('welcome-screen');
            if (welcomeScreenEl) {
                welcomeScreenEl.classList.remove('hidden');
            }

            // Reset countdown
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                countdownEl.textContent = '60';
            }
        }

        function showCameraError() {
            hideCameraLoading();
            const cameraScreen = document.getElementById('camera-screen');
            cameraScreen.innerHTML = `
                <div class="camera-error">
                    <h2>Camera Access Required</h2>
                    <p>Please allow camera access to continue with the photo capture.</p>
                    <button class="btn btn-primary" onclick="location.reload()" style="margin-top: 20px;">Try Again</button>
                </div>
            `;
        }

        function generateSessionId() {
            return 'kiosk_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function showCameraLoading(message = 'Loading camera…') {
            if (!cameraLoading) return;
            cameraLoading.classList.add('active');
            const title = cameraLoading.querySelector('h2');
            const description = cameraLoading.querySelector('p');
            if (title) title.textContent = message;
            if (description) description.textContent = 'Give us a second while we prepare the view…';
        }

        function hideCameraLoading() {
            if (!cameraLoading) return;
            cameraLoading.classList.remove('active');
        }

        // Prevent accidental navigation
        window.addEventListener('beforeunload', function (e) {
            if (stream) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Clean up on page unload
        window.addEventListener('unload', stopCamera);
    </script>
</body>
</html>

