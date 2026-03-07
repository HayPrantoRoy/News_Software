# Opinion Management System - Admin Guide

## 🎯 Overview
Complete admin panel for managing opinion photocard images on your website.

---

## 📁 Files Created/Modified

### New Files:
1. **`Admin/manage_opinions.php`** - Main opinion management page
2. **`Admin/img/opinions/`** - Directory for uploaded images (auto-created)

### Modified Files:
1. **`Admin/navigation.php`** - Added "Opinions" menu item

---

## 🔐 Access Control

- **Access Level:** Super Admin Only
- **Menu Location:** Admin Panel → Opinions
- **Icon:** 💬 Comment Dots

---

## ✨ Features

### 1. **Add Opinion**
- Upload photocard images
- Set display order (0, 1, 2...)
- Toggle active/inactive status
- Real-time image preview

### 2. **Edit Opinion**
- Update display order
- Replace image (optional)
- Change status
- Keeps existing image if not replaced

### 3. **Delete Opinion**
- Removes from database
- Deletes image file from server
- Confirmation prompt before deletion

### 4. **View All Opinions**
- Sorted by display order
- Shows preview thumbnail
- Status badges (Active/Inactive)
- Creation date

---

## 🖼️ Image Upload Specifications

| Property | Value |
|----------|-------|
| **Accepted Formats** | JPG, JPEG, PNG, GIF, WebP |
| **Recommended Width** | 400px |
| **Auto-Generated Name** | opinion_timestamp_uniqueid.ext |
| **Storage Path** | `Admin/img/opinions/` |

---

## 📝 How to Add Opinion

1. **Login as Super Admin**
2. **Navigate to Admin Panel** → Opinions
3. **Fill the form:**
   - Upload photocard image (required)
   - Set display order (0 = first, 1 = second, etc.)
   - Check "Active" to show on website
4. **Click "Add Opinion"**
5. **Success!** Image will appear on frontend

---

## 🔄 How Display Order Works

```
Display Order 0 → Shows FIRST
Display Order 1 → Shows SECOND  
Display Order 2 → Shows THIRD
...and so on
```

**Example:**
```
Opinion A: order = 5
Opinion B: order = 2
Opinion C: order = 8

Result: B → A → C
```

---

## 📊 Database Integration

The system uses the `opinions` table:

```sql
Table: opinions
- id (Primary Key)
- image (VARCHAR 255)
- display_order (INT)
- status (TINYINT: 1=Active, 0=Inactive)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

**Frontend Query:**
```sql
SELECT * FROM opinions 
WHERE status = 1 
ORDER BY display_order DESC 
LIMIT 10
```

---

## 🎨 Frontend Integration

Opinions automatically display on the main website:
- **Location:** After Video Section
- **Display:** Manual slider with navigation arrows
- **Limit:** Last 10 active opinions
- **Responsive:** Fully mobile-optimized

---

## 🛠️ Technical Details

### File Upload Process:
1. Validates file type (jpg, jpeg, png, gif, webp)
2. Generates unique filename
3. Creates `/opinions/` directory if needed
4. Moves file to `Admin/img/opinions/`
5. Saves relative path to database

### Image Deletion:
1. When deleting opinion, system automatically:
   - Removes database record
   - Deletes physical file from server

### Security:
- ✅ Super admin access only
- ✅ Session validation
- ✅ SQL injection prevention (prepared statements)
- ✅ File type validation
- ✅ Unique filename generation

---

## 🔧 Troubleshooting

### Images Not Uploading?
1. Check folder permissions: `Admin/img/opinions/` should be writable (777)
2. Check PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

### Images Not Showing on Frontend?
1. Verify `status = 1` (Active)
2. Check database has records
3. Clear browser cache
4. Verify image path: `Admin/img/opinions/filename.jpg`

### Can't Access Page?
1. Ensure logged in as Super Admin
2. Check session variables
3. Verify role = 'super_admin' in database

---

## 📱 Responsive Design

The management page is fully responsive:
- **Desktop:** Full layout with grid
- **Tablet:** Adjusted spacing
- **Mobile:** Single column, scrollable table

---

## 🎯 Best Practices

1. **Image Quality:** Use high-quality photocard images
2. **Naming:** Keep display order sequential (0, 1, 2, 3...)
3. **Active Status:** Only activate opinions you want visible
4. **Regular Cleanup:** Delete unused/old opinions
5. **Order Management:** Update orders when adding new items

---

## 🚀 Quick Start

1. **Access:** Go to `yoursite.com/Admin/manage_opinions.php`
2. **Add:** Upload first opinion image
3. **Order:** Set display_order = 0
4. **Activate:** Check "Active" checkbox
5. **Save:** Click "Add Opinion"
6. **View:** Visit homepage to see it in slider!

---

## 📞 Support Notes

### Common Questions:

**Q: How many opinions can I add?**
A: Unlimited, but only last 10 active ones show on frontend.

**Q: Can I change order after adding?**
A: Yes! Edit the opinion and update display_order.

**Q: What happens to old images when I replace?**
A: System automatically deletes old image file.

**Q: Can reporters access this?**
A: No, only super admins can manage opinions.

---

**Version:** 1.0  
**Created:** January 2026  
**Compatible:** PHP 7.4+, MySQL 5.7+
