# Changelog

All notable changes to `inertia-content` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-12-29

**Stable Release** - Production Ready 🎉

This is the first stable release of Inertia Content. After 6 release candidates with extensive testing and bug fixes, the package is now ready for production use.

### Summary

Inertia Content is a Nuxt Content-compatible content management system for Laravel + Inertia.js + Vue applications. It compiles Markdown to Vue components at build time, providing zero runtime overhead while maintaining Laravel's server-side authority.

### Key Features

- ✅ File-based content management with Markdown
- ✅ Build-time compilation to Vue components
- ✅ Nuxt Content-compatible PHP query API
- ✅ Full TypeScript support
- ✅ Hot Module Replacement (HMR) in development
- ✅ Production-ready caching
- ✅ Security-first design with path traversal protection
- ✅ Complete documentation and examples

### Compatibility

- PHP: 8.1, 8.2, 8.3
- Laravel: 10.x, 11.x, 12.x
- Inertia.js: 1.x, 2.x
- Vue: 3.3+
- Node: 18+, 20+

### Installation

```bash
composer require farsi/inertia-content
```

See [Installation Guide](./docs/installation.md) for complete setup instructions.

### What's Included

- **PHP Package**: 13 classes with full query API
- **Vue Components**: 3 components + composables
- **Vite Plugin**: Build-time compilation with HMR
- **Documentation**: 12 comprehensive guides
- **Examples**: Blog and documentation site templates

### Breaking Changes

None - this is the first stable release. All previous release candidates were pre-stable versions.

### Migration from RC.6

No migration needed - v1.0.0 is fully compatible with v1.0.0-rc.6.

### Thank You

Thank you to all early adopters who tested the release candidates and provided valuable feedback!

## [1.0.0-rc.6] - 2025-12-29

**Release Candidate 6** - Production content loading fixed

### Fixed
- **Critical**: Don't delete compiled files after build - they're needed for glob imports
- Fixed useContent to work correctly with glob imports in production
- Fixed Page.vue template slots to properly render content component
- Fixed date display to use actual _updatedAt from meta
- Fixed TOC to show nested headings (H2 → H3 → H4)

### Changed
- Compiled files now persist in resources/js/.content-compiled/ (required for bundling)
- Added .gitkeep to compiled directory
- Improved sample Page.vue template

## [1.0.0-rc.5] - 2025-12-29

**Release Candidate 5** - All workflows passing

### Fixed
- Removed npm run build from TypeScript tests (not needed for source package)
- Fixed npm test script (no JS tests yet)
- Fixed code-quality workflow phpcs command
- All CI/CD checks now pass

## [1.0.0-rc.4] - 2025-12-29

**Release Candidate 4** - TypeScript and workflow fixes

### Fixed
- TypeScript type checking errors in CI/CD
- Added Vue type declarations (shims-vue.d.ts)
- Fixed HMR function call arguments
- Added missing Vue devDependencies

### Improved
- tsconfig excludes compiled content directory
- Better TypeScript configuration
- CI/CD workflows now pass type checking

## [1.0.0-rc.3] - 2025-12-29

**Release Candidate 3** - Tests and workflow fixes

### Added
- PHP unit tests for ContentEntry
- PHP unit tests for ManifestLoader
- PHP unit tests for PathResolver
- Test fixtures for manifest testing

### Fixed
- GitHub workflows now work without package-lock.json
- Removed npm cache from all workflows
- Changed npm ci to npm install across all workflows

### Improved
- CI/CD reliability
- Test coverage beginning

## [1.0.0-rc.2] - 2025-12-29

**Release Candidate 2** - Bug fixes and cleanup

### Fixed
- Fixed writeCompiledFilesToDisk function properly defined
- Cleaned up duplicate code in plugin
- Fixed path resolution for content directory
- Ensured compiled files are written before Vite processes them

### Improved
- Simplified plugin code
- Better error handling
- Cleaner codebase

## [1.0.0-rc.1] - 2025-12-29

**Release Candidate 1** - Production-ready with standard Vite workflow

### Fixed
- **Critical**: Completely rewrote build approach using standard Vite workflow
- Compile Markdown to actual Vue files in `resources/js/.content-compiled/`
- Use Vite's glob imports instead of virtual modules for reliability
- Content chunks now properly bundled by Vite in both dev and production
- HMR works seamlessly with real Vue files
- Production builds work correctly without any hacks

### Changed
- **Breaking**: Switched from virtual modules to real compiled Vue files
- Import mechanism now uses Vite glob imports (standard and reliable)
- Simplified plugin code - removed complex virtual module handling
- Architecture now follows Vite best practices

