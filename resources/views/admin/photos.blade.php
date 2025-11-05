<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .controls {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-group label {
            color: #666;
            font-size: 0.9rem;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .photo-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .photo-image {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            background: #f0f0f0;
        }

        .photo-info {
            padding: 15px;
        }

        .photo-filename {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.85rem;
            word-break: break-all;
        }

        .photo-meta {
            font-size: 0.8rem;
            color: #666;
            line-height: 1.6;
        }

        .photo-meta div {
            margin-bottom: 4px;
        }

        .session-id {
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
            font-family: monospace;
            font-size: 0.75rem;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #666;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f0f0f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .empty-state h2 {
            color: #999;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #bbb;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination button {
            padding: 10px 15px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pagination button:hover:not(:disabled) {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .current-page {
            padding: 10px 15px;
            color: #666;
        }

        /* Modal for full image view */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 40px;
            color: white;
            cursor: pointer;
            background: none;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📸 Photo Gallery</h1>
            <p style="color: #666; margin-top: 10px;">Promotional Campaign - Kiosk Photos</p>
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-label">Total Photos</span>
                    <span class="stat-value" id="total-photos">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Today</span>
                    <span class="stat-value" id="today-photos">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">This Week</span>
                    <span class="stat-value" id="week-photos">0</span>
                </div>
            </div>
        </header>

        <div class="controls">
            <div class="filter-group">
                <label>Date:</label>
                <input type="date" id="date-filter">
                <button class="btn btn-secondary" onclick="applyFilters()">Filter</button>
                <button class="btn btn-secondary" onclick="clearFilters()">Clear</button>
            </div>
            <div>
                <button class="btn btn-primary" onclick="loadPhotos()">🔄 Refresh</button>
                <a href="/" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">← Back to Kiosk</a>
            </div>
        </div>

        <div id="photo-grid" class="photo-grid">
            <div class="loading">
                <div class="spinner"></div>
                <p>Loading photos...</p>
            </div>
        </div>

        <div class="pagination" id="pagination"></div>
    </div>

    <!-- Modal for full image -->
    <div class="modal" id="image-modal" onclick="closeModal()">
        <button class="modal-close" onclick="closeModal()">×</button>
        <img class="modal-content" id="modal-image">
    </div>

    <script>
        let currentPage = 1;
        let allPhotos = [];

        async function loadPhotos() {
            try {
                const response = await fetch('/api/photos');
                const data = await response.json();
                
                allPhotos = data.data || [];
                updateStats();
                renderPhotos(allPhotos);
            } catch (error) {
                console.error('Error loading photos:', error);
                document.getElementById('photo-grid').innerHTML = `
                    <div class="empty-state">
                        <h2>Error Loading Photos</h2>
                        <p>Please try again later</p>
                    </div>
                `;
            }
        }

        function updateStats() {
            const today = new Date().toDateString();
            const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
            
            document.getElementById('total-photos').textContent = allPhotos.length;
            
            const todayPhotos = allPhotos.filter(p => 
                new Date(p.created_at).toDateString() === today
            ).length;
            document.getElementById('today-photos').textContent = todayPhotos;
            
            const weekPhotos = allPhotos.filter(p => 
                new Date(p.created_at) >= weekAgo
            ).length;
            document.getElementById('week-photos').textContent = weekPhotos;
        }

        function renderPhotos(photos) {
            const grid = document.getElementById('photo-grid');
            
            if (!photos || photos.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <h2>No Photos Yet</h2>
                        <p>Photos captured from the kiosk will appear here</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = photos.map(photo => `
                <div class="photo-card">
                    <img 
                        src="/storage/${photo.path}" 
                        alt="${photo.filename}"
                        class="photo-image"
                        onclick="openModal('/storage/${photo.path}')"
                        style="cursor: pointer;"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22225%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22400%22 height=%22225%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 fill=%22%23999%22 font-family=%22Arial%22%3EImage not found%3C/text%3E%3C/svg%3E'"
                    >
                    <div class="photo-info">
                        <div class="photo-filename">${photo.filename}</div>
                        <div class="photo-meta">
                            <div>📅 ${formatDate(photo.created_at)}</div>
                            <div>🕐 ${formatTime(photo.created_at)}</div>
                            ${photo.session_id ? `<div class="session-id">${photo.session_id}</div>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit'
            });
        }

        function applyFilters() {
            const dateFilter = document.getElementById('date-filter').value;
            
            if (!dateFilter) {
                renderPhotos(allPhotos);
                return;
            }

            const filtered = allPhotos.filter(photo => {
                const photoDate = new Date(photo.created_at).toISOString().split('T')[0];
                return photoDate === dateFilter;
            });

            renderPhotos(filtered);
        }

        function clearFilters() {
            document.getElementById('date-filter').value = '';
            renderPhotos(allPhotos);
        }

        function openModal(imageSrc) {
            document.getElementById('modal-image').src = imageSrc;
            document.getElementById('image-modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('image-modal').classList.remove('active');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Load photos on page load
        window.addEventListener('load', loadPhotos);

        // Auto-refresh every 30 seconds
        setInterval(loadPhotos, 30000);
    </script>
</body>
</html>

