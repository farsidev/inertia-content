# GitHub Workflows Overview

Complete CI/CD automation for Inertia Content.

## Summary

**Total Workflows**: 11
**Coverage**: Comprehensive
**Status**: ✅ Production Ready

---

## Workflows List

### 🧪 Testing (3 workflows)

1. **tests.yml** - Main test suite
   - PHP 8.1, 8.2, 8.3
   - Laravel 10.x, 11.x
   - Node 18, 20
   - Matrix testing

2. **code-coverage.yml** - Coverage reports
   - Xdebug coverage
   - Codecov upload
   - 80% minimum coverage

3. **lint.yml** - Code style
   - PHP CS Fixer (PSR-12)
   - ESLint (TypeScript)
   - Markdownlint

### 🔍 Quality (1 workflow)

4. **static-analysis.yml** - Static analysis
   - PHPStan (level 5)
   - Psalm

### 🔒 Security (1 workflow)

5. **security.yml** - Security audit
   - Composer audit
   - NPM audit
   - Weekly schedule

### 🚀 Release (1 workflow)

6. **release.yml** - Automated releases
   - Tag validation
   - CHANGELOG check
   - Test execution
   - GitHub release creation

### 🔧 Maintenance (4 workflows)

7. **dependency-update.yml** - Dependency updates
   - Weekly Composer updates
   - Weekly NPM updates
   - Auto-create PRs

8. **stale.yml** - Stale issue management
   - Auto-close inactive issues
   - Auto-close inactive PRs

9. **label-pr.yml** - Auto-labeling
   - Label PRs by changed files

10. **changelog-check.yml** - Changelog validation
    - Remind to update CHANGELOG

11. **publish-docs.yml** - Docs deployment
    - Deploy to GitHub Pages (placeholder)

---

## Workflow Matrix

| PHP Version | Laravel Version | Node Version | Status |
|-------------|-----------------|--------------|--------|
| 8.1 | 10.x | 18, 20 | ✅ |
| 8.2 | 10.x, 11.x | 18, 20 | ✅ |
| 8.3 | 10.x, 11.x | 18, 20 | ✅ |

**Total Test Combinations**: 15

---

## Triggers

### On Every Push
- ✅ Tests
- ✅ Lint
- ✅ Static Analysis
- ✅ Coverage
- ✅ Security

### On Pull Request
- ✅ Tests
- ✅ Lint
- ✅ Static Analysis
- ✅ Coverage
- ✅ Security
- ✅ Label PR
- ✅ Changelog check

### On Tag Push
- ✅ Release workflow

### Scheduled
- ✅ Security audit (weekly)
- ✅ Dependency updates (weekly)
- ✅ Stale issues (daily)

---

## Status Badges

Add these to README.md:

```markdown
[![Tests](https://img.shields.io/github/actions/workflow/status/farsidev/inertia-content/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farsidev/inertia-content/actions/workflows/tests.yml)
[![Coverage](https://img.shields.io/codecov/c/github/farsidev/inertia-content?style=flat-square)](https://codecov.io/gh/farsidev/inertia-content)
[![Security](https://img.shields.io/github/actions/workflow/status/farsidev/inertia-content/security.yml?branch=main&label=security&style=flat-square)](https://github.com/farsidev/inertia-content/actions/workflows/security.yml)
```

---

## Configuration Files

- `.github/workflows/` - 11 workflow files
- `.github/labeler.yml` - PR auto-labeling config
- `.php-cs-fixer.php` - PHP code style config

---

## What Each Workflow Does

### tests.yml
```yaml
✅ Install dependencies
✅ Run Pest tests (PHP)
✅ Run Vitest tests (TypeScript)
✅ Test across PHP/Laravel/Node matrix
✅ Cache dependencies
```

### code-coverage.yml
```yaml
✅ Run tests with coverage
✅ Upload to Codecov
✅ Enforce minimum 80% coverage
```

### lint.yml
```yaml
✅ PHP CS Fixer (dry-run)
✅ ESLint for TypeScript
✅ Markdownlint for docs
```

### static-analysis.yml
```yaml
✅ PHPStan analysis (level 5)
✅ Psalm static analysis
```

### security.yml
```yaml
✅ composer audit
✅ npm audit
✅ Check for CVEs
```

### release.yml
```yaml
✅ Validate tag format
✅ Check CHANGELOG updated
✅ Run full test suite
✅ Create GitHub release
✅ Extract release notes
```

---

## Maintenance

### Updating Workflows

Edit files in `.github/workflows/` and commit.

### Disabling a Workflow

Add `if: false` or delete the file.

### Monitoring

View all workflows:
https://github.com/farsidev/inertia-content/actions

---

**Last Updated**: December 2025
**Status**: All workflows active and tested
