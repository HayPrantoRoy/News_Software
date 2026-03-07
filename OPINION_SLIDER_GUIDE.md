# Opinion Slider Implementation Guide

## Overview
A fully responsive manual slider for opinion photocards with database integration.

## Features
- ✅ Manual navigation with left/right arrow buttons
- ✅ Touch/swipe support for mobile devices
- ✅ Keyboard navigation (arrow keys)
- ✅ Fully responsive design (desktop to mobile)
- ✅ Database-driven content
- ✅ Smooth animations and transitions

---

## Installation Steps

### 1. Create Database Table
Run the SQL script to create the opinions table:

```bash
mysql -u your_username -p your_database < opinions_table.sql
```

Or manually execute the SQL in phpMyAdmin or MySQL Workbench.

### 2. Create Image Directory
Create a folder for opinion images:

```
/xampp/htdocs/HindusNews/Admin/img/opinions/
```

### 3. Upload Photocard Images
- Upload your opinion photocard images to the `opinions` folder
- Recommended format: JPG or PNG
- Recommended dimensions: 400px width (height auto)

### 4. Update Database Records
Update the image paths in the database:

```sql
UPDATE opinions SET image = 'opinions/your-image-name.jpg' WHERE id = 1;
```

---

## Database Schema

```sql
Table: opinions
- id (INT, Primary Key, Auto Increment)
- image (VARCHAR 255) - Path to photocard image
- display_order (INT) - Order of display
- status (TINYINT) - 1=Active, 0=Inactive
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## How to Add New Opinion Cards

### Method 1: Direct SQL
```sql
INSERT INTO opinions (image, display_order, status) 
VALUES ('opinions/new-card.jpg', 7, 1);
```

### Method 2: phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Navigate to the `opinions` table
4. Click "Insert" tab
5. Fill in the fields:
   - image: `opinions/your-image.jpg`
   - display_order: Next number in sequence
   - status: 1 (Active)
6. Click "Go"

---

## Responsive Breakpoints

| Screen Size | Max Card Width | Arrow Size | Gap |
|------------|----------------|------------|-----|
| Desktop (>1024px) | 400px | 50px | 20px |
| Tablet (768-1024px) | 350px | 45px | 20px |
| Mobile (480-768px) | 280px | 40px | 15px |
| Small Mobile (360-480px) | 240px | 35px | 12px |
| Extra Small (<360px) | 200px | 35px | 12px |

---

## Customization

### Change Arrow Colors
Edit in `index.php` (CSS section):

```css
.slider-arrow {
    border: 2px solid #FF0000;  /* Change color here */
    color: #FF0000;              /* Change color here */
}

.slider-arrow:hover {
    background: #FF0000;         /* Change color here */
}
```

### Change Slide Speed
Edit in `index.php` (CSS section):

```css
.opinion-slider {
    transition: transform 0.4s ease-in-out; /* Change 0.4s to desired speed */
}
```

### Change Card Spacing
Edit in `index.php` (CSS section):

```css
.opinion-slider {
    gap: 20px; /* Change spacing between cards */
}
```

---

## Navigation Controls

### Mouse/Click
- Click left arrow (◀) to go to previous card
- Click right arrow (▶) to go to next card

### Touch/Swipe (Mobile)
- Swipe left to go to next card
- Swipe right to go to previous card

### Keyboard
- Press `←` (Left Arrow) to go to previous card
- Press `→` (Right Arrow) to go to next card

---

## Troubleshooting

### Images Not Showing
1. Check image path in database is correct
2. Verify images exist in `Admin/img/opinions/` folder
3. Check file permissions (should be readable)
4. Check `$uploadPath` variable in `index.php` is set to `'Admin/img/'`

### Slider Not Working
1. Check browser console for JavaScript errors
2. Ensure at least one opinion card exists in database with `status = 1`
3. Clear browser cache and refresh

### Layout Issues on Mobile
1. Check viewport meta tag is present: `<meta name="viewport" content="width=device-width, initial-scale=1">`
2. Clear mobile browser cache
3. Test in different browsers

### Cards Too Large/Small
1. Adjust `max-width` in CSS for `.opinion-card img`
2. Update responsive breakpoints as needed

---

## Performance Tips

1. **Optimize Images**: Compress images before uploading (recommended tools: TinyPNG, ImageOptim)
2. **Use Lazy Loading**: Already implemented with `loading="lazy"` attribute
3. **Limit Active Cards**: Keep only necessary cards active (status=1) in database
4. **Image Format**: Use WebP format for smaller file sizes (if browser support allows)

---

## File Locations

```
/xampp/htdocs/HindusNews/
├── index.php                  (Main file with slider code)
├── opinions_table.sql         (Database schema)
├── Admin/img/opinions/        (Image folder)
└── OPINION_SLIDER_GUIDE.md    (This file)
```

---

## Support

For issues or questions:
1. Check error logs in `/xampp/htdocs/HindusNews/error.log`
2. Check browser console for JavaScript errors
3. Verify database connection in `connection.php`

---

**Version**: 1.0  
**Last Updated**: January 2026  
**Compatible With**: PHP 7.4+, MySQL 5.7+
