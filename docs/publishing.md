# Publishing Guide

Guide for maintainers on how to publish new versions.

## Pre-Release Checklist

### Code Quality
- [ ] All tests passing (`composer test && npm test`)
- [ ] No linter errors (`npm run typecheck`)
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version numbers bumped

### Testing
- [ ] Test in a real Laravel + Inertia app
- [ ] Test installation from scratch
- [ ] Verify HMR works
- [ ] Test production build

---

## Version Bumping

Update version in THREE places:

```bash
# 1. package.json
{
  "version": "1.0.0"  # ← Update this
}

# 2. composer.json (not needed - auto from git tag)

# 3. CHANGELOG.md
## [1.0.0] - 2025-12-29
```

---

## Publishing Process

### 1. Build JavaScript Package

```bash
# Clean previous builds
rm -rf dist/

# Build
npm run build

# Verify build
ls -la dist/
# Should see: index.js, index.d.ts, vite/plugin.js, etc.
```

### 2. Commit and Tag

```bash
# Commit all changes
git add .
git commit -m "Release v1.0.0"

# Create tag
git tag -a v1.0.0 -m "Release version 1.0.0"

# Push with tags
git push origin main
git push origin v1.0.0
```

### 3. Publish to NPM

```bash
# Login to NPM (first time only)
npm login

# Verify package contents before publishing
npm pack
# Creates: farsi-inertia-content-1.0.0.tgz
# Extract and verify contents

# Publish
npm publish

# Verify
npm info farsi-inertia-content
```

### 4. Publish to Packagist

Packagist auto-updates from GitHub tags (after initial setup).

**First time setup:**
1. Go to https://packagist.org/packages/submit
2. Enter: `https://github.com/farsidev/inertia-content`
3. Click "Check"
4. Click "Submit"
5. Set up GitHub webhook for auto-updates

**For subsequent releases:**
- Packagist automatically detects new tags
- No manual action needed

### 5. Create GitHub Release

1. Go to: https://github.com/farsidev/inertia-content/releases/new
2. Select tag: `v1.0.0`
3. Release title: `v1.0.0`
4. Description: Copy from CHANGELOG.md
5. Attach files (optional):
   - `farsi-inertia-content-1.0.0.tgz` (npm package)
6. Click "Publish release"

---

## Post-Release

### Verify Installation

Create a new test Laravel app:

```bash
# New Laravel app
laravel new test-app
cd test-app

# Install Inertia
composer require inertiajs/inertia-laravel
npm install @inertiajs/vue3

# Install our package
composer require farsi/inertia-content
npm install farsi-inertia-content

# Run installer
php artisan inertia-content:install

# Build
npm run build

# Verify files exist
ls public/build/inertia-content-manifest.json
```

### Announce

- [ ] Tweet about release
- [ ] Post in Laravel News
- [ ] Post in relevant Discord/Slack channels
- [ ] Update documentation site (if exists)

---

## Troubleshooting

### NPM publish fails

```bash
# Check if you're logged in
npm whoami

# Check package name is available
npm search farsi-inertia-content

# Verify package.json
cat package.json | jq '.name, .version'
```

### Packagist not updating

1. Check webhook: https://packagist.org/packages/farsi/inertia-content
2. Click "Force Update"
3. Check GitHub webhook deliveries

### Version mismatch

```bash
# Check versions
cat package.json | grep version
git describe --tags

# They should match!
```

---

## Release Checklist

**Before tagging:**
- [ ] Code complete and tested
- [ ] CHANGELOG.md updated
- [ ] package.json version bumped
- [ ] Documentation current
- [ ] Clean build successful

**After tagging:**
- [ ] NPM published
- [ ] Packagist updated
- [ ] GitHub release created
- [ ] Installation verified
- [ ] Announcement made

---

## Rolling Back

If you need to unpublish or rollback:

### NPM

```bash
# Unpublish specific version (within 72 hours)
npm unpublish farsi-inertia-content@1.0.0

# Deprecate instead (after 72 hours)
npm deprecate farsi-inertia-content@1.0.0 "Please use version X.X.X instead"
```

### Packagist

Packagist follows Git tags - delete the git tag:

```bash
# Delete local tag
git tag -d v1.0.0

# Delete remote tag
git push origin :refs/tags/v1.0.0

# Packagist will auto-update
```

### GitHub Release

1. Go to releases page
2. Click "Delete" on the release
3. This doesn't delete the git tag

---

## Version Strategy

We follow [Semantic Versioning](https://semver.org/):

- `1.0.0` → `1.0.1` - Bug fixes (patch)
- `1.0.0` → `1.1.0` - New features (minor)
- `1.0.0` → `2.0.0` - Breaking changes (major)

### Examples

**Patch (1.0.0 → 1.0.1)**
- Bug fixes
- Documentation updates
- Performance improvements

**Minor (1.0.0 → 1.1.0)**
- New features (backward compatible)
- New Vue components
- New query methods

**Major (1.0.0 → 2.0.0)**
- Breaking API changes
- PHP version requirement bump
- Laravel version requirement change

---

## Automation (Future)

Consider setting up GitHub Actions for:

```yaml
# .github/workflows/publish.yml
name: Publish
on:
  push:
    tags:
      - 'v*'
jobs:
  publish-npm:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
      - run: npm ci
      - run: npm run build
      - run: npm publish
        env:
          NODE_AUTH_TOKEN: ${{ secrets.NPM_TOKEN }}
```

