# Fix: Database Name Issue on Heroku

## The Problem

You're getting this error:
```
#1044 - Access denied for user 'idpcxulxcy46n4st'@'%' to database 'um_skills_clinic'
```

**Why?** On Heroku, the database name is NOT `um_skills_clinic`. Heroku/JawsDB creates its own database with a different name.

## Solution: Find Your Actual Database Name

### Step 1: Get Your Database URL

Run this command:
```bash
heroku config:get JAWSDB_URL
```

You'll get something like:
```
mysql://idpcxulxcy46n4st:password123@us-cdbr-east-05.cleardb.net:3306/heroku_abc123def456
```

### Step 2: Parse the Database Name

The database name is the part **after the last slash** `/`:

From: `mysql://user:pass@host:port/database_name`
- Database name: `heroku_abc123def456` (or similar)

**Your database name is NOT `um_skills_clinic`** - it's whatever comes after the `/` in your URL!

### Step 3: Use the Correct Database in phpMyAdmin

1. **In phpMyAdmin, select the correct database** from the left sidebar
   - Look for a database name like `heroku_xxxxx` or `idpcxulxcy46n4st_xxxxx`
   - This is your actual database name

2. **Or create the table directly:**
   - Click on your database (the one from the URL)
   - Go to "SQL" tab
   - Paste this SQL (without the database name):

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

3. **Click "Go"** to execute

## Alternative: Update config.php to Use Correct Database

The `config.php` should automatically use the correct database name from the URL. But let's verify:

1. Check your `config.php` - it should parse the database name from `JAWSDB_URL`
2. The database name is extracted automatically from the URL path

## Quick Check: What Database Are You Connected To?

In phpMyAdmin:
1. Look at the top - it shows which database you're currently using
2. Make sure it matches the database name from your `JAWSDB_URL`
3. If you see `um_skills_clinic` selected, that's wrong - select the Heroku database instead

## Still Having Issues?

### Option 1: Use the Setup Script (Easiest)

The `setup_database.php` script automatically uses the correct database:

1. Deploy it:
   ```bash
   git add setup_database.php database_heroku.sql
   git commit -m "Add setup script"
   git push heroku main
   ```

2. Visit: `https://your-app.herokuapp.com/setup_database.php`

3. Click "Setup Database" - it will use the correct database automatically

### Option 2: Manual SQL (Correct Way)

1. Get your database name:
   ```bash
   heroku config:get JAWSDB_URL
   ```
   Extract the database name (after the last `/`)

2. In phpMyAdmin:
   - Select the database with that name (from left sidebar)
   - Go to SQL tab
   - Run the CREATE TABLE statement (without any USE or CREATE DATABASE commands)

## Important Notes

- ✅ **DO NOT** try to create a database named `um_skills_clinic` on Heroku
- ✅ **DO** use the database name that Heroku provided (from JAWSDB_URL)
- ✅ The `config.php` automatically uses the correct database name from the URL
- ✅ You only need to create the `users` table, not the database

## Verify It Worked

After creating the table:
1. In phpMyAdmin, you should see the `users` table in your database
2. Test registration: Visit `https://your-app.herokuapp.com/register.php`
3. Test login: Visit `https://your-app.herokuapp.com/login.php`
