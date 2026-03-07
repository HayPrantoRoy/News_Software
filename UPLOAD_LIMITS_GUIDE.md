# Upload Limits & Display Order Implementation Guide

## 📋 Overview
This guide documents the upload limits and display order features added to the admin panel.

---

## ✅ Features Implemented

### 1. **Opinions Section (Maximum 10 Images)**
- ✨ Upload limit: **10 opinions maximum**
- 🚫 Prevents adding more than 10 opinion images
- 📊 Shows current count (e.g., "7/10")
- ⚠️ Error message when limit is reached

### 2. **Videos Section (Maximum 8 Videos)**
- ✨ Upload limit: **8 videos maximum**
- 🚫 Prevents adding more than 8 videos
- 📊 Shows current count (e.g., "5/8")
- ⚠️ Error message when limit is reached

### 3. **Display Order for Videos**
- 🔢 New "Display Order" field added
- 📊 Shows in table listing
- ⬆️ Lower numbers appear first (0, 1, 2...)
- 💾 Database column added

---

## 🗄️ Database Changes

### SQL Script Location:
📁 **`database/add_video_display_order.sql`**

### What it does:
1. Adds `display_order` column to `news_video` table
2. Creates index for better performance
3. Updates existing records with sequential order

### How to run:
```sql
-- Run this script in your MySQL database
mysql -u your_username -p your_database < database/add_video_display_order.sql
```

Or via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to SQL tab
4. Copy and paste the content from `add_video_display_order.sql`
5. Click "Go"

---

## 📁 Modified Files

### 1. **manage_opinions.php**
**Changes:**
- Added 10 image upload limit check
- Added counter display (X/10)
- Prevents adding when limit reached
- Shows error message: "Maximum limit reached! You can only have 10 opinions. Please delete an existing one before adding new."

**Code Location:**
```php
// Line ~61-68
if ($total_opinions >= 10) {
    $_SESSION['error'] = "Maximum limit reached!";
}
```

### 2. **manage_videos.php**
**Changes:**
- Added `display_order` field to form
- Added 8 video upload limit check
- Added counter display (X/8)
- Updated table to show Display Order column
- Modified INSERT/UPDATE queries to include display_order
- Updated edit JavaScript to populate display_order
- Changed sort order: `ORDER BY display_order ASC, created_at DESC`

**Form Field:**
```html
<input type="number" name="display_order" id="displayOrder" required>
```

**Limit Check:**
```php
// Line ~47-54
if ($total_videos >= 8) {
    $_SESSION['error'] = "Maximum limit reached!";
}
```

### 3. **add_video_display_order.sql** (NEW)
**Location:** `database/add_video_display_order.sql`

**Purpose:** Adds display_order column to news_video table

---

## 🎯 How It Works

### Upload Limit Logic:

**Opinions (10 max):**
1. When user clicks "Add Opinion"
2. System counts total opinions in database
3. If count >= 10: Show error, prevent insert
4. If count < 10: Allow upload

**Videos (8 max):**
1. When user clicks "Add Video"
2. System counts total videos in database
3. If count >= 8: Show error, prevent insert
4. If count < 8: Allow upload

### Display Order Logic:

**Videos:**
- Display Order = 0 → Shows FIRST
- Display Order = 1 → Shows SECOND
- Display Order = 2 → Shows THIRD
- Higher numbers appear later

**Example:**
```
Video A: order = 5
Video B: order = 2
Video C: order = 8

Result: B → A → C
```

---

## 🎨 UI Changes

### Opinions Page:
```
┌─────────────────────────────────────┐
│ ℹ️ Note: Upload photocard images   │
│ ⚠️ Limit: Maximum 10 opinions      │
│    Current: 7/10                    │
└─────────────────────────────────────┘
```

### Videos Page:
```
┌─────────────────────────────────────┐
│ ℹ️ Note: Videos displayed by order │
│ ⚠️ Limit: Maximum 8 videos         │
│    Current: 5/8                     │
└─────────────────────────────────────┘

Form Fields:
- Title
- YouTube Link
- Display Order ⭐ NEW
- Subtitle
- Thumbnail Image
- Active checkbox
```

### Table Changes (Videos):
| Thumbnail | Title | Subtitle | Link | **Display Order** ⭐ | Status | Created | Actions |

---

## ⚠️ Error Messages

### When Limit Reached:

**Opinions (10):**
> ❌ Maximum limit reached! You can only have 10 opinions. Please delete an existing one before adding new.

**Videos (8):**
> ❌ Maximum limit reached! You can only have 8 videos. Please delete an existing one before adding new.

---

## 🔧 Testing Steps

### Test Opinions Limit:
1. Go to Admin → Opinions
2. Add opinions until you have 10
3. Try to add 11th opinion
4. Should see error message
5. Delete one opinion
6. Should be able to add again

### Test Videos Limit:
1. Go to Admin → Videos
2. Add videos until you have 8
3. Try to add 9th video
4. Should see error message
5. Delete one video
6. Should be able to add again

### Test Display Order:
1. Add 3 videos with orders: 5, 2, 8
2. Check table - should show in order: 2, 5, 8
3. Edit video and change order
4. Table should re-sort automatically

---

## 💡 Tips

### Best Practices:
1. **Sequential Orders:** Use 0, 1, 2, 3... for easy management
2. **Leave Gaps:** Use 10, 20, 30... to easily insert items later
3. **Regular Cleanup:** Delete unused videos/opinions to stay within limit
4. **Test Before Delete:** Preview items before deleting

### Changing Display Order:
1. Click "Edit" on the item
2. Change "Display Order" number
3. Click "Update"
4. Table automatically re-sorts

---

## 🚀 Frontend Impact

### Videos:
- Frontend automatically fetches by display_order
- SQL: `ORDER BY display_order ASC`
- No frontend changes needed

### Opinions:
- Already sorted by display_order
- Limit ensures only 10 show on frontend
- Slider works with any number up to 10

---

## 📞 Quick Reference

| Feature | Limit | Table | Column | Sort Order |
|---------|-------|-------|--------|------------|
| Opinions | 10 max | `opinions` | `display_order` | ASC |
| Videos | 8 max | `news_video` | `display_order` | ASC |

---

## ✅ Checklist

Before going live:
- [ ] Run SQL script to add display_order column
- [ ] Test adding 10th opinion (should work)
- [ ] Test adding 11th opinion (should fail)
- [ ] Test adding 8th video (should work)
- [ ] Test adding 9th video (should fail)
- [ ] Test display order sorting
- [ ] Test editing existing items
- [ ] Verify frontend displays correctly

---

**Version:** 1.0  
**Created:** January 2026  
**Status:** ✅ Implemented and Ready
