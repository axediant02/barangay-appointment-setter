# Quick Deployment Checklist - Login Redirect Loop Fix

## Pre-Deployment (5 min)
```bash
☐ Back up current system: cp -r barangay-system barangay-system.backup
☐ Note current PHP version: php -v
☐ Note session.save_path: php -r "echo ini_get('session.save_path');"
```

## Files to Deploy (Copy to live server)
```
☐ config/session.php (NEW)
☐ public/index.php
☐ views/auth/login.php
☐ views/auth/register.php
☐ views/resident/dashboard.php
☐ views/admin/dashboard.php
☐ views/admin/manage-request.php
```

## Post-Deployment (15 min)
```bash
☐ Verify file permissions: chmod 644 config/session.php public/index.php
☐ Clear old sessions: find /tmp -name "sess_*" -delete
☐ Check error logs: tail -20 /var/log/php-fpm/error.log

# Verify session path is writable:
☐ php -r "echo is_writable(sys_get_temp_dir()) ? 'OK' : 'ERROR';"

# Test session creation:
☐ php -r "
    session_start(); 
    \$_SESSION['test'] = uniqid(); 
    echo 'Session ID: ' . session_id() . PHP_EOL;
"
```

## Testing (10 min)

### Test 1: Basic Login Flow
1. Open **private/incognito** browser window
2. Go to: `https://yourdomain.com/public/index.php?page=login`
3. Enter test credentials
4. ✅ Should redirect to dashboard (NOT login page repeatedly)
5. ✅ Dashboard should load completely
6. Close browser

### Test 2: Session Persistence
1. Open private window
2. Log in
3. Navigate to: `?page=my-requests`
4. ✅ Should stay logged in (not redirect to login)
5. Navigate back to dashboard
6. ✅ Should still be logged in

### Test 3: Admin Login
1. Log out
2. Log in with **admin** account
3. ✅ Should go to admin dashboard
4. ✅ Should NOT loop back to login

### Test 4: Logout
1. While logged in, click Logout
2. ✅ Should go to home page
3. Clicking back should NOT show logged-in content
4. Try to access dashboard directly: `?page=resident-dashboard`
5. ✅ Should redirect to login

## If You See Redirect Loop

**Immediate Action**:
```bash
# 1. Check if session files are being created
ls -la /tmp/barangay_sessions/
# Should show files after login attempt

# 2. If empty, session path is not writable
# Check current path:
php -i | grep "session.save_path"

# 3. Create writable path:
mkdir -p /var/www/upload/sessions
chmod 700 /var/www/upload/sessions

# 4. Manually set in config/session.php:
session_save_path('/var/www/upload/sessions');
```

**Clear Cache & Restart**:
```bash
# Remove all old session files
rm -rf /tmp/barangay_sessions/*

# Restart PHP (if using FPM):
sudo systemctl restart php-fpm

# Restart web server:
sudo systemctl restart apache2  # or nginx
```

## Signs of Success ✅

| Item | Expected |
|------|----------|
| Login redirects | To dashboard (once) |
| Session persists | ✅ Yes, across pages |
| Logout works | Redirects to home, clears session |
| Admin login | Goes to admin dashboard |
| Error logs | No session-related errors |
| Session files | Created in `/tmp/barangay_sessions/` |

## Signs of Failure ❌

| Item | Problem | Solution |
|------|---------|----------|
| Infinite redirect to login | Session not persisting | Check session.save_path writable |
| "Session initialization failed" error | Database issue | Verify users table exists |
| Headers already sent warning | Output before header | Check for whitespace before `<?php` |
| Blank page on dashboard | Database query error | Check error_log |

## Database Verification

```bash
# Connect to MySQL and verify structure:
mysql -u root -p barangay-appointment

# Check users table:
> DESC users;

# Should have columns: id, name, email, password, role
# Should have at least one test user:
> SELECT id, name, email, role FROM users LIMIT 1;
```

## Performance Check

```bash
# Monitor active sessions:
watch -n 5 'ls -1 /tmp/barangay_sessions/ | wc -l'

# View recent sessions:
ls -ltr /tmp/barangay_sessions/ | tail -10
```

## Rollback Command

If critical issues occur:
```bash
# One-line rollback:
rm -rf barangay-system && cp -r barangay-system.backup barangay-system && systemctl restart php-fpm
```

## Contact Points

For support, gather this info:
```
- Screenshot of error message
- Output of: cat /var/log/php-fpm/error.log (last 20 lines)
- Browser dev tools > Network tab (screenshot)
- Result of: curl -v https://yourdomain.com/public/index.php?page=login
```

---

**Estimated Total Time**: 30 minutes  
**Difficulty**: Easy  
**Risk Level**: Low (with backup)

**Last Updated**: February 12, 2026
