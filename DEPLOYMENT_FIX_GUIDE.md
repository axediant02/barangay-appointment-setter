# Login Redirect Loop - Root Cause Analysis & Fix Guide

## Problem Description

The resident dashboard page was looping infinitely after login on live deployment. The user would log in successfully, but instead of accessing the dashboard, they would be redirected back to the login page repeatedly.

---

## Root Causes Identified

### 1. **Session Persistence Issues** ⚠️ CRITICAL
On live deployment, PHP sessions may not persist properly between requests due to:
- **Non-writable session save path**: The default PHP session save path (`/tmp` or `C:\Windows\Temp`) may not be writable
- **Missing session directory**: No dedicated session storage directory
- **Server configuration mismatch**: Live server PHP settings differ from local development

**Impact**: Session variables (`$_SESSION['user_id']`, `$_SESSION['role']`) were set during login but lost on the next request (dashboard page load).

### 2. **Duplicate Session Checks**
Multiple files were independently checking session variables:
- `public/index.php` - Router checks auth
- `views/resident/dashboard.php` - Dashboard checks auth AGAIN
- `views/admin/dashboard.php` - Admin dashboard checks auth AGAIN

**Impact**: Redundant checks increased failure points. If any check failed due to session loss, the loop occurred.

### 3. **Inconsistent Session Variable Names**
- Login stored: `$_SESSION['name']`
- Dashboard expected: `$_SESSION['username']`

**Impact**: Mismatch could cause authentication checks to fail even if the session existed.

### 4. **Poor Error Handling**
- No verification that session data was actually saved
- No fallback mechanisms
- Silent failures instead of clear error messages

---

## Solutions Implemented

### ✅ Fix #1: Centralized Session Configuration
**File**: `config/session.php` (NEW)

**Changes**:
- Configure PHP session handling for production stability
- Ensure session save path is writable and absolute
- Add fallback to default temp directory if path creation fails
- Implement helper functions for consistent session management
- Add error detection and reporting

**Key Functions Added**:
```php
isLoggedIn()          // Check if user is logged in
hasRole($role)        // Check if user has specific role
requireLogin()        // Guard function for protected pages
setSessionData()      // Safely set session after login with validation
logout()              // Properly destroy session and cookies
```

### ✅ Fix #2: Updated Entry Point
**File**: `public/index.php`

**Changes**:
- Use centralized session config instead of inline `session_start()`
- Use helper functions for cleaner, more maintainable code
- Use helper functions instead of direct $_SESSION access

**Before**:
```php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
```

**After**:
```php
require_once '../config/session.php';
$isLoggedIn = isLoggedIn();
$role = getCurrentRole();
```

### ✅ Fix #3: Removed Redundant Session Checks
**Files**:
- `views/resident/dashboard.php`
- `views/admin/dashboard.php`
- `views/admin/manage-request.php`

**Changes**:
- Removed duplicate `if (!isset($_SESSION['user_id']))` checks
- Kept only safety checks in views
- Rely on router (index.php) for primary authentication

### ✅ Fix #4: Fixed Session Variable Consistency
**Files**:
- `views/auth/login.php`
- `views/auth/register.php`

**Changes**:
- Use `setSessionData()` helper function
- Ensure `$_SESSION['username']` is always set (not `name`)
- Validate session was actually saved before redirecting
- Add HTTP status codes to headers for clarity

**Before**:
```php
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['name'];  // WRONG: views expect 'username'
header("Location: ?page=admin-dashboard");
```

**After**:
```php
if (setSessionData($user)) {
    header("Location: ?page=admin-dashboard", true, 302);  // 302 = temporary redirect
    exit;
} else {
    $errors[] = "Session initialization failed. Please try again.";
}
```

### ✅ Fix #5: Improved Logout Handling
**File**: `public/index.php`

**Changes**:
- Use centralized `logout()` function
- Properly destroy session cookies
- Clear all session data safely

---

## Deployment Steps

### Step 1: Backup Current System
```bash
# On your live server
cp -r /var/www/html/barangay-system /var/www/html/barangay-system.backup

# Or on Windows
xcopy C:\inetpub\wwwroot\barangay-system C:\inetpub\wwwroot\barangay-system.backup /S /I
```

### Step 2: Deploy Updated Files
```bash
# Upload these files to your live server:
config/session.php           # NEW - Session configuration
public/index.php             # UPDATED - Use session config
views/auth/login.php         # UPDATED - Use setSessionData()
views/auth/register.php      # UPDATED - Use setSessionData()
views/resident/dashboard.php # UPDATED - Removed duplicate check
views/admin/dashboard.php    # UPDATED - Removed duplicate check
views/admin/manage-request.php # UPDATED - Removed duplicate check
```

### Step 3: Verify File Permissions
```bash
# Ensure web server can read config files
chmod 644 /path/to/config/session.php
chmod 644 /path/to/public/index.php

# Ensure web server can create temp directories if needed
chmod 755 /path/to/config/
```

### Step 4: Check PHP Configuration
```bash
# SSH into your server and check PHP settings:
php -r "echo ini_get('session.save_path') . PHP_EOL;"

# Verify the path is writable:
php -r "echo is_writable(sys_get_temp_dir()) ? 'Writable' : 'Not writable';"
```

### Step 5: Clear Browser Cache
```bash
# Instruct users to:
# - Clear browser cookies (especially session cookies)
# - Clear browser cache
# - Close and reopen browser
# Or provide them with private/incognito window link
```

### Step 6: Test the Fix
```
1. Open a private/incognito window
2. Navigate to: https://your-domain.com/public/index.php?page=login
3. Log in with test credentials
4. Verify you reach the dashboard WITHOUT redirect loop
5. Check that session persists across page navigation
6. Test logout functionality
7. Test with different user roles (admin, resident)
```

