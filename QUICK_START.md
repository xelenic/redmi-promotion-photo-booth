# 🚀 Quick Start Guide - Kiosk App

## Ready to Use!

Your kiosk app is now fully configured and ready to run!

## Start the App (3 Simple Steps)

### 1. Start Laravel Server
```bash
php artisan serve
```

### 2. Open Kiosk Interface
Open your browser and go to:
```
http://localhost:8000
```

### 3. Enter Fullscreen Mode (Optional but Recommended)
Press **F11** on your keyboard for the best kiosk experience

---

## What You Get

### ✅ Main Kiosk (`http://localhost:8000`)
- **Welcome Screen** - Attractive entry point with continue button
- **Camera View** - Live camera feed with capture button
- **Photo Preview** - Review and retake option before saving
- **Success Screen** - Confirmation with auto-reset after 5 seconds
- **16:9 Ratio** - Optimized for kiosk displays

### ✅ Admin Panel (`http://localhost:8000/admin/photos`)
- **Photo Gallery** - View all captured photos
- **Statistics** - Total, today, and weekly photo counts
- **Filters** - Filter by date
- **Full View** - Click any photo to view full size
- **Auto-Refresh** - Updates every 30 seconds

### ✅ Backend Features
- **Database Storage** - All photos saved to SQLite database
- **File Management** - Photos organized by date (YYYY/MM/DD)
- **Session Tracking** - Each kiosk session uniquely identified
- **Metadata** - Capture time, IP address, and user agent stored
- **RESTful API** - Complete API for photo operations

---

## Database Schema

The `photos` table includes:
- `id` - Auto-increment primary key
- `filename` - Generated unique filename
- `path` - Storage path (photos/YYYY/MM/DD/filename.png)
- `session_id` - Kiosk session identifier
- `metadata` - JSON data (captured_at, ip_address, user_agent)
- `created_at` / `updated_at` - Laravel timestamps

---

## File Locations

### Captured Photos
```
storage/app/public/photos/YYYY/MM/DD/
```

Photos are accessible via:
```
http://localhost:8000/storage/photos/YYYY/MM/DD/filename.png
```

### Views
- Kiosk: `resources/views/kiosk.blade.php`
- Admin: `resources/views/admin/photos.blade.php`

### Backend
- Controller: `app/Http/Controllers/PhotoController.php`
- Model: `app/Models/Photo.php`
- Routes: `routes/web.php`
- Migration: `database/migrations/*_create_photos_table.php`

---

## API Usage Examples

### Capture and Save Photo (from JavaScript)
```javascript
const response = await fetch('/api/photos', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        photo: 'data:image/png;base64,...',
        session_id: 'optional_session_id'
    })
});
```

### Get All Photos
```bash
curl http://localhost:8000/api/photos
```

### Get Single Photo
```bash
curl http://localhost:8000/api/photos/1
```

---

## Customization Tips

### 1. Change Brand Colors
Edit `resources/views/kiosk.blade.php` line ~45:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### 2. Update Logo
Replace: `public/01/01_Logo.png` with your logo

### 3. Adjust Auto-Reset Time
Edit `resources/views/kiosk.blade.php` in the `startCountdown()` function:
```javascript
let seconds = 5; // Change to desired seconds
```

### 4. Modify Screen Ratio
Edit `resources/views/kiosk.blade.php` line ~23:
```css
aspect-ratio: 16/9; /* Change to your preferred ratio */
```

---

## Keyboard Shortcuts (Kiosk Mode)

- **F11** - Toggle fullscreen
- **Esc** - Exit fullscreen
- **F5** - Refresh page
- **Ctrl/Cmd + Shift + I** - Open developer tools (for debugging)

---

## Browser Support

✅ **Recommended:** Chrome, Edge, Brave  
✅ **Supported:** Firefox, Safari  
⚠️ **Note:** Camera requires HTTPS in production (localhost is OK for testing)

---

## Production Checklist

Before deploying to production:

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Configure proper database (MySQL/PostgreSQL)
- [ ] Set up HTTPS (required for camera access)
- [ ] Configure web server (Nginx/Apache)
- [ ] Set proper file permissions
- [ ] Enable Laravel caching
- [ ] Set up backup system for photos
- [ ] Configure auto-restart after crashes
- [ ] Test camera on production environment
- [ ] Set up monitoring/logging

---

## Troubleshooting

### Camera Permission Denied
1. Check browser permissions
2. Ensure using HTTPS (or localhost)
3. Verify no other app is using camera

### Photos Not Showing in Admin
1. Check storage link: `php artisan storage:link`
2. Verify file permissions: `chmod -R 775 storage`
3. Check database connection

### Server Won't Start
1. Check if port 8000 is already in use
2. Try different port: `php artisan serve --port=8080`
3. Check PHP version (requires PHP 8.1+)

---

## Support

For detailed documentation, see: `KIOSK_README.md`

For Laravel documentation: https://laravel.com/docs

---

**Your kiosk app is ready! Start capturing photos! 📸**