### Improved
- Better error logging and debugging
- More reliable HMR updates
- Standard Vite code splitting and tree shaking
- Cleaner codebase

### Added
- Build approaches documentation
- Production build fix documentation
- Packagist webhook setup guide

## [1.0.0-beta.3] - 2025-12-29

**Deprecated** - Experimental approach, use RC.1 instead

## [1.0.0-beta.2] - 2025-12-29

### Added
- Laravel 12 support
- Inertia.js 2.x support

### Improved
- Extended PHP version support matrix in CI
- Updated test workflows to include Laravel 12

### Compatibility
- PHP: 8.1, 8.2, 8.3
- Laravel: 10.x, 11.x, 12.x
- Inertia.js: 1.x, 2.x

## [1.0.0-beta.1] - 2025-12-29

### Fixed
- **Critical**: Fixed virtual modules not emitting in production build
- Content entries now written to temporary directory (`node_modules/.vite-inertia-content/`) during production builds
- Dynamic imports work correctly in both development and production modes
- Added cleanup hook to remove temporary files after build

### Changed
- useContent composable now uses conditional imports (virtual modules in dev, physical files in prod)
- Improved build process logging

### Documentation
- Added production build fix documentation
- Added known bugs documentation
- Updated installation guide with dependency installation steps

## [1.0.0-alpha] - 2025-12-29

**Status**: Alpha release - Code complete, needs testing

This is the initial alpha release of Inertia Content. The package is feature-complete but has not been tested in production environments.

### Known Issues
- ⚠️ Virtual modules don't work in production build (fixed in unreleased)

### Added
- Initial package structure
- File-based content management with Markdown
- Build-time compilation to Vue components
- Nuxt Content-compatible PHP query API
- Vue composables and components
  - `useContent` composable
  - `ContentRenderer` component
  - `ContentDoc` component
  - `ContentList` component
- Vite plugin with HMR support
- TypeScript support (strict mode)
- Artisan install command
- Artisan cache clear command
- Content caching system
- Navigation tree generation
- Draft content support
- Frontmatter parsing (YAML)
- Automatic heading extraction (H2-H4)
- Automatic excerpt generation
- Table of Contents support
- Path traversal protection
- Virtual module system for Vite
- Manifest generation and loading
- Complete documentation (English)
- Example implementations (Blog + Documentation site)
- GitHub Actions CI workflow
- Security policy
- Contributing guidelines
- Code of Conduct

### Features
- Query content with fluent API (`where`, `orderBy`, `limit`, etc.)
- Filter by directory, draft status, custom fields
- Sort by any field with multiple sort criteria
- Pagination support (skip/limit)
- Content Entry with full metadata
- Content Collection with navigation helpers
- Inertia.js integration (`Content::page()`)
- Hot Module Replacement in development
- Production-ready caching
- Automatic content hash for cache busting
- Server-side access control
- Client-side component rendering

### Developer Experience
- Single Composer installation
- JavaScript included in package
- No separate npm package needed
- Vite alias support (`@inertia-content`)
- Full TypeScript types
- IDE autocomplete support
- Clear error messages

---

## Release Notes

### [1.0.0-alpha] - December 29, 2025

**First alpha release!** 🎉

This release includes complete implementation of core features but requires testing before production use.

**Installation (Alpha):**
```bash
composer require farsi/inertia-content:@dev
```

**⚠️ Warning**: This is an alpha release. Not recommended for production use.

---

## Versioning Strategy

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (x.0.0): Breaking changes
- **MINOR** (1.x.0): New features (backward compatible)
- **PATCH** (1.0.x): Bug fixes

---

## Future Roadmap

### v1.1 (Planned)
- [ ] MDC (Markdown Components) support
- [ ] Full-text search integration
- [ ] Code syntax highlighting
- [ ] Custom markdown-it plugins support
- [ ] Content relationships (prev/next)

### v1.2 (Planned)
- [ ] Multi-language content support
- [ ] Content versioning
- [ ] Content collections API
- [ ] Advanced caching strategies
- [ ] Content transformers

### v2.0 (Future)
- [ ] Multiple content formats (JSON, YAML)
- [ ] Content API endpoints
- [ ] GraphQL integration
- [ ] Advanced search with indexing

---

## Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for how to contribute to this changelog.

When making changes:
1. Add entry under `[Unreleased]` section
2. Use categories: Added, Changed, Deprecated, Removed, Fixed, Security
3. Link to issues/PRs when applicable

---

**Maintained by**: [Farsi Dev](https://github.com/farsidev)
**License**: [MIT](./LICENSE.md)