### Step 7: Monitor Logs
```bash
# Check PHP error logs for session-related issues
tail -f /var/log/php-fpm/error.log

# Check web server access logs
tail -f /var/log/apache2/access.log  # Apache
# or
tail -f /var/log/nginx/access.log    # Nginx

# Look for patterns like:
# - Multiple redirects to same login page
# - 302 status codes in sequence
# - Session file write errors
```

---

## Troubleshooting

### Issue: Still Getting Redirect Loop After Deployment

**Solution 1: Clear Session Files**
```bash
# SSH to server and remove old session files
find /tmp -name "sess_*" -delete

# Or the custom session path:
rm -rf /tmp/barangay_sessions/*
```

**Solution 2: Check Session Permissions**
```bash
# Ensure session save path is writable
php -r "
\$path = sys_get_temp_dir() . '/barangay_sessions';
@mkdir(\$path, 0700, true);
echo is_writable(\$path) ? 'OK - Writable' : 'ERROR - Not writable';
"
```

**Solution 3: Disable Session Autostart**
```bash
# In php.ini, make sure:
session.auto_start = 0
session.use_cookies = 1
session.use_only_cookies = 1
```

**Solution 4: Check Database Connection**
```bash
# Verify users table has correct column names:
# Should have: id, name, email, password, role

# Test with:
mysql -u root -p barangay-appointment -e "DESC users;"
```

**Solution 5: Enable Debug Mode**
```php
// Add to public/index.php temporarily:
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../debug.log');
```

### Issue: Session Data Lost After Single Page Load

**Cause**: Session save path not writable or session files not being created

**Solution**:
```bash
# Check if session files are being created:
ls -la /tmp/barangay_sessions/

# If empty, the path isn't writable. Solution:
# Edit config/session.php and set a custom writable path:
session_save_path('/var/www/html/barangay-system/storage/sessions');

# Create directory with proper permissions:
mkdir -p /var/www/html/barangay-system/storage/sessions
chmod 700 /var/www/html/barangay-system/storage/sessions
chown www-data:www-data /var/www/html/barangay-system/storage/sessions  # For Apache/Nginx
```

---

## Performance & Security Considerations

### Session Security Enhancements
The updated code includes:
- ✅ `session.use_only_cookies` - Prevent session fixation attacks
- ✅ `session.use_strict_mode` - Enforce strict session mode
- ✅ `session.cookie_httponly` - Prevent JavaScript access
- ✅ `session.cookie_samesite` - CSRF protection
- ✅ Automatic session garbage collection (24-hour timeout)

### Monitoring Session Health
Add this monitoring script:

```php
// File: admin/check-sessions.php
<?php
require_once '../config/session.php';
require_once '../config/database.php';

requireAdmin();  // Must be admin

$sessionPath = sys_get_temp_dir() . '/barangay_sessions';
$sessionFiles = glob($sessionPath . '/sess_*');

echo "Total active sessions: " . count($sessionFiles) . "\n";
echo "Session path: " . $sessionPath . "\n";
echo "Path writable: " . (is_writable($sessionPath) ? "YES" : "NO") . "\n";

// Show oldest sessions (might be stale):
usort($sessionFiles, function($a, $b) {
    return filemtime($a) - filemtime($b);
});

echo "\nOldest sessions:\n";
foreach (array_slice($sessionFiles, 0, 5) as $file) {
    echo $file . " - " . date('Y-m-d H:i:s', filemtime($file)) . "\n";
}
```

---

## Rollback Plan

If you encounter critical issues after deployment:

```bash
# Restore from backup:
rm -rf /var/www/html/barangay-system
cp -r /var/www/html/barangay-system.backup /var/www/html/barangay-system

# Clear sessions to force fresh start:
find /tmp -name "sess_*" -delete
```

---

## Summary of Changes

| File | Change | Impact |
|------|--------|--------|
| `config/session.php` | NEW | Centralized session management |
| `public/index.php` | UPDATED | Uses session config, cleaner auth checks |
| `views/auth/login.php` | UPDATED | Uses setSessionData() with validation |
| `views/auth/register.php` | UPDATED | Uses setSessionData() with validation |
| `views/resident/dashboard.php` | UPDATED | Removed duplicate session check |
| `views/admin/dashboard.php` | UPDATED | Removed duplicate session check |
| `views/admin/manage-request.php` | UPDATED | Removed duplicate session check |

---

## Testing Checklist

- [ ] Deploy all updated files
- [ ] Clear browser cookies and cache
- [ ] Test login as resident
- [ ] Test login as admin  
- [ ] Verify dashboard loads without redirect
- [ ] Verify session persists on page changes
- [ ] Test logout functionality
- [ ] Test register and immediate login
- [ ] Check error logs for warnings
- [ ] Monitor session file creation
- [ ] Verify session timeout (24 hours)
- [ ] Test with multiple browsers simultaneously

---

## Questions or Issues?

If you still experience problems:

1. **Check session file creation**:
   ```bash
   ls -la /tmp/barangay_sessions/
   ```

2. **Test session functionality**:
   ```bash
   php -r "session_start(); \$_SESSION['test'] = 'ok'; echo \$_SESSION['test'];"
   ```

3. **Review PHP error log**:
   ```bash
   tail -50 /var/log/php-fpm/error.log
   ```

4. **Verify database connectivity**:
   ```php
   // Test in a simple PHP file
   require_once 'config/database.php';
   echo $pdo ? 'Connected' : 'Error';
   ```

---

**Last Updated**: February 12, 2026  
**Version**: 1.0  
**Status**: Ready for Production Deployment
