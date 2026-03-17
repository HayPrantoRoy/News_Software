# Multi-Tenant News Software System

## Overview
This system allows multiple news portals to run on a single codebase, each with their own separate database.

## Setup Instructions

### Step 1: Create Master Database
Import the master database by running the SQL file in phpMyAdmin:
```
master_news_software_db.sql
```

Or run this command:
```bash
mysql -u root -p < master_news_software_db.sql
```

### Step 2: Directory Structure
Ensure these directories exist and are writable:
- `uploads/logos/` - For portal logo images

### Step 3: Access Points

#### Registration
New users can register at:
```
http://localhost/News_Software/register.php
```

#### Login
Registered users can login at:
```
http://localhost/News_Software/login.php
```

#### Accessing a Portal
Each portal is accessed using the `user_id` parameter:
```
http://localhost/News_Software/index.php?user_id=1
http://localhost/News_Software/index.php?user_id=2
http://localhost/News_Software/index.php?user_id=3
```

## How It Works

### Registration Process
1. User fills out the registration form with:
   - Account credentials (username, email, password)
   - Portal information (name, logo, description)
   - Contact information
   - Social media links

2. System creates:
   - A record in `master_news_software_db.users` table
   - A new database named `news_db_{user_id}_{username}`
   - All required tables in the new database
   - Basic info populated from registration form

### Dynamic Database Connection
- When `user_id` parameter is provided in URL, the system:
  1. Looks up the user in master database
  2. Gets their database name
  3. Connects to their specific database
  4. All queries run against their database

### Files Modified for Multi-Tenancy
- `connection.php` - Dynamic database selection
- `Admin/connection.php` - Dynamic database selection for admin
- `index.php` - User ID handling and link generation
- `category.php` - User ID handling and link generation
- `news.php` - User ID handling and link generation

### New Files Created
- `master_news_software_db.sql` - Master database schema
- `master_connection.php` - Master database connection
- `news_software_tables.sql` - Template tables for new databases
- `register.php` - Registration form
- `login.php` - Login page
- `logout.php` - Logout handler

## Database Structure

### Master Database (`master_news_software_db`)
- `users` - Stores all tenant information
- `subscription_plans` - Available plans
- `user_subscriptions` - User subscription tracking

### Tenant Databases (`news_db_{id}_{username}`)
Each tenant gets their own database with:
- `basic_info` - Portal settings
- `category` - News categories
- `news` - News articles
- `reporter` - Reporters
- `videos` - Video content
- `podcasts` - Podcast content
- `opinions` - Opinion pieces
- `quizzes` - Quiz content
- `menus` - Admin menu items
- `roles` - User roles
- `permissions` - Role permissions
- `admin_users` - Admin accounts
- `earnings` - Earning records
- `payments` - Payment records
- `settings` - Portal settings

## URL Parameters
- `user_id` - Required to identify which portal to display
- All internal links automatically include the `user_id` parameter

## Security Notes
- Passwords are hashed using `password_hash()`
- Database names are sanitized to prevent injection
- Each tenant's data is completely isolated
