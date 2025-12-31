# GitHub Actions Workflows

Automated workflows for CI/CD, testing, and maintenance.

## Active Workflows

### 🧪 Testing & Quality

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| **tests.yml** | Push, PR | Run PHP & TypeScript tests across multiple versions |
| **code-coverage.yml** | Push, PR | Generate coverage reports, upload to Codecov |
| **lint.yml** | Push, PR | Check code style (PHP CS Fixer, ESLint, Markdown) |
| **static-analysis.yml** | Push, PR | Run PHPStan and Psalm |

### 🔒 Security

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| **security.yml** | Push, PR, Weekly | Audit dependencies for vulnerabilities |

### 🚀 Release & Deployment

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| **release.yml** | Tag push | Validate tag, run tests, create GitHub release |
| **publish-docs.yml** | Docs change | Deploy documentation (placeholder) |

### 🔧 Maintenance

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| **dependency-update.yml** | Weekly | Auto-update dependencies, create PRs |
| **stale.yml** | Daily | Close stale issues and PRs |
| **label-pr.yml** | PR open | Auto-label PRs based on changed files |
| **changelog-check.yml** | PR | Remind to update CHANGELOG.md |

---

## Workflow Details

### tests.yml

Runs comprehensive test suite:

**PHP Tests:**
- PHP 8.1, 8.2, 8.3
- Laravel 10.x, 11.x
- Matrix testing for compatibility

**TypeScript Tests:**
- Node 18, 20
- Type checking
- Build verification
- Unit tests

**Triggers:**
- Push to `main` or `develop`
- Pull requests to `main` or `develop`

### code-coverage.yml

Generates code coverage reports:
- Uses Xdebug for PHP coverage
- Uploads to Codecov
- Requires minimum 80% coverage
- Runs on every push to `main`

### lint.yml

Code style and formatting:
- **PHP**: PHP CS Fixer (PSR-12)
- **TypeScript**: ESLint
- **Markdown**: markdownlint

### static-analysis.yml

Static code analysis:
- **PHPStan**: Level 5 analysis
- **Psalm**: Type checking and bug detection
- Runs on push and PR

### security.yml

Security auditing:
- `composer audit` - Check for vulnerable PHP packages
- `npm audit` - Check for vulnerable Node packages
- Runs on push, PR, and weekly schedule

### release.yml

Automated release process:

1. **Validate**: Check tag format (vX.Y.Z)
2. **Verify**: Ensure CHANGELOG.md updated
3. **Test**: Run full test suite
4. **Release**: Create GitHub release with notes
5. **Notify**: Log release info

**Triggers:**
- Push tags matching `v*.*.*` (e.g., `v1.0.0`)

### dependency-update.yml

Automated dependency updates:
- Updates Composer dependencies weekly
- Updates NPM dependencies weekly
- Runs tests before creating PR
- Creates separate PRs for each ecosystem

### stale.yml

Issue and PR management:
- Marks inactive issues as stale after 30 days
- Closes stale issues after 7 days
- Marks inactive PRs as stale after 14 days
- Exempts pinned, security, and "help wanted" labels

### label-pr.yml

Auto-labeling pull requests:
- Labels based on modified files
- Uses `.github/labeler.yml` configuration
- Helps with PR organization

### changelog-check.yml

Changelog validation:
- Checks if CHANGELOG.md was updated
- Warns (doesn't fail) if not updated
- Encourages good practices

---

## Setting Up Workflows

### Required Secrets

Most workflows use default `GITHUB_TOKEN`. No additional secrets needed unless:

**For Codecov** (optional):
- Add `CODECOV_TOKEN` to repository secrets
- Get token from https://codecov.io

**For Auto-deploy** (future):
- May need additional deploy tokens

### Badge Configuration

Add to README.md:

```markdown
[![Tests](https://img.shields.io/github/actions/workflow/status/farsidev/inertia-content/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farsidev/inertia-content/actions/workflows/tests.yml)
[![Coverage](https://img.shields.io/codecov/c/github/farsidev/inertia-content?style=flat-square)](https://codecov.io/gh/farsidev/inertia-content)
```

---

## Disabling Workflows

To disable a workflow:

1. Add `if: false` to the job:
```yaml
jobs:
  test:
    if: false  # Disabled
    runs-on: ubuntu-latest
```

2. Or delete/rename the workflow file

---

## Monitoring

View workflow runs:
- https://github.com/farsidev/inertia-content/actions

Check specific workflow:
- Tests: `.../actions/workflows/tests.yml`
- Coverage: `.../actions/workflows/code-coverage.yml`
- Security: `.../actions/workflows/security.yml`

---

## Troubleshooting

### Tests Failing

Check the workflow run for details:
1. Click on the failed workflow
2. Expand the failed step
3. Review error messages
4. Fix and push again

### Coverage Not Uploading

Ensure Codecov token is set:
1. Go to repository settings
2. Secrets and variables → Actions
3. Add `CODECOV_TOKEN`

### Release Not Creating

Check that:
1. Tag format is correct (`v1.0.0`)
2. CHANGELOG.md has the version
3. Tests are passing
4. You have write permissions

---

## Adding New Workflows

1. Create `.github/workflows/your-workflow.yml`
2. Define triggers and jobs
3. Test with `act` locally (optional)
4. Push and verify on GitHub

---

**Total Workflows**: 11
**Coverage**: Comprehensive
**Status**: ✅ Production Ready

