# Kiosk App - Promotional Campaign

A full-featured kiosk application for promotional campaigns with camera capture and photo storage.

## Features

- **16:9 Aspect Ratio Display** - Optimized for kiosk screens
- **Three-Step Flow:**
  1. Welcome screen with continue button
  2. Camera view with photo capture
  3. Success screen with auto-restart
- **Photo Storage** - Saves photos to database with metadata
- **Session Tracking** - Tracks each kiosk session
- **Auto-Reset** - Returns to welcome screen after 5 seconds

## Setup Instructions

### 1. Database Migration
The photos table has been created with the following fields:
- `id` - Unique identifier
- `filename` - Original filename
- `path` - Storage path
- `session_id` - Kiosk session identifier
- `metadata` - JSON metadata (capture time, IP, user agent)
- `created_at` / `updated_at` - Timestamps

### 2. Storage Configuration
The public storage link has been created to serve uploaded photos.

### 3. Running the Application

Start the Laravel development server:
```bash
php artisan serve
```

Then open your browser to: `http://localhost:8000`

For production, configure your web server to point to the `public` directory.

## How to Use

### Kiosk Mode
1. Open the kiosk URL in fullscreen mode (press F11 in most browsers)
2. The app will display the welcome screen
3. Click "Continue" to start the camera
4. Click the circular button to capture a photo
5. Review the photo and click "Save Photo" or "Retake"
6. Success screen appears and auto-resets after 5 seconds

### Admin View
Access captured photos at: `http://localhost:8000/admin/photos`

## API Endpoints

### Store Photo
```
POST /api/photos
Content-Type: application/json

{
    "photo": "data:image/png;base64,...",
    "session_id": "optional_session_id"
}
```

### List Photos
```
GET /api/photos
```

### Get Single Photo
```
GET /api/photos/{id}
```

## Browser Compatibility

- Chrome/Edge (Recommended)
- Firefox
- Safari

**Note:** Camera access requires HTTPS in production or localhost for testing.

## File Structure

```
app/
├── Http/Controllers/PhotoController.php  # Handles photo operations
├── Models/Photo.php                      # Photo model
database/
├── migrations/
│   └── *_create_photos_table.php        # Database schema
resources/
├── views/
│   ├── kiosk.blade.php                  # Main kiosk interface
│   └── admin/photos.blade.php           # Admin photo gallery
routes/
└── web.php                               # Application routes
storage/
└── app/public/photos/                    # Photo storage location
```

## Customization

### Change Logo
Replace the logo image at `public/01/01_Logo.png`

### Adjust Colors
Edit the gradient in `kiosk.blade.php`:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Modify Countdown Time
Change the countdown duration in the `startCountdown()` function:
```javascript
let seconds = 5; // Change this value
```

### Screen Aspect Ratio
The kiosk maintains a 16:9 aspect ratio automatically. To change:
```css
aspect-ratio: 16/9; /* Modify this value */
```

## Security Considerations

1. **CSRF Protection** - All POST requests include CSRF token
2. **Image Validation** - Server validates base64 image data
3. **Storage Permissions** - Photos stored in secure location
4. **Session Tracking** - Each session is uniquely identified

## Troubleshooting

### Camera Not Working
- Ensure browser has camera permissions
- Use HTTPS in production
- Check if camera is being used by another application

### Photos Not Saving
- Verify storage permissions: `chmod -R 775 storage`
- Check database connection
- Review Laravel logs in `storage/logs`

### Display Issues
- Clear browser cache
- Ensure modern browser version
- Check console for JavaScript errors

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Configure web server (Nginx/Apache)
3. Use HTTPS for camera access
4. Set up proper file permissions
5. Configure backup for `storage/app/public/photos`

## License

This kiosk application is part of the Redmi promotional campaign.

