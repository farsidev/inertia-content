# Packagist Webhook Setup - Step by Step

Complete visual guide to setup auto-updates from GitHub to Packagist.

## 🎯 Goal

Setup webhook so Packagist automatically updates when you push new tags to GitHub.

---

## Step 1: Submit Package to Packagist (One-Time)

### 1.1 Go to Packagist Submit Page

```
https://packagist.org/packages/submit
```

### 1.2 Enter Repository URL

```
https://github.com/farsidev/inertia-content
```

### 1.3 Click "Check"

Packagist will validate:
- ✅ Repository exists
- ✅ composer.json is valid
- ✅ Package name available
- ✅ Git tags present

### 1.4 Click "Submit"

Your package will be created at:
```
https://packagist.org/packages/farsi/inertia-content
```

**Initial version**: Detected from your latest git tag (v1.0.0-beta.1)

---

## Step 2: Get Packagist API Token

### 2.1 Login to Packagist

Go to: https://packagist.org/login

### 2.2 Go to Your Profile

Click your username → "Profile" or visit:
```
https://packagist.org/profile/
```

### 2.3 Show API Token

Scroll to "API Token" section and click **"Show API Token"**

**Copy this token** - you'll need it next!

```
Example: a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

⚠️ **Keep this secret!** Don't commit it to git.

---

## Step 3: Setup GitHub Webhook

### 3.1 Go to Repository Settings

```
https://github.com/farsidev/inertia-content/settings/hooks
```

Or: Repository → Settings → Webhooks

### 3.2 Click "Add webhook"

### 3.3 Configure Webhook

**Payload URL:**
```
https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME&apiToken=YOUR_API_TOKEN
```

Replace:
- `YOUR_PACKAGIST_USERNAME` - Your Packagist username
- `YOUR_API_TOKEN` - Token from Step 2

**Example:**
```
https://packagist.org/api/github?username=farsidev&apiToken=a1b2c3d4e5f6g7h8i9j0
```

**Content type:**
```
application/json
```

**Secret:** (leave empty)

**SSL verification:**
```
✅ Enable SSL verification
```

**Which events would you like to trigger this webhook?**
```
● Just the push event
```

**Active:**
```
✅ Active
```

### 3.4 Click "Add webhook"

---

## Step 4: Test the Webhook

### 4.1 Check Webhook Status

After adding webhook:

1. Click on the webhook you just created
2. Scroll to "Recent Deliveries"
3. You should see a "ping" event with ✅ green checkmark

### 4.2 Trigger Real Update

Push a dummy tag to test:

```bash
git tag v1.0.0-test
git push origin v1.0.0-test
```

### 4.3 Verify Webhook Delivery

1. Go to: https://github.com/farsidev/inertia-content/settings/hooks
2. Click on Packagist webhook
3. Check "Recent Deliveries"
4. Latest delivery should show:
   - ✅ Status: 200
   - ✅ Response body (from Packagist)

### 4.4 Check Packagist Updated

Visit: https://packagist.org/packages/farsi/inertia-content

- New version `v1.0.0-test` should appear
- Updated timestamp should be recent

### 4.5 Clean Up Test Tag

```bash
git tag -d v1.0.0-test
git push origin :refs/tags/v1.0.0-test
```

On Packagist, click "Update" to refresh and remove test version.

---

## Step 5: Verify Everything Works

### Current Tags on GitHub

```bash
git tag -l
```

**Output:**
```
v1.0.0-alpha
v1.0.0-beta.1
```

### On Packagist

Visit: https://packagist.org/packages/farsi/inertia-content

**Should show:**
- Latest version: v1.0.0-beta.1
- Versions list with both tags
- Auto-update: ✅ Enabled

---

## Future Releases (Automated!)

Now that webhook is setup, future releases are simple:

```bash
# 1. Make changes and commit
git add .
git commit -m "feat: new feature"

# 2. Update CHANGELOG.md
# Document your changes

# 3. Create and push tag
git tag -a v1.0.1 -m "Release v1.0.1"
git push origin main
git push origin v1.0.1

# 4. Wait 1-2 minutes
# ✅ Packagist auto-updates via webhook!
# ✅ New version available on Packagist
# ✅ Users can: composer require farsi/inertia-content
```

---

## Webhook URL Format

```
https://packagist.org/api/github?username={PACKAGIST_USERNAME}&apiToken={YOUR_API_TOKEN}
```

**Fields:**
- `username` - Your Packagist username (NOT GitHub username)
- `apiToken` - Your Packagist API token (from profile page)

**Example:**
```
https://packagist.org/api/github?username=farsidev&apiToken=abc123def456ghi789
```

---

## Security Notes

### ⚠️ Protect Your API Token

- Don't commit token to git
- Don't share publicly
- Regenerate if exposed
- Use GitHub secrets for automation

### Webhook IP Whitelist

Packagist uses these IPs (if you have firewall):
```
185.199.108.0/22
185.199.109.0/22
```

---

## Troubleshooting Reference

| Problem | Solution |
|---------|----------|
| Webhook fails | Check URL format, verify token |
| Version not updating | Wait 5 mins, or click "Update" manually |
| Wrong version shown | Check tag format (v1.0.0) |
| 401 Unauthorized | API token invalid, regenerate |
| 404 Not Found | Package URL wrong, check submission |

---

## Quick Links

- **Submit Package**: https://packagist.org/packages/submit
- **Your Profile** (API token): https://packagist.org/profile/
- **Package Page**: https://packagist.org/packages/farsi/inertia-content
- **GitHub Webhooks**: https://github.com/farsidev/inertia-content/settings/hooks

---

## Summary

```
✅ Package on GitHub
✅ Tag v1.0.0-beta.1 pushed
📋 Next: Submit to Packagist
📋 Next: Setup webhook
🎉 Then: Automated forever!
```

**After setup, releasing = just push tags!** 🚀
