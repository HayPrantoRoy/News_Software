# Dynamic Video Section Setup Guide

## Step 1: Create Database Table

Run the SQL file to create the `news_video` table:

```sql
-- Execute this in your phpMyAdmin or MySQL client
-- File: database/create_news_video_table.sql
```

Or manually run this SQL:

```sql
CREATE TABLE IF NOT EXISTS `news_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `youtube_link` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Step 2: Access Admin Panel

Go to: `http://localhost/HindusNews/Admin/manage_videos.php`

## Step 3: Add Videos

1. Click **"Add New Video"** button
2. Fill in the form:
   - **Title**: Video title (required)
   - **Subtitle**: Short description/excerpt (optional)
   - **Thumbnail**: Upload an image (will be stored in Admin/img/)
   - **YouTube Link**: Full YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)
   - **Active**: Check to show on website
3. Click **"Add Video"**

## Step 4: Manage Videos

- **Edit**: Click edit button to modify video details
- **Delete**: Click delete button to remove video
- **Status**: Toggle active/inactive to show/hide videos

## Supported YouTube Link Formats

- `https://www.youtube.com/watch?v=VIDEO_ID`
- `https://youtu.be/VIDEO_ID`
- `https://www.youtube.com/embed/VIDEO_ID`

## Features

✅ Latest 8 active videos displayed on homepage
✅ Automatic YouTube ID extraction
✅ Thumbnail upload support
✅ Active/Inactive status control
✅ No extra columns - only essential fields
✅ Sorted by creation date (newest first)

## File Changes

1. **Database**: `database/create_news_video_table.sql` - Table structure
2. **Admin Panel**: `Admin/manage_videos.php` - Video management interface
3. **Frontend**: `index.php` (lines 2239-2280) - Dynamic video display

## Notes

- Videos will show in the existing video section on the homepage
- If no videos exist, shows "কোন ভিডিও পাওয়া যায়নি" message
- Thumbnails stored in `Admin/img/` folder
- Only active videos (`is_active = 1`) are displayed
