<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Promotional Campaign Kiosk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            color: white;
            text-shadow: 0 0 30px rgba(255,255,255,0.8),
                         0 0 60px rgba(255,255,255,0.6),
                         0 0 90px rgba(255,255,255,0.4);
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
            color: #4ade80;
            text-shadow: 0 0 30px rgba(74, 222, 128, 0.8),
                         0 0 60px rgba(74, 222, 128, 0.6);
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
        }

        #success-screen::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
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
            animation: rotateBackground 20s linear infinite;
            opacity: 0.3;
            z-index: 0;
        }

        #success-screen::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, transparent 0%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }

        #success-screen > * {
            position: relative;
            z-index: 2;
        }

        @keyframes rotateBackground {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Fireworks Container */
        .fireworks-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
            overflow: hidden;
        }

        .firework {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            animation: fireworkExplode 1.5s ease-out forwards;
        }

        @keyframes fireworkExplode {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }

        .firework-trail {
            position: absolute;
            bottom: 0;
            width: 3px;
            height: 100px;
            background: linear-gradient(to top, transparent, var(--color));
            animation: rocketLaunch 0.8s ease-out forwards;
        }

        @keyframes rocketLaunch {
            0% {
                transform: translateY(0);
                opacity: 1;
            }
            100% {
                transform: translateY(-200px);
                opacity: 0;
            }
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
            position: relative;
            width: 60vh;
            max-width: 80%;
            aspect-ratio: 9/16;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .photo-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            z-index: 3;
            pointer-events: none;
        }

        .captured-photo-display {
            position: absolute;
            width: 75%;
            height: 85%;
            top: 7.5%;
            left: 12.5%;
            object-fit: cover;
            z-index: 2;
            border-radius: 15px;
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
    </style>
</head>
<body>
    <div id="kiosk-container">
        <!-- Step 1: Welcome Screen -->
        <div id="welcome-screen">
            <img src="/01/01_Logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
            <button class="btn btn-primary" onclick="startCamera()">
                <img src="/01/01_Button.png" alt="Continue" style="max-width: 35vh;margin-top: 54vh;">
            </button>
        </div>

        <!-- Step 2: Camera Screen -->
        <div id="camera-screen" class="hidden">
            <div class="camera-wrapper">
                <video id="video" autoplay playsinline></video>
                <div class="camera-overlay"></div>
                <div class="camera-logo">
                    <img src="/02/02_Logo.png" alt="Logo" style="max-width: 49vh;margin-top: 2vh;width: 29vh;height: auto;">
                </div>
                <canvas id="canvas"></canvas>

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
            <div class="fireworks-container" id="fireworks-container"></div>
            <div class="photo-frame-container">
                <img id="captured-photo-display" src="" alt="Captured Photo" class="captured-photo-display">
                <img src="/03/Frame_4K_1.png" alt="Photo Frame" class="photo-frame">
            </div>
            <h1 id="success-message">Saving Photo...</h1>
            <div class="saving-indicator" id="saving-indicator">
                <div class="spinner"></div>
                <p>Please wait while we save your photo...</p>
            </div>
            <div class="countdown hidden" id="countdown-container">Restarting in <span id="countdown">5</span> seconds...</div>
        </div>
    </div>

    <script>
        let stream = null;
        let capturedImage = null;
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const previewImage = document.getElementById('preview-image');
        const previewContainer = document.getElementById('preview-container');

        // CSRF Token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function startCamera() {
            try {
                // Hide welcome, show camera
                document.getElementById('welcome-screen').classList.add('hidden');
                document.getElementById('camera-screen').classList.remove('hidden');

                // Request camera access
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1080 },
                        height: { ideal: 1920 }
                    }
                });

                video.srcObject = stream;
            } catch (error) {
                console.error('Error accessing camera:', error);
                showCameraError();
            }
        }

        function startCountdown() {
            const overlay = document.getElementById('countdown-overlay');
            const countdownNumber = document.getElementById('countdown-number');
            const captureButton = document.getElementById('capture-button');

            // Disable button during countdown
            captureButton.disabled = true;
            captureButton.style.opacity = '0.5';

            // Show overlay
            overlay.classList.add('active');

            let count = 3;
            countdownNumber.textContent = count;

            const countdownInterval = setInterval(() => {
                count--;

                if (count > 0) {
                    // Update number and restart animation
                    countdownNumber.style.animation = 'none';
                    setTimeout(() => {
                        countdownNumber.textContent = count;
                        countdownNumber.style.animation = 'countdownPulse 1s ease-in-out';
                    }, 10);
                } else {
                    clearInterval(countdownInterval);

                    // Show "Smile!" or camera flash effect
                    countdownNumber.textContent = '📸';
                    countdownNumber.style.animation = 'none';
                    setTimeout(() => {
                        countdownNumber.style.animation = 'countdownPulse 0.5s ease-in-out';
                    }, 10);

                    // Capture after brief delay
                    setTimeout(() => {
                        overlay.classList.remove('active');
                        capturePhoto();

                        // Re-enable button
                        captureButton.disabled = false;
                        captureButton.style.opacity = '1';

                        // Reset countdown number
                        countdownNumber.textContent = '3';
                    }, 500);
                }
            }, 1000);
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
                    // Show "Smile!" or camera emoji
                    countdownDisplay.textContent = '📸 Smile!';
                    countdownDisplay.className = 'countdown-smile';
                } else {
                    // Take the photo
                    clearInterval(countdownInterval);
                    countdownOverlay.classList.remove('active');
                    captureBtn.disabled = false;
                    capturePhoto();
                }
            }, 1000);
        }

        function capturePhoto() {
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

            // Convert to base64
            capturedImage = canvas.toDataURL('image/png');

            // Small delay for flash effect to complete
            setTimeout(() => {
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
                savePhoto();
            }, 300);
        }

        async function savePhoto() {
            if (!capturedImage) {
                console.error('No photo captured');
                document.getElementById('success-message').textContent = 'Error: No photo captured!';
                document.getElementById('saving-indicator').classList.add('hidden');
                startCountdown();
                return;
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

                const data = await response.json();

                if (data.success) {
                    // Update success message
                    document.getElementById('success-message').textContent = 'Photo Saved Successfully!';
                    document.getElementById('saving-indicator').classList.add('hidden');
                    document.getElementById('countdown-container').classList.remove('hidden');

                    // Launch fireworks celebration
                    launchFireworks();

                    // Start countdown to restart
                    startCountdown();
                } else {
                    throw new Error('Failed to save photo');
                }
            } catch (error) {
                console.error('Error saving photo:', error);
                document.getElementById('success-message').textContent = 'Error Saving Photo';
                document.getElementById('saving-indicator').innerHTML = '<p style="color: #ff6b6b;">Failed to save. Photo will still reset.</p>';

                // Still start countdown even on error
                setTimeout(() => {
                    document.getElementById('saving-indicator').classList.add('hidden');
                    document.getElementById('countdown-container').classList.remove('hidden');
                    startCountdown();
                }, 2000);
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function startCountdown() {
            let seconds = 5;
            const countdownElement = document.getElementById('countdown');

            const interval = setInterval(() => {
                seconds--;
                countdownElement.textContent = seconds;

                if (seconds <= 0) {
                    clearInterval(interval);
                    resetKiosk();
                }
            }, 1000);
        }

        function resetKiosk() {
            // Reset all states
            capturedImage = null;
            previewContainer.classList.remove('active');

            // Clear captured photo display
            document.getElementById('captured-photo-display').src = '';

            // Clear fireworks
            document.getElementById('fireworks-container').innerHTML = '';

            // Reset success screen messages
            document.getElementById('success-message').textContent = 'Saving Photo...';
            document.getElementById('saving-indicator').classList.remove('hidden');
            document.getElementById('countdown-container').classList.add('hidden');

            // Show welcome screen
            document.getElementById('success-screen').classList.add('hidden');
            document.getElementById('welcome-screen').classList.remove('hidden');

            // Reset countdown
            document.getElementById('countdown').textContent = '5';
        }

        function showCameraError() {
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

        // Fireworks Animation
        function launchFireworks() {
            const container = document.getElementById('fireworks-container');
            const colors = ['#ff0000', '#ff7300', '#fffb00', '#48ff00', '#00ffd5', '#002bff', '#7a00ff', '#ff00c8'];

            // Clear any existing fireworks
            container.innerHTML = '';

            // Launch multiple fireworks
            for (let i = 0; i < 5; i++) {
                setTimeout(() => {
                    createFirework(container, colors);
                }, i * 300);
            }

            // Continue launching fireworks for 3 seconds
            let count = 0;
            const interval = setInterval(() => {
                createFirework(container, colors);
                count++;
                if (count >= 8) {
                    clearInterval(interval);
                }
            }, 500);
        }

        function createFirework(container, colors) {
            const color = colors[Math.floor(Math.random() * colors.length)];
            const startX = Math.random() * window.innerWidth;
            const startY = window.innerHeight;
            const explosionY = Math.random() * 200 + 100; // Explode in upper half

            // Create explosion point
            const explosionX = startX + (Math.random() - 0.5) * 200;

            // Create particles
            const particleCount = 30;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'firework';
                particle.style.left = explosionX + 'px';
                particle.style.top = explosionY + 'px';
                particle.style.background = color;
                particle.style.boxShadow = `0 0 10px ${color}`;

                // Random direction for explosion
                const angle = (Math.PI * 2 * i) / particleCount;
                const velocity = Math.random() * 100 + 100;
                const tx = Math.cos(angle) * velocity;
                const ty = Math.sin(angle) * velocity;

                particle.style.setProperty('--tx', tx + 'px');
                particle.style.setProperty('--ty', ty + 'px');

                container.appendChild(particle);

                // Remove after animation
                setTimeout(() => {
                    if (particle.parentNode === container) {
                        container.removeChild(particle);
                    }
                }, 1500);
            }
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

