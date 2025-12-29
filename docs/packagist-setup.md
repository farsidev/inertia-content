# Packagist Setup Guide

Complete guide to publishing on Packagist with automatic updates.

## Step 1: Submit to Packagist

### First-Time Submission

1. **Go to Packagist**
   - Visit: https://packagist.org/packages/submit

2. **Enter Repository URL**
   ```
   https://github.com/farsidev/inertia-content
   ```

3. **Click "Check"**
   - Packagist will validate the repository
   - Checks for valid `composer.json`
   - Verifies package name is available

4. **Click "Submit"**
   - Package will be created
   - Initial version detected from tags

5. **Verify Package**
   - Visit: https://packagist.org/packages/farsi/inertia-content
   - Check versions list
   - Verify description and links

---

## Step 2: Setup GitHub Webhook (Auto-Updates)

### Why Webhook?

Without webhook, you must manually click "Update" on Packagist for each release.
With webhook, Packagist auto-updates when you push tags.

### Setup Instructions

#### On Packagist

1. **Go to your package page**
   ```
   https://packagist.org/packages/farsi/inertia-content
   ```

2. **Click "Settings" or "Edit"**
   - Find the webhook section
   - Copy the webhook URL

   **Webhook URL format:**
   ```
   https://packagist.org/api/github?username=USERNAME&apiToken=YOUR_TOKEN
   ```

3. **Get your API token**
   - Go to: https://packagist.org/profile/
   - Click "Show API Token"
   - Copy your token

#### On GitHub

1. **Go to repository settings**
   ```
   https://github.com/farsidev/inertia-content/settings/hooks
   ```

2. **Click "Add webhook"**

3. **Configure webhook:**
   - **Payload URL**:
     ```
     https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME&apiToken=YOUR_API_TOKEN
     ```

   - **Content type**: `application/json`

   - **Which events?**: Select "Just the push event"

   - **Active**: ✅ Check this

4. **Click "Add webhook"**

5. **Test webhook:**
   - After adding, click on the webhook
   - Click "Recent Deliveries"
   - Click "Redeliver" to test
   - Should see green checkmark ✅

---

## Step 3: Verify Auto-Update Works

### Test the Webhook

1. **Create a test tag:**
   ```bash
   git tag v1.0.0-test
   git push origin v1.0.0-test
   ```

2. **Check GitHub webhook:**
   - Go to: Settings → Webhooks
   - Click on Packagist webhook
   - Check "Recent Deliveries"
   - Should see new delivery with status 200

3. **Check Packagist:**
   - Visit: https://packagist.org/packages/farsi/inertia-content
   - Refresh page
   - New version should appear automatically

4. **Clean up test tag:**
   ```bash
   git tag -d v1.0.0-test
   git push origin :refs/tags/v1.0.0-test
   ```

---

## Packagist Configuration

### Package Settings

On https://packagist.org/packages/farsi/inertia-content/settings

**Recommended settings:**
- ✅ **Auto-update**: Enabled (via webhook)
- ✅ **Notify maintainers**: On package updates
- ✅ **Abandoned**: No
- ⚠️ **Security advisories**: Monitor

### Badge URLs

Add to README.md:

```markdown
[![Latest Version](https://img.shields.io/packagist/v/farsi/inertia-content.svg)](https://packagist.org/packages/farsi/inertia-content)
[![Total Downloads](https://img.shields.io/packagist/dt/farsi/inertia-content.svg)](https://packagist.org/packages/farsi/inertia-content)
[![License](https://img.shields.io/packagist/l/farsi/inertia-content.svg)](https://packagist.org/packages/farsi/inertia-content)
```

---

## Release Process (After Setup)

Once webhook is configured, releasing is simple:

```bash
# 1. Update CHANGELOG.md
# Add your changes under [Unreleased]

# 2. Commit changes
git add CHANGELOG.md
git commit -m "chore: prepare v1.0.0 release"

# 3. Create and push tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin main
git push origin v1.0.0

# 4. Packagist auto-updates! ✅
# Within 1-2 minutes, new version appears on Packagist
```

---

## Troubleshooting

### Webhook Not Triggering

**Check:**
1. Webhook URL is correct
2. API token is valid
3. Webhook is "Active"
4. GitHub can reach Packagist (no firewall)

**Test:**
```bash
# Manual trigger via curl
curl -X POST "https://packagist.org/api/github?username=USERNAME&apiToken=TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"repository":{"url":"https://github.com/farsidev/inertia-content"}}'
```

### Packagist Not Showing New Version

**Solutions:**
1. Wait 5-10 minutes (can take time)
2. Force update: Click "Update" button on Packagist
3. Check webhook delivery log on GitHub
4. Verify tag pushed: `git ls-remote --tags origin`

### Wrong Version Displayed

**Check:**
- Tag format is correct: `v1.0.0` (with 'v' prefix)
- Tag is pushed to GitHub
- composer.json version field (if exists) doesn't conflict

---

## Current Status

### v1.0.0-beta.1

**Tag**: ✅ Created and pushed
**GitHub**: ✅ Available
**Packagist**: ⏳ Pending submission

**To install (after Packagist submission):**
```bash
composer require farsi/inertia-content
```

**To install (before Packagist, from GitHub):**
```bash
composer config repositories.inertia-content vcs https://github.com/farsidev/inertia-content
composer require farsi/inertia-content:dev-main
```

---

## Automated Release Workflow

We have a GitHub Action that can help (already configured):

```yaml
# .github/workflows/release.yml
# Triggers on tag push (v*.*.*)
# - Validates tag
# - Runs tests
# - Creates GitHub release
# - Notifies Packagist (if webhook fails)
```

---

## Next Steps

1. **Submit to Packagist** (one-time)
   - Go to: https://packagist.org/packages/submit
   - Enter: `https://github.com/farsidev/inertia-content`
   - Submit

2. **Setup webhook** (one-time)
   - Follow steps above
   - Test with dummy tag

3. **Future releases** (automated)
   - Just push tags
   - Everything else is automatic!

---

**Quick Commands:**

```bash
# Check current tags
git tag -l

# Check remote
git ls-remote --tags origin

# Check latest commit
git log --oneline -1

# Force Packagist update (manual)
curl -X POST "https://packagist.org/api/update-package?username=USER&apiToken=TOKEN" \
  -d "repository={\"url\":\"https://github.com/farsidev/inertia-content\"}"
```

---

**Last Updated**: December 29, 2025
**Current Version**: v1.0.0-beta.1
